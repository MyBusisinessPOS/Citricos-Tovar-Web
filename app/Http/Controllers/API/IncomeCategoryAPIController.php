<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\IncomeCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeCategoryAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $incomeCategory = IncomeCategory::where('deleted_at', '=', null)
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('user_id'), function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id);
                });
            })
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', $request->search . "%")
                        ->orWhere('description', 'LIKE', $request->search . "%");
                });
            })->paginate($request->limit ?? 10);
        return $this->sendResponse($incomeCategory, 'Imcome catgories retrieved successfully');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($request->incomeCategory)) {

            $insert = [];

            collect($request->incomeCategory)->each(function ($item) use (&$insert){
                $category = IncomeCategory::where('name', $item['name'])->first();
                if (!empty($category)) {
                    $category->user_id = $item['user_id'];
                    $category->name = $item['name'];
                    $category->description = $item['description'];
                    $category->save();
                } else {
                    $insert[] = [
                        'user_id' => $item['user_id'],
                        'name' => $item['name'],
                        'description' => $item['description'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            });

            try {
                DB::beginTransaction();
                $incomeCategory = IncomeCategory::insert($insert);
                DB::commit();
                return $this->sendResponse($incomeCategory, 'Income categories saved successfully');
            } catch (Exception $ex) {
                DB::rollBack();
                return $this->sendError($ex->getMessage());
            }

        } else {
            return $this->sendError('The incomeCategory attribute does not exist');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\IncomeCategory  $incomeCategory
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $incomeCategory = IncomeCategory::find($id);
        if (empty($incomeCategory)) {
            return $this->sendError('Income categorie not found', 404);
        }
        return $this->sendResponse($incomeCategory, 'Income categories retrieved successfully');
    }

    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\IncomeCategory  $incomeCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $incomeCategory = IncomeCategory::find($id);
        if (empty($incomeCategory)) {
            return $this->sendError('Income categorie not found', 404);
        }
        
        try {
            $input = $request->all();
            DB::beginTransaction();
            $incomeCategory->update($input);
            DB::commit();
            return $this->sendResponse($incomeCategory, 'Income categorie updated successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\IncomeCategory  $incomeCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $incomeCategory = IncomeCategory::find($id);
        if (empty($incomeCategory)) {
            return $this->sendError('Income categorie not found', 404);
        }
        $incomeCategory->delete();
        return $this->sendResponse($incomeCategory, 'Income categorie deleted successfully');
    }
}
