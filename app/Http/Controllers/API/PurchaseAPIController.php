<?php

namespace App\Http\Controllers\API;

use App\Exports\PurchasesWeekly;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePruchaseRequest;
use App\Http\Requests\UpdatePruchaseRequest;
use App\Mail\PurchaseWeekly;
use App\Models\Expense;
use App\Models\PaymentPurchase;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Unit;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $purchases = Purchase::with('facture', 'provider', 'warehouse')
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', $request->search . "%")
                        ->orWhere('statut', 'LIKE', $request->search . "%")
                        ->orWhere('GrandTotal', $request->search)
                        ->orWhere('payment_statut', 'like', "$request->search")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('provider', function ($q) use ($request) {
                                $q->where('name', 'LIKE', $request->search . "%");
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('warehouse', function ($q) use ($request) {
                                $q->where('name', 'LIKE', $request->search . "%");
                            });
                        });
                });
            })->paginate($request->limit ?? 10);
        return $this->sendResponse($purchases, 'Purchase retrieved successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($request->purchases)) {

            $insert = [];
            try {
                DB::beginTransaction();
                collect($request->purchases)->each(function ($item) use (&$insert) {
                    
                    $purchase = Purchase::where('Ref', $item['Ref'])->where('date', $item['date'])->first();
                    if (empty($purchase)) {
                        $input['user_id'] = $item['user_id'];
                        $input['Ref'] = !empty($item['Ref']) ? $item['Ref'] : $this->getNumberOrder();
                        $input['date'] = $item['date'];
                        $input['provider_id'] = $item['provider_id'];
                        $input['warehouse_id'] = $item['warehouse_id'];
                        $input['tax_rate'] = $item['tax_rate'];
                        $input['TaxNet'] = $item['TaxNet'];
                        $input['discount'] = $item['discount'];
                        $input['shipping'] = $item['shipping'];
                        $input['GrandTotal'] = $item['GrandTotal'];
                        $input['paid_amount'] = $item['payment_one'];
                        $input['statut'] = $item['statut'] ?? 'completed';
                        $input['payment_statut'] = $item['payment_statut'] ?? 'paid';
                        $input['notes'] = $item['notes'] ?? null;
                        $input['created_at'] = Carbon::now();
                        $input['updated_at'] = Carbon::now();

                        $purchase = Purchase::create($input);
                        $insert = [];
                        collect($item['details'])->each(function ($row) use (&$insert, $purchase) {
                            $insert[] = [
                                'cost' => $row['cost'],
                                'purchase_unit_id' => 1,
                                'TaxNet' => $row['TaxNet'],
                                'tax_method' => 1,
                                'discount' => $row['discount'],
                                'discount_method' => 2,
                                'purchase_id' => $purchase->id,
                                'product_id' => $row['product_id'],
                                'product_variant_id' => $row['product_variant_id'] ?? null,
                                'total' => $row['total'],
                                'quantity' => $row['quantity'],
                                'box' => $row['box'],
                                'weight' => $row['weight'],
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),
                            ];

                            
                            if ($purchase->statut == "received") {
                                if ($row['product_variant_id'] !== null) {
                                    $product_warehouse = product_warehouse::where('warehouse_id', $purchase->warehouse_id)
                                        ->where('product_id', $row['product_id'])
                                        ->where('product_variant_id', $row['product_variant_id'])
                                        ->first();

                                    if ($unit && $product_warehouse) {
                                        if ($unit->operator == '/') {
                                            $product_warehouse->qte += $row['quantity'] / $unit->operator_value;
                                        } else {
                                            $product_warehouse->qte += $row['quantity'] * $unit->operator_value;
                                        }
                                        $product_warehouse->save();
                                    }
                                } else {
                                    $product_warehouse = product_warehouse::where('warehouse_id', $purchase->warehouse_id)
                                        ->where('product_id', $row['product_id'])
                                        ->first();

                                    if ($unit && $product_warehouse) {
                                        if ($unit->operator == '/') {
                                            $product_warehouse->qte += $row['quantity'] / $unit->operator_value;
                                        } else {
                                            $product_warehouse->qte += $row['quantity'] * $unit->operator_value;
                                        }
                                        $product_warehouse->save();
                                    }
                                }
                            }
                        });
                        PurchaseDetail::insert($insert);
                    }
                });

                DB::commit();
                return $this->sendResponse($insert, 'Purchase saved successfully');
            } catch (Exception $ex) {
                DB::rollBack();
                return $this->sendError($ex->getMessage());
            }
        } else {
            return $this->sendError('The incomes attribute does not exist');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $purchase = Purchase::with('facture', 'provider', 'warehouse')->find($id);
        if (empty($purchase)) {
            return $this->sendError('Purchase not found');
        }
        return $this->sendResponse($purchase, 'Purchase retrievied sucessfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePruchaseRequest $request, $id)
    {
        $purchase = Purchase::find($id);

        if (empty($purchase)) {
            return $this->sendError('Purchase not found');
        }

        DB::beginTransaction();

        try {

            $old_purchase_details = PurchaseDetail::where('purchase_id', $id)->get();
            $new_purchase_details = $request['details'];
            $length = sizeof($new_purchase_details);

            // Get Ids for new Details
            $new_products_id = [];
            foreach ($new_purchase_details as $new_detail) {
                $new_products_id[] = $new_detail['id'];
            }

            // Init Data with old Parametre
            $old_products_id = [];
            foreach ($old_purchase_details as $key => $value) {
                $old_products_id[] = $value->id;

                //check if detail has purchase_unit_id Or Null
                if ($value['purchase_unit_id'] !== null) {
                    $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                } else {
                    $product_unit_purchase_id = Product::with('unitPurchase')->where('id', $value['product_id'])->first();
                    $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                }

                if ($value['purchase_unit_id'] !== null) {
                    if ($purchase->statut == "received") {

                        if ($value['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('warehouse_id', $purchase->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse) {
                                if ($unit->operator == '/') {
                                    $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $purchase->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->first();

                            if ($unit && $product_warehouse) {
                                if ($unit->operator == '/') {
                                    $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        }
                    }

                    // Delete Detail
                    if (!in_array($old_products_id[$key], $new_products_id)) {
                        $PurchaseDetail = PurchaseDetail::findOrFail($value->id);
                        $PurchaseDetail->delete();
                    }
                }
            }

            // Update Data with New request
            foreach ($new_purchase_details as $key => $prod_detail) {

                if ($prod_detail['no_unit'] !== 0) {
                    $unit_prod = Unit::where('id', $prod_detail['purchase_unit_id'])->first();

                    if ($request['statut'] == "received") {

                        if ($prod_detail['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->where('product_variant_id', $prod_detail['product_variant_id'])
                                ->first();

                            if ($unit_prod && $product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte += $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte += $prod_detail['quantity'] * $unit_prod->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        } else {
                            $product_warehouse = product_warehouse::where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->first();

                            if ($unit_prod && $product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte += $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte += $prod_detail['quantity'] * $unit_prod->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        }
                    }

                    $orderDetails['purchase_id'] = $id;
                    $orderDetails['cost'] = $prod_detail['Unit_cost'];
                    $orderDetails['purchase_unit_id'] = $prod_detail['purchase_unit_id'];
                    $orderDetails['TaxNet'] = $prod_detail['tax_percent'];
                    $orderDetails['tax_method'] = $prod_detail['tax_method'];
                    $orderDetails['discount'] = $prod_detail['discount'];
                    $orderDetails['discount_method'] = $prod_detail['discount_Method'];
                    $orderDetails['quantity'] = $prod_detail['quantity'];
                    $orderDetails['product_id'] = $prod_detail['product_id'];
                    $orderDetails['product_variant_id'] = $prod_detail['product_variant_id'];
                    $orderDetails['total'] = $prod_detail['subtotal'];

                    if (!in_array($prod_detail['id'], $old_products_id)) {
                        PurchaseDetail::Create($orderDetails);
                    } else {
                        PurchaseDetail::where('id', $prod_detail['id'])->update($orderDetails);
                    }
                }
            }

            $due = $request['GrandTotal'] - $purchase->paid_amount;
            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due != $request['GrandTotal']) {
                $payment_statut = 'partial';
            } else if ($due == $request['GrandTotal']) {
                $payment_statut = 'unpaid';
            }

            $purchase->update([
                'date' => $request['date'],
                'provider_id' => $request['provider_id'],
                'warehouse_id' => $request['warehouse_id'],
                'notes' => $request['notes'],
                'tax_rate' => $request['tax_rate'],
                'TaxNet' => $request['TaxNet'],
                'discount' => $request['discount'],
                'shipping' => $request['shipping'],
                'statut' => $request['statut'],
                'GrandTotal' => $request['GrandTotal'],
                'payment_statut' => $payment_statut,
            ]);


            DB::commit();
            return $this->sendResponse($purchase, 'Purchase updated successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $current_Purchase = Purchase::find($id);
        if (empty($current_Purchase)) {
            return $this->sendError('Purchase not found');
        }

        DB::beginTransaction();
        try {

            $old_purchase_details = PurchaseDetail::where('purchase_id', $id)->get();
            foreach ($old_purchase_details as $key => $value) {

                //check if detail has purchase_unit_id Or Null
                if ($value['purchase_unit_id'] !== null) {
                    $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                } else {
                    $product_unit_purchase_id = Product::with('unitPurchase')
                        ->where('id', $value['product_id'])
                        ->first();
                    $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                }

                if ($current_Purchase->statut == "received") {

                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_Purchase->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($unit && $product_warehouse) {
                            if ($unit->operator == '/') {
                                $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                            }

                            $product_warehouse->save();
                        }
                    } else {
                        $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_Purchase->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                        if ($unit && $product_warehouse) {
                            if ($unit->operator == '/') {
                                $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                            } else {
                                $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                            }

                            $product_warehouse->save();
                        }
                    }
                }
            }

            $current_Purchase->details()->delete();
            $current_Purchase->delete();

            PaymentPurchase::where('purchase_id', $id)->delete();

            DB::commit();
            return $this->sendResponse($current_Purchase, 'Purchase deteled successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    public function generateReport (Request $request) {
        
        $from = isset($request->from) ? $request->from : Carbon::now()->firstOfMonth()->format('Y-m-d');
        $to = isset($request->to) ? $request->to : Carbon::now()->endOfMonth()->format('Y-m-d'); 

        $purchases = Purchase::join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
        ->join('products', 'purchase_details.product_id', '=', 'products.id')
        ->join('providers', 'purchases.provider_id', '=', 'providers.id')
        ->selectRaw('providers.name as provider, products.name as product, sum(purchase_details.quantity) as quantity, sum(purchases.GrandTotal) as total')
        ->whereBetween('purchases.date', [$from, $to])
        ->groupBy('purchases.provider_id')
        ->groupBy('products.name')
        ->get();

        $providers = [];
        $totales = [];
        foreach ($purchases as $key => $item) {
            $providers[$item['provider']][] = $item;
        }
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();
        $totales = collect($purchases)->groupBy('product')->map(function ($item) use ($from, $to) {
            return [
                'product' => $item->first()->product,
                'quantity' => $item->sum('quantity'),
                'total' => $item->sum('total'),
                'gas' => Expense::where('expense_category_id', 2)->whereBetween('created_at', [$from, $to])->sum('amount'), //Combustible,
                'payroll' => Expense::where('expense_category_id', 3)->whereBetween('created_at', [$from, $to])->sum('amount'), //Nomina,
                'bills' => Expense::whereNotIn('expense_category_id', [2, 3])->whereBetween('created_at', [$from, $to])->sum('amount'), //Gastos,
            ];
        });
        $name = "/public/exports/Reporte-Compras-" . Carbon::parse($from)->format('Y-m-d') . "-" . Carbon::parse($to)->format('Y-m-d') . ".xlsx";   
        Excel::store(new PurchasesWeekly($providers, $totales, $from, $to), $name);
        return [
            'success' => true,
            'url' => asset("storage/exports/Reporte-Compras-" . Carbon::parse($from)->format('Y-m-d') . "-" . Carbon::parse($to)->format('Y-m-d') . ".xlsx"),
            'message' => 'Purchase Report successfully generated',
        ];        
    }

    public function getNumberOrder()
    {

        $last = Purchase::lastest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode("_", $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'PS_1111';
        }
        return $code;
    }
}
