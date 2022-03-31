<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateWareHouseRequest;
use App\Http\Requests\UpdateWareHouseRequest;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WareHouseAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $warehouses = Warehouse::where(function ($query) use ($request) {
            return $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('name', 'LIKE', "%{$request->search}%")
                    ->orWhere('mobile', 'LIKE', "%{$request->search}%")
                    ->orWhere('country', 'LIKE', "%{$request->search}%")
                    ->orWhere('city', 'LIKE', "%{$request->search}%")
                    ->orWhere('zip', 'LIKE', "%{$request->search}%")
                    ->orWhere('email', 'LIKE', "%{$request->search}%");
            });
        })->paginate($request->limit ?? 10);
        return $this->sendResponse($warehouses, 'WareHouses retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateWareHouseRequest $request)
    {
        DB::beginTransaction();
        try {

            $input = $request->all();

            $warehouse = Warehouse::create($input);

            $products = Product::pluck('id')->toArray();
            if ($products) {
                foreach ($products as $product) {
                    $product_warehouse = [];
                    $Product_Variants = ProductVariant::where('product_id', $product)
                        ->where('deleted_at', null)
                        ->get();

                    if ($Product_Variants->isNotEmpty()) {
                        foreach ($Product_Variants as $product_variant) {

                            $product_warehouse[] = [
                                'product_id' => $product,
                                'warehouse_id' => $warehouse->id,
                                'product_variant_id' => $product_variant->id,
                            ];
                        }
                    } else {
                        $product_warehouse[] = [
                            'product_id' => $product,
                            'warehouse_id' => $warehouse->id,
                            'product_variant_id' => null,
                        ];
                    }

                    product_warehouse::insert($product_warehouse);
                }
            }

            DB::commit();
            return $this->sendResponse($warehouse, 'WareHouse saved successfully');
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
        $warehouse = Warehouse::find($id);
        if (empty($warehouse)) {
            return $this->sendError('WareHouse not found', 404);
        }
        return $this->sendResponse($warehouse, 'WareHouse retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateWareHouseRequest $request, $id)
    {
        $warehouse = Warehouse::find($id);
        if (empty($warehouse)) {
            return $this->sendError('WareHouse not found', 404);
        }

        DB::beginTransaction();
        try {

            $input = $request->all();
            $warehouse->update($input);
            DB::commit();
            
            return $this->sendResponse($warehouse, 'WareHouse updated successfully');

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
        $warehouse = Warehouse::find($id);
        if (empty($warehouse)) {
            return $this->sendError('WareHouse not found', 404);
        }
        $warehouse->delete();
        return $this->sendResponse($warehouse, 'WareHouse deleted successfully');
    }
}
