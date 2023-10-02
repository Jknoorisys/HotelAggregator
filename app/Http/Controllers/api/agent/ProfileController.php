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

    public function updateProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'agent_id'  => ['required','alpha_dash', Rule::notIn('undefined')],
            'fname'     => ['string', 'max:255'],
            'lname'     => ['string', 'max:255'],
            'email'     => ['email', 'max:255', Rule::unique('users')->ignore($request->agent_id)],
            'phone'     => ['numeric', 'digits:10', Rule::unique('users')->ignore($request->agent_id)],
            'address'   => ['string', 'max:255'],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'logo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
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
    
            $data = [
                'fname'     => $request->input('fname', $agent->fname),
                'lname'     => $request->input('lname', $agent->lname),
                'email'     => $request->input('email', $agent->email),
                'country_code' => $request->input('country_code', $agent->country_code),
                'phone'     => $request->input('phone', $agent->phone),
                'address'   => $request->input('address', $agent->address),
            ];
    
            $file = $request->file('photo');
            if ($file) {
                if ($agent->photo) {
                    $oldPhotoPath = public_path($agent->photo);
        
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath); 
                    }
                }
        
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $image_url = $file->move('assets/uploads/agent-photos/', $filename);
                $data['photo'] = $image_url;
            }
    
            $logo = $request->file('logo'); 
            if ($logo) {
                if ($agent->logo) {
                    $oldLogoPath = public_path($agent->logo);
        
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath); 
                    }
                }

                $extension = $logo->getClientOriginalExtension();
                $logo_name = time().'.'.$extension;
                $logo_url = $logo->move('assets/uploads/agent-logos/', $logo_name);
                $data['logo'] = $logo_url; 
            }
    
            $update = $agent->update($data);
    
            if ($update) {
                $agent->fresh();
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.update.success'),
                    'data'      => $agent,
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.failed'),
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
            'new_password'   => ['required', 'min:8', 'max:20'],
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
