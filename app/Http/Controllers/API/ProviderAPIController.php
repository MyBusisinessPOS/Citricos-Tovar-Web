<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProviderRequest;
use App\Http\Requests\UpdateProviderRequest;
use App\Models\Provider;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            ->paginate(request('perPage'));
        return $this->sendResponse($providers, 'Providers retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateProviderRequest $request)
    {
        $input = $request->all();
        $input['code'] = $this->getNumberOrder();
        $provider = Provider::create($input);
        return $this->sendResponse($provider, 'Provider saved successfully');
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

        $last = Provider::latest()->first();;

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }

}
