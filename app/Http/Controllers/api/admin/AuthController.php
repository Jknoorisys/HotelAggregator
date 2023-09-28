<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Services;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   =>  trans('msg.validation'),
                'errors'    =>  $validator->errors(),
            ], 400);
        } 

        try {
            $service = new Services();
            $email = $request->email;
            $password = $request->password;

            $admin  = Admin::where('email', '=', $email)->first();

            if(!empty($admin)) 
            {
                if (Hash::check($password, $admin->password)) {
                    $claims = array(
                        'exp'   => Carbon::now()->addDays(1)->timestamp,
                        'uuid'  => $admin->id
                    );

                    $admin->JWT_token = $service->getSignedAccessTokenForUser($admin, $claims);
                    $admin->save();

                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.login.success'),
                        'data'      => $admin,
                    ], 200);
                }else {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.login.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.login.invalid-email'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
