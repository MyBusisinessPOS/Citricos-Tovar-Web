<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $incomes = Income::with('income_category', 'warehouse')
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
                            return $query->whereHas('income_category', function ($q) use ($request) {
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
        return $this->sendResponse($incomes, 'Incomes retrieved successfully');
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($request->incomes)) {

            $insert = [];
            collect($request->incomes)->each(function ($item) use (&$insert) {
                $insert[] = [
                    'date' => $item['date'],
                    'Ref' => !empty($item['ref']) ? $item['ref'] : $this->getNumberOrder(),
                    'user_id' => $item['userId'],
                    'income_category_id' => $item['incomeCategoryId'],
                    'warehouse_id' => $item['warehouseId'],
                    'details' => $item['details'],
                    'amount' => $item['amount'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            });

            try {
                DB::beginTransaction();
                $income = Income::insert($insert);
                DB::commit();
                return $this->sendResponse($income, 'Income saved successfully');
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
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $income = Income::find($id);
        if (empty($income)) {
            return $this->sendError('Income not found', 404);
        }
        return $this->sendResponse($income, 'Income retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        request()->validate([
            'income.date' => 'required',
            'income.warehouse_id' => 'required',
            'income.income_category_id' => 'required',
            'income.details' => 'required',
            'income.amount' => 'required',
            'income.user_id' => 'required',
        ]);

        $income = Income::find($id);
        if (empty($income)) {
            return $this->sendError('Income not found', 404);
        }

        try {

            $input = $request->all();
            $income->update($input);
            return $this->sendResponse($income, 'Income updated successfully');
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $income = Income::find($id);
        if (empty($income)) {
            return $this->sendError('Income not found', 404);
        }
        $income->delete();
        return $this->sendResponse($income, 'Income deleted successfully');
    }

    private function getNumberOrder()
    {

        $last = Income::latest('id')->first();

        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode("_", $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'INC_1111';
        }
        return $code;
    }
}
