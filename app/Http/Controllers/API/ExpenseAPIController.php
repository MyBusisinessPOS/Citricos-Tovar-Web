<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $expenses = Expense::with('expense_category', 'warehouse')
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('user_id'), function ($query) use ($request) {
                    return $query->where('user_id', $request->user_id);
                });
            })->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('Ref', 'LIKE', $request->search . "%")
                        ->orWhere('date', 'LIKE', $request->search . "%")
                        ->orWhere('details', 'LIKE', $request->search . "%")
                        ->orWhere(function ($query) use ($request) {
                            return $query->whereHas('expense_category', function ($q) use ($request) {
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
            return $this->sendResponse($expenses, 'Expense retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateExpenseRequest $request)
    {
        DB::beginTransaction();
        try {

            $input = $request->all();
            $input['Ref'] = $this->getNumberOrder();
            $expense = Expense::create($input);

            DB::commit();
            return $this->sendResponse($expense, 'Expense saved successfully');
        }catch (Exception $ex) {
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
        $expense = Expense::find($id);
        if (empty($expense)) {
            return $this->sendError('Expense not found', 404);
        }
        return $this->sendResponse($expense, 'Expense retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateExpenseRequest $request, $id)
    {
        $expense = Expense::find($id);
        if (empty($expense)) {
            return $this->sendError('Expense not found', 404);
        }

        try {

            $input = $request->all();
            $expense->update($input);
            return $this->sendResponse($expense, 'Expense updated successfully');

        }catch (Exception $ex) {
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
        $expense = Expense::find($id);
        if (empty($expense)) {
            return $this->sendError('Expense not found', 404);
        }
        $expense->delete();
        return $this->sendResponse($expense, 'Expense deleted successfully');
    }

    private function getNumberOrder()
    {

        $last = Expense::latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode("_", $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'EXP_1111';
        }
        return $code;
    }
}
