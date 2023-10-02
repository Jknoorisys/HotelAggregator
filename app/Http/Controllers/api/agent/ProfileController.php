<?php

namespace App\Http\Controllers\api\agent;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use function App\Helpers\validateAgent;

class ProfileController extends Controller
{
    public function getProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id'   => ['required','alpha_dash', Rule::notIn('undefined')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        } 

        try {
            $agent = validateAgent($request->agent_id);
            if (!empty($agent) && $agent->status == 'inactive') {
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.detail.inactive'),
                ], 400);
            }

            if (!empty($agent)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.detail.success'),
                    'data'      => $agent,
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.detail.failed'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agent_id' => 'required',
            'old_password' => 'required',
            'new_password'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        } 

        try {
            
            $old_password = $request->old_password;
            $new_password = $request->new_password;

            $agent = validateAgent($request->agent_id);
            if (!empty($agent) && $agent->status == 'inactive') {
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.detail.inactive'),
                ], 400);
            }

            if(!empty($agent)) 
            {
                if (Hash::check($old_password, $agent->password)) {

                    $agent->password = Hash::make($new_password);
                    $update = $agent->save();

                    if ($update) {
                        return response()->json([
                            'status'    => 'success',
                            'message'   => trans('msg.change-password.success'),
                            'data'      => $agent,
                        ], 200);
                    } else {
                        return response()->json([
                            'status'    => 'failed',
                            'message'   => trans('msg.change-password.failed'),
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.change-password.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.change-password.not-found'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
