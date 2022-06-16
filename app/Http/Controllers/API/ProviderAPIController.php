<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProviderRequest;
use App\Http\Requests\UpdateProviderRequest;
use App\Models\Provider;
use App\utils\helpers;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $providers = Provider::query()
            ->where(function ($q) {
                $q->where('name', 'LIKE', request('search') . '%')
                    ->orWhere('code', 'LIKE',  request('search') . '%')
                    ->orWhere('phone', 'LIKE',  request('search') . '%')
                    ->orWhere('email', 'LIKE',  request('search') . '%');
            })
            ->paginate(request('limit'));
        return $this->sendResponse($providers, 'Providers retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (isset($request->providers)) {

            $insert = [];
            collect($request->providers)->each(function ($item) use (&$insert) {
                $insert[] = [
                    'account_number' => $item['account_number'],
                    'provider' => $item['provider'],
                    'name' => $item['name'],
                    'code' => (!empty($item['code']) && $item['code'] != 0) ? $item['code'] : $this->getNumberOrder(),
                    'email' => $item['email'],
                    'rfc' => $item['rfc'],
                    'use_cfdi' => $item['use_cfdi'],
                    'phone' => $item['phone'],
                    'country' => $item['country'],
                    'city' => $item['city'],                   
                    'adresse' => $item['adresse'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            });

            try {
                DB::beginTransaction();
                $provider = Provider::insert($insert);
                DB::commit();
                return $this->sendResponse($provider, 'Provider saved successfully');
            } catch (Exception $ex) {
                DB::rollBack();
                return $this->sendError($ex->getMessage());
            }
        } else {
            return $this->sendError('The providers attribute does not exist');
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
        $provider = Provider::find($id);
        if (empty($provider)) {
            return $this->sendError('Provider not found', 404);
        }
        return $this->sendResponse($provider, 'Provider retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProviderRequest $request, $id)
    {
        $input = $request->all();
        $provider = Provider::find($id);
        if (empty($provider)) {
            return $this->sendError('Provider not found', 404);
        }

        $provider->update($input);
        return $this->sendResponse($provider, 'Provider updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $provider = Provider::find($id);
        if (empty($provider)) {
            return $this->sendError('Provider not found', 404);
        }
        $provider->delete();
        return $this->sendResponse($provider, 'Provider deleted successfully');
    }

    /**
     * Get last item
     *
     * @return  [inte]  [return last code]
     */
    private function getNumberOrder()
    {
        $last = Provider::latest('id')->first();
        return ($last) ? $last->code + 1 : 1;
    }

}
