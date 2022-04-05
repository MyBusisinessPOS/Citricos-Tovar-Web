<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\PaymentSale;
use App\Models\PaymentWithCreditCard;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\Unit;
use App\utils\helpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check If User Has Permission View  All Records
        $Sales = Sale::with('facture', 'client', 'warehouse')->where(function ($query) use ($request) {
            return $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('Ref', 'LIKE', "%{$request->search}%")
                    ->orWhere('statut', 'LIKE', "%{$request->search}%")
                    ->orWhere('GrandTotal', $request->search)
                    ->orWhere('payment_statut', 'like', "$request->search")
                    ->orWhere(function ($query) use ($request) {
                        return $query->whereHas('client', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    })
                    ->orWhere(function ($query) use ($request) {
                        return $query->whereHas('warehouse', function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%{$request->search}%");
                        });
                    });
            });
        })->paginate($request->limit ?? 10);
        return $this->sendResponse($Sales, 'Sales retrievied sucessfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSaleRequest $request)
    {
        DB::beginTransaction();
        try {

            $input = $request->all();
            $helpers = new helpers();

            $input['Ref'] = $this->getNumberOrder();
            $input['payment_statut'] = 'unpaid';
            // $input['date'] = Carbon::now();
            $input['is_pos'] = 0;
            $sale = Sale::create($input);

            $data = $request['details'];
            foreach ($data as $key => $value) {
                $unit = Unit::find($value['sale_unit_id']);
                $orderDetails[] = [
                    'date' => $input['date'],
                    'sale_id' => $sale->id,
                    'sale_unit_id' =>  $value['sale_unit_id'],
                    'quantity' => $value['quantity'],
                    'price' => $value['Unit_price'],
                    'TaxNet' => $value['tax_percent'],
                    'tax_method' => $value['tax_method'],
                    'discount' => $value['discount'],
                    'discount_method' => $value['discount_Method'],
                    'product_id' => $value['product_id'],
                    'product_variant_id' => $value['product_variant_id'],
                    'total' => $value['subtotal'],
                ];

                if ($sale->statut == "completed") {
                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = product_warehouse::where('warehouse_id', $sale->warehouse_id)
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
                        $product_warehouse = product_warehouse::where('warehouse_id', $sale->warehouse_id)
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

            SaleDetail::insert($orderDetails);

            // $sale = Sale::find($order->id);

            $total_paid = $sale->paid_amount + $request['amount'];
            $due = $sale->GrandTotal - $total_paid;
            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due != $sale->GrandTotal) {
                $payment_statut = 'partial';
            } else if ($due == $sale->GrandTotal) {
                $payment_statut = 'unpaid';
            }

            if ($request['amount'] > 0) {
                if ($request->payment['Reglement'] == 'credit card') {
                } else {
                    PaymentSale::create([
                        'sale_id' => $sale->id,
                        'Ref' => app('App\Http\Controllers\PaymentSalesController')->getNumberOrder(),
                        'date' => Carbon::now(),
                        'Reglement' => $request->payment['Reglement'],
                        'montant' => $request['amount'],
                        'change' => $request['change'],
                        'user_id' => $request->user_id,
                    ]);

                    $sale->update([
                        'paid_amount' => $total_paid,
                        'payment_statut' => $payment_statut,
                    ]);
                }
            }

            DB::commit();
            return $this->sendResponse($sale, 'Sale saved successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
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
        $sale_data = Sale::with('details.product.unitSale')->find($id);
        if (empty($sale_data)) {
            return $this->sendError('Sale not found');
        }

        $details = array();
        $sale_details['Ref'] = $sale_data->Ref;
        $sale_details['date'] = $sale_data->date;
        $sale_details['note'] = $sale_data->notes;
        $sale_details['statut'] = $sale_data->statut;
        $sale_details['warehouse'] = $sale_data['warehouse']->name;
        $sale_details['discount'] = $sale_data->discount;
        $sale_details['shipping'] = $sale_data->shipping;
        $sale_details['tax_rate'] = $sale_data->tax_rate;
        $sale_details['TaxNet'] = $sale_data->TaxNet;
        $sale_details['client_name'] = $sale_data['client']->name;
        $sale_details['client_phone'] = $sale_data['client']->phone;
        $sale_details['client_adr'] = $sale_data['client']->adresse;
        $sale_details['client_email'] = $sale_data['client']->email;
        $sale_details['GrandTotal'] = number_format($sale_data->GrandTotal, 2, '.', '');
        $sale_details['paid_amount'] = number_format($sale_data->paid_amount, 2, '.', '');
        $sale_details['due'] = number_format($sale_details['GrandTotal'] - $sale_details['paid_amount'], 2, '.', '');
        $sale_details['payment_status'] = $sale_data->payment_statut;

        foreach ($sale_data['details'] as $detail) {

            //check if detail has sale_unit_id Or Null
            if ($detail->sale_unit_id !== null) {
                $unit = Unit::where('id', $detail->sale_unit_id)->first();
            } else {
                $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $detail->product_id)
                    ->first();
                $unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
            }

            if ($detail->product_variant_id) {

                $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                    ->where('id', $detail->product_variant_id)->first();

                $data['code'] = $productsVariants->name . '-' . $detail['product']['code'];
            } else {
                $data['code'] = $detail['product']['code'];
            }

            $data['quantity'] = $detail->quantity;
            $data['total'] = $detail->total;
            $data['name'] = $detail['product']['name'];
            $data['price'] = $detail->price;
            $data['unit_sale'] = $unit->ShortName;

            if ($detail->discount_method == '2') {
                $data['DiscountNet'] = $detail->discount;
            } else {
                $data['DiscountNet'] = $detail->price * $detail->discount / 100;
            }

            $tax_price = $detail->TaxNet * (($detail->price - $data['DiscountNet']) / 100);
            $data['Unit_price'] = $detail->price;
            $data['discount'] = $detail->discount;

            if ($detail->tax_method == '1') {
                $data['Net_price'] = $detail->price - $data['DiscountNet'];
                $data['taxe'] = $tax_price;
            } else {
                $data['Net_price'] = ($detail->price - $data['DiscountNet']) / (($detail->TaxNet / 100) + 1);
                $data['taxe'] = $detail->price - $data['Net_price'] - $data['DiscountNet'];
            }

            $details[] = $data;
        }

        $company = Setting::find(1);
        return $this->sendResponse([
            'details' => $details,
            'sale' => $sale_details,
            'company' => $company,
        ], 'Sale retrievied successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSaleRequest $request, $id)
    {
        $current_Sale = Sale::find($id);
        if (empty($current_Sale)) {
            return $this->sendError('Sale not found');
        }

        DB::beginTransaction();

        try {

            $old_sale_details = SaleDetail::where('sale_id', $id)->get();
            $new_sale_details = $request['details'];
            $length = sizeof($new_sale_details);

            // Get Ids for new Details
            $new_products_id = [];
            foreach ($new_sale_details as $new_detail) {
                $new_products_id[] = $new_detail['id'];
            }

            // Init Data with old Parametre
            $old_products_id = [];
            foreach ($old_sale_details as $key => $value) {
                $old_products_id[] = $value->id;

                //check if detail has sale_unit_id Or Null
                if ($value['sale_unit_id'] !== null) {
                    $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                } else {
                    $product_unit_sale_id = Product::with('unitSale')
                        ->where('id', $value['product_id'])
                        ->first();
                    $old_unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                }

                if ($value['sale_unit_id'] !== null) {
                    if ($current_Sale->statut == "completed") {

                        if ($value['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($product_warehouse) {
                                if ($old_unit->operator == '/') {
                                    $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                                } else {
                                    $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->first();
                            if ($product_warehouse) {
                                if ($old_unit->operator == '/') {
                                    $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                                } else {
                                    $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }
                    // Delete Detail
                    if (!in_array($old_products_id[$key], $new_products_id)) {
                        $SaleDetail = SaleDetail::findOrFail($value->id);
                        $SaleDetail->delete();
                    }
                }
            }

            // Update Data with New request
            foreach ($new_sale_details as $prd => $prod_detail) {

                if ($prod_detail['no_unit'] !== 0) {
                    $unit_prod = Unit::where('id', $prod_detail['sale_unit_id'])->first();

                    if ($request['statut'] == "completed") {

                        if ($prod_detail['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->where('product_variant_id', $prod_detail['product_variant_id'])
                                ->first();

                            if ($product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->first();

                            if ($product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }

                    $orderDetails['sale_id'] = $id;
                    $orderDetails['price'] = $prod_detail['Unit_price'];
                    $orderDetails['sale_unit_id'] = $prod_detail['sale_unit_id'];
                    $orderDetails['TaxNet'] = $prod_detail['tax_percent'];
                    $orderDetails['tax_method'] = $prod_detail['tax_method'];
                    $orderDetails['discount'] = $prod_detail['discount'];
                    $orderDetails['discount_method'] = $prod_detail['discount_Method'];
                    $orderDetails['quantity'] = $prod_detail['quantity'];
                    $orderDetails['product_id'] = $prod_detail['product_id'];
                    $orderDetails['product_variant_id'] = $prod_detail['product_variant_id'];
                    $orderDetails['total'] = $prod_detail['subtotal'];

                    if (!in_array($prod_detail['id'], $old_products_id)) {
                        $orderDetails['date'] = Carbon::now();
                        $orderDetails['sale_unit_id'] = $unit_prod ? $unit_prod->id : Null;
                        SaleDetail::Create($orderDetails);
                    } else {
                        SaleDetail::where('id', $prod_detail['id'])->update($orderDetails);
                    }
                }
            }

            $due = $request['GrandTotal'] - $current_Sale->paid_amount;
            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due != $request['GrandTotal']) {
                $payment_statut = 'partial';
            } else if ($due == $request['GrandTotal']) {
                $payment_statut = 'unpaid';
            }

            $current_Sale->update([
                'date' => $request['date'],
                'client_id' => $request['client_id'],
                'warehouse_id' => $request['warehouse_id'],
                'notes' => $request['notes'],
                'statut' => $request['statut'],
                'tax_rate' => $request['tax_rate'],
                'TaxNet' => $request['TaxNet'],
                'discount' => $request['discount'],
                'shipping' => $request['shipping'],
                'GrandTotal' => $request['GrandTotal'],
                'payment_statut' => $payment_statut,
            ]);
            DB::commit();

            return $this->sendResponse($current_Sale, 'Sale updated successfully');
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
        $current_Sale = Sale::find($id);
        if (empty($current_Sale)) {
            return $this->sendError('Sale not found');
        }

        DB::beginTransaction();
        try {

            $old_sale_details = SaleDetail::where('sale_id', $id)->get();
            foreach ($old_sale_details as $key => $value) {

                //check if detail has sale_unit_id Or Null
                if ($value['sale_unit_id'] !== null) {
                    $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                } else {
                    $product_unit_sale_id = Product::with('unitSale')
                        ->where('id', $value['product_id'])
                        ->first();
                    $old_unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                }

                if ($current_Sale->statut == "completed") {

                    if ($value['product_variant_id'] !== null) {
                        $product_warehouse = product_warehouse::where('warehouse_id', $current_Sale->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                        if ($product_warehouse) {
                            if ($old_unit->operator == '/') {
                                $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                            } else {
                                $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                            }
                            $product_warehouse->save();
                        }
                    } else {
                        $product_warehouse = product_warehouse::where('warehouse_id', $current_Sale->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();
                        if ($product_warehouse) {
                            if ($old_unit->operator == '/') {
                                $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                            } else {
                                $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                            }
                            $product_warehouse->save();
                        }
                    }
                }
            }

            $current_Sale->details()->delete();
            $current_Sale->delete();

            $Payment_Sale_data = PaymentSale::where('sale_id', $id)->get();
            foreach ($Payment_Sale_data as $Payment_Sale) {
                if ($Payment_Sale->Reglement == 'credit card') {
                    $PaymentWithCreditCard = PaymentWithCreditCard::where('payment_id', $Payment_Sale->id)->first();
                    if ($PaymentWithCreditCard) {
                        $PaymentWithCreditCard->delete();
                    }
                }
                $Payment_Sale->delete();
            }

            DB::commit();
            return $this->sendResponse($current_Sale, 'Sale deleted successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    private function getNumberOrder()
    {

        $last = Sale::latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode("_", $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'SL_1111';
        }
        return $code;
    }
}
