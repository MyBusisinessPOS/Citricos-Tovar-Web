<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\role_user;
use App\Models\User;
use App\utils\helpers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserAPIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::with('roles')->where(function ($query) use ($request) {
            return $query->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('username', 'LIKE', $request->search . "%")
                    ->orWhere('firstname', 'LIKE', $request->search . "%")
                    ->orWhere('lastname', 'LIKE', $request->search . "%")
                    ->orWhere('email', 'LIKE', $request->search . "%")
                    ->orWhere('phone', 'LIKE', $request->search . "%");
            });
        })->paginate($request->limit ?? 10);
        return $this->sendResponse($users, 'User retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        DB::beginTransaction();
        try {

            $input = $request->all();

            if (isset($request['avatar']) && !empty($request['avatar'])) {

                $image = $request->avatar;
                $helpers = new helpers();
                $extension = $helpers->getExtensionFromBase64($image);
                @list($type, $file_data) = explode(';', $image);
                @list(, $file_data) = explode(',', $file_data);
                $name = rand(11111111, 99999999) . "." . $extension;
                $path = public_path() . '/images/avatar/';
                $success = file_put_contents($path . $name, base64_decode($file_data));
                $input['avatar'] = $name;
            } else {
                $input['avatar'] = "no_avatar.png";
            }

            $input['password'] = Hash::make($request['password']);
            $input['password_mobile'] = $request['password'];
            $user = User::create($input);

            role_user::create([
                'user_id' => $user->id,
                'role_id' => $user->role_id
            ]);

            DB::commit();
            return $this->sendResponse($user, 'User saved successfully');
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
        $user = User::find($id);
        if (empty($user)) {
            return $this->sendError('User not found', 404);
        }
        return $this->sendResponse($user, 'User retrieved successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return $this->sendError('User not found', 404);
        }

        DB::beginTransaction();
        try {

            $input = $request->all();
            if (isset($input['password']) && !empty($input['password'])) {
                $input['password_mobile'] = $input['password'];
                $input['password'] = Hash::make($input['password']);
            } else {
                unset($input['password']);
            }
            if (isset($request->avatar) && !empty($request->avatar)) {

                $image = $request->avatar;
                $helpers = new helpers();
                $extension = $helpers->getExtensionFromBase64($image);
                @list($type, $file_data) = explode(';', $image);
                @list(, $file_data) = explode(',', $file_data);
                $name = rand(11111111, 99999999) . "." . $extension;
                $path = public_path() . '/images/avatar/';
                
                $userPhoto = $path . '/' . $user->avatar;
                if (file_exists($userPhoto)) {
                    if ($user->avatar != 'no_avatar.png') {
                        @unlink($userPhoto);
                    }
                }
                
                $success = file_put_contents($path . $name, base64_decode($file_data));
                $input['avatar'] = $name;

            } else {
                $input['avatar'] = 'no_avatar.png';
            }
            $user->update($input);

            role_user::where('user_id' , $id)->update([
                'user_id' => $id,
                'role_id' => $request['role_id'],
            ]);

            DB::commit();
            return $this->sendResponse($user, 'User updated successfully');
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
        $user = User::find($id);
        if (empty($user)) {
            return $this->sendError('User not found');
        }

        $user->delete();
        return $this->sendResponse($user, 'User deleted successfully');
    }
}
