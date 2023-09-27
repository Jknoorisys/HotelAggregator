<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function changePassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admin_id' => 'required',
            'currentpassword' => 'required',
            'newpassword'   => 'required',
            'confirmpassword' => 'same:newpassword',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.validation'),
                    'errors'    =>  $validator->errors(),
                ], 400
            );
        } 

        try {
            
            $currentpassword = $req->currentpassword;
            $newpassword = $req->newpassword;
            $admin  = Admin::where('id', '=', $req->admin_id)->first();

            if(!empty($admin)) 
            {
                if (Hash::check($currentpassword,$admin->password)) {
                    $admin->password = Hash::make($newpassword);
                    $admin->save();
                        return response()->json(
                            [
                                'status'    => 'success',
                                'data' => $admin,
                                'message'   =>   __('msg.change-password.success'),
                            ], 200);
                }else {
                    return response()->json([
                            'status'    => 'failed',
                            'message'   =>  __('msg.change-password.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  __('msg.change-password.not-found'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  __('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getProfile(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admin_id'   => ['required','alpha_dash', Rule::notIn('undefined')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.validation'),
                    'errors'    =>  $validator->errors(),
                ], 400
            );
        } 

        try {
            $admin = Admin::where('id', '=', $req->admin_id)->first();
            if (!empty($admin)) {
                return response()->json(
                    [
                        'status'    => 'success',
                        'data' => $admin,
                        'message'   =>  __('msg.detail.success'),
                    ],200);
            } else {
                return response()->json(
                    [
                        'status'    => 'failed',
                        'message'   =>  __('msg.detail.failed'),
                    ],400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  __('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
