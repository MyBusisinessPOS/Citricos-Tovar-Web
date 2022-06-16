<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Client;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $clients = Client::where(function ($query) use ($request) {
            return $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('name', 'LIKE', $request->search . "%")
                    ->orWhere('code', 'LIKE', $request->search . "%")
                    ->orWhere('phone', 'LIKE', $request->search . "%")
                    ->orWhere('email', 'LIKE', $request->search . "%");
            });
        })->paginate($request->limit ?? 10);
        return $this->sendResponse($clients, 'Customer retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (isset($request->clients)) {

            $insert = [];
            collect($request->clients)->each(function ($item) use (&$insert) {
                $insert[] = [
                    'account_number' => $item['account_number'],
                    'client' => $item['client'],
                    'name' => $item['name'],
                    'code' => (!empty($item['code']) && $item['code'] != 0) ? $item['code'] : $this->getNumberOrder(),
                    'email' => $item['email'],
                    'rfc' => $item['rfc'],
                    'use_cfdi' => $item['use_cfdi'],
                    'country' => $item['country'],
                    'city' => $item['city'],
                    'phone' => $item['phone'],
                    'adresse' => $item['adresse'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            });

            try {
                DB::beginTransaction();
                $client = Client::insert($insert);
                DB::commit();
                return $this->sendResponse($client, 'Customer saved successfully');
            } catch (Exception $ex) {
                DB::rollBack();
                return $this->sendError($ex->getMessage());
            }
        } else {
            return $this->sendError('The clients attribute does not exist');
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
        $customer = Client::find($id);
        if (empty($customer)) {
            return $this->sendError('Customer not found');
        }
        return $this->sendResponse($customer, 'Customer retrievied sucessfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = Client::find($id);

        if (empty($customer)) {
            return $this->sendError('Customer not found');
        }

        DB::beginTransaction();
        try {

            $input = $request->all();
            $customer->update($input);

            DB::commit();
            return $this->sendResponse($customer, 'Customer updated sucessfully');
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
        $customer = Client::find($id);
        if (empty($customer)) {
            return $this->sendError('Customer not found');
        }
        $customer->delete();
        return $this->sendResponse($customer, 'Customer deleted sucessfully');
    }

    private function getNumberOrder()
    {
        $last = Client::latest('id')->first();
        return ($last) ? $last->code + 1 : 1;
    }
}
