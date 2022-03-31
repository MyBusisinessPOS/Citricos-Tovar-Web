<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use App\utils\helpers;
use Exception;
use \Gumlet\ImageResize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ProductAPIController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::with('unit', 'category', 'brand')
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('products.name', 'LIKE', $request->search . '%')
                        ->orWhere('products.code', 'LIKE', $request->search . '%')
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('category', function ($q) use ($request) {
                                $q->where('name', 'LIKE', $request->search . '%');
                            });
                        })
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('brand', function ($q) use ($request) {
                                $q->where('name', 'LIKE', $request->search . '%');
                            });
                        });
                });
            })
            ->paginate(request('limit'));
        return $this->sendResponse($products, 'Products retrieved successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProductRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            if (isset($input['image']) && !empty($input['image'])) {

                $image = $request->image;
                $helpers = new helpers();
                $extension = $helpers->getExtensionFromBase64($image);
                @list($type, $file_data) = explode(';', $image);
                @list(, $file_data) = explode(',', $file_data);
                $name = rand(11111111, 99999999) . "." . $extension;
                $path = public_path() . '/images/products/';
                $success = file_put_contents($path . $name, base64_decode($file_data));
                $input['image'] = $name;
            } else {
                $input['image'] = "no-image.png";
            }
            $product = Product::create($input);

            if (boolval($input['is_variant'])) {
                foreach ($request['variants'] as $variant) {
                    $Product_variants_data[] = [
                        'product_id' => $product->id,
                        'name' => $variant,
                    ];
                }
                ProductVariant::insert($Product_variants_data);
            }

            $warehouses = Warehouse::pluck('id')->toArray();
            if ($warehouses) {
                $Product_variants = ProductVariant::where('product_id', $product->id)->get();
                foreach ($warehouses as $warehouse) {
                    if (boolval($input['is_variant'])) {
                        foreach ($Product_variants as $product_variant) {

                            $product_warehouse[] = [
                                'product_id' => $product->id,
                                'warehouse_id' => $warehouse,
                                'product_variant_id' => $product_variant->id,
                            ];
                        }
                    } else {
                        $product_warehouse[] = [
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouse,
                        ];
                    }
                }
                product_warehouse::insert($product_warehouse);
            }

            DB::commit();
            return $this->sendResponse($product, 'Product saved successfully');
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
        $Product = Product::find($id);
        if (empty($Product)) {
            return $this->sendError('Product not found', 404);
        }
        return $this->sendResponse($Product, 'Product retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);
        if (empty($product)) {
            return $this->sendError('Product not found', 404);
        }

        DB::beginTransaction();
        try {

            $input = $request->all();

            // Store Variants Product
            $oldVariants = ProductVariant::where('product_id', $id)->get();

            $warehouses = Warehouse::pluck('id')->toArray();

            if (boolval($input['is_variant'])) {

                if ($oldVariants->isNotEmpty()) {
                    $new_variants_id = [];
                    $var = 'id';

                    foreach ($request['variants'] as $new_id) {
                        if (array_key_exists($var, $new_id)) {
                            $new_variants_id[] = $new_id['id'];
                        } else {
                            $new_variants_id[] = 0;
                        }
                    }

                    foreach ($oldVariants as $key => $value) {
                        $old_variants_id[] = $value->id;

                        // Delete Variant
                        if (!in_array($old_variants_id[$key], $new_variants_id)) {
                            $ProductVariant = ProductVariant::findOrFail($value->id);
                            $ProductVariant->delete();
                            product_warehouse::where('product_variant_id', $value->id)->delete();
                        }
                    }

                    foreach ($request['variants'] as $key => $variant) {
                        if (array_key_exists($var, $variant)) {

                            $ProductVariantDT = new ProductVariant;

                            //-- Field Required
                            $ProductVariantDT->product_id = $variant['product_id'];
                            $ProductVariantDT->name = $variant['text'];
                            $ProductVariantDT->qty = $variant['qty'];
                            $ProductVariantUP['product_id'] = $variant['product_id'];
                            $ProductVariantUP['name'] = $variant['text'];
                            $ProductVariantUP['qty'] = $variant['qty'];
                        } else {
                            $ProductVariantDT = new ProductVariant;

                            //-- Field Required
                            $ProductVariantDT->product_id = $id;
                            $ProductVariantDT->name = $variant['text'];
                            $ProductVariantDT->qty = 0.00;
                            $ProductVariantUP['product_id'] = $id;
                            $ProductVariantUP['name'] = $variant['text'];
                            $ProductVariantUP['qty'] = 0.00;
                        }

                        if (!in_array($new_variants_id[$key], $old_variants_id)) {
                            $ProductVariantDT->save();

                            //--Store Product warehouse
                            if ($warehouses) {
                                $product_warehouse = [];
                                foreach ($warehouses as $warehouse) {

                                    $product_warehouse[] = [
                                        'product_id' => $id,
                                        'warehouse_id' => $warehouse,
                                        'product_variant_id' => $ProductVariantDT->id,
                                    ];
                                }
                                product_warehouse::insert($product_warehouse);
                            }
                        } else {
                            ProductVariant::where('id', $variant['id'])->update($ProductVariantUP);
                        }
                    }
                } else {
                    product_warehouse::where('product_id', $id)->delete();

                    foreach ($request['variants'] as $variant) {
                        $product_warehouse_DT = [];
                        $ProductVarDT = new ProductVariant;

                        //-- Field Required
                        $ProductVarDT->product_id = $id;
                        $ProductVarDT->name = $variant['text'];
                        $ProductVarDT->save();

                        //-- Store Product warehouse
                        if ($warehouses) {
                            foreach ($warehouses as $warehouse) {

                                $product_warehouse_DT[] = [
                                    'product_id' => $id,
                                    'warehouse_id' => $warehouse,
                                    'product_variant_id' => $ProductVarDT->id,
                                ];
                            }

                            product_warehouse::insert($product_warehouse_DT);
                        }
                    }
                }
            } else {
                if ($oldVariants->isNotEmpty()) {
                    foreach ($oldVariants as $old_var) {
                        $var_old = ProductVariant::where('product_id', $old_var['product_id'])->first();
                        $var_old->delete();
                        product_warehouse::where('product_variant_id', $old_var['id'])->delete();
                    }

                    if ($warehouses) {
                        foreach ($warehouses as $warehouse) {

                            $product_warehouse[] = [
                                'product_id' => $id,
                                'warehouse_id' => $warehouse,
                                'product_variant_id' => null,
                            ];
                        }
                        product_warehouse::insert($product_warehouse);
                    }
                }
            }

            if ($request->image === null) {
                if ($product->image !== null) {
                    foreach (explode(',', $product->image) as $img) {
                        $pathIMG = public_path() . '/images/products/' . $img;
                        if (file_exists($pathIMG)) {
                            if ($img != 'no-image.png') {
                                @unlink($pathIMG);
                            }
                        }
                    }
                }
                $input['image'] = 'no-image.png';
            } else {
                if ($product->image !== null) {
                    foreach (explode(',', $product->image) as $img) {
                        $pathIMG = public_path() . '/images/products/' . $img;
                        if (file_exists($pathIMG)) {
                            if ($img != 'no-image.png') {
                                @unlink($pathIMG);
                            }
                        }
                    }
                }

                $image = $request->image;
                $helpers = new helpers();
                $extension = $helpers->getExtensionFromBase64($image);
                @list($type, $file_data) = explode(';', $image);
                @list(, $file_data) = explode(',', $file_data);
                $name = rand(11111111, 99999999) . "." . $extension;
                $path = public_path() . '/images/products/';
                $success = file_put_contents($path . $name, base64_decode($file_data));
                $input['image'] = $name;
            }

            $product->update($input);
            DB::commit();

            return $this->sendResponse($product, 'Product updated successfully');
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
        DB::beginTransaction();
        try {
            $product = Product::find($id);
            if (empty($product)) {
                return $this->sendError('Product not found', 404);
            }
            $product->delete();

            foreach (explode(',', $product->image) as $img) {
                $pathIMG = public_path() . '/images/products/' . $img;
                if (file_exists($pathIMG)) {
                    if ($img != 'no-image.png') {
                        @unlink($pathIMG);
                    }
                }
            }

            product_warehouse::where('product_id', $id)->delete();
            ProductVariant::where('product_id', $id)->delete();

            DB::commit();

            return $this->sendResponse($product, 'Product deleted successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }
}
