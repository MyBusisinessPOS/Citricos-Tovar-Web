<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseCategoryAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expenseCategory = ExpenseCategory::where('deleted_at', '=', null)
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
        return $this->sendResponse($expenseCategory, 'Expense catgorie retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (isset($request->expenseCategory)) {

            $insert = [];

            collect($request->expenseCategory)->each(function ($item) use (&$insert){
                $category = ExpenseCategory::where('name', $item['name'])->first();
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
                $expenseCategory = ExpenseCategory::insert($insert);
                DB::commit();
                return $this->sendResponse($expenseCategory, 'Expense categorie saved successfully');
            } catch (Exception $ex) {
                DB::rollBack();
                return $this->sendError($ex->getMessage());
            }

        } else {
            return $this->sendError('The expenseCategory attribute does not exist');
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
        $expenseCategory = ExpenseCategory::find($id);
        if (empty($expenseCategory)) {
            return $this->sendError('Expense categorie not found', 404);
        }
        return $this->sendResponse($expenseCategory, 'Expense categorie retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpenseCategoryRequest $request, $id)
    {
        $expenseCategory = ExpenseCategory::find($id);
        if (empty($expenseCategory)) {
            return $this->sendError('Expense categorie not found', 404);
        }

        DB::beginTransaction();
        try {
            $input = $request->all();
            $expenseCategory->update($input);
            DB::commit();
            return $this->sendResponse($expenseCategory, 'Expense categorie updated successfully');
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
        $expenseCategory = ExpenseCategory::find($id);
        if (empty($expenseCategory)) {
            return $this->sendError('Expense categorie not found', 404);
        }
        $expenseCategory->delete();
        return $this->sendResponse($expenseCategory, 'Expense categorie deleted successfully');
    }
}
