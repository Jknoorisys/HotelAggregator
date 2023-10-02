<?php

namespace App\Http\Controllers\api\agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Libraries\Services;
use App\Models\User;

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

            $agent  = User::where('email', '=', $email)->first();

            if(!empty($agent)){
                if (Hash::check($password, $agent->password)) {

                    if (!empty($agent) && $agent->status == 'inactive') {
                        return response()->json([
                            'status'    => 'failed',
                            'message'   =>  trans('msg.login.inactive'),
                        ], 400);
                    }

                    $claims = array(
                        'exp'   => Carbon::now()->addDays(1)->timestamp,
                        'uuid'  => $agent->id
                    );

                    $agent->JWT_token = $service->getSignedAccessTokenForUser($agent, $claims);
                    $agent->save();

                    return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.login.success'),
                        'data'      => $agent,
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
