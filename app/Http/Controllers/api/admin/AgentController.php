<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Notifications\AgentRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    public function add(Request $request)
    {
        $messages = [
            'fname.required' => 'First name is required.',
            'fname.max' => 'First name must not exceed :max characters.',
            'lname.required' => 'Last name is required.',
            'lname.max' => 'Last name must not exceed :max characters.',
            'country_code.required' => 'Country code is required.',
        ];

        $validator = Validator::make($request->all(), [
            'fname'     => ['required', 'string', 'max:255'],
            'lname'     => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')],
            'country_code' => ['required'],
            'phone'     => ['required', 'numeric', 'digits:10', Rule::unique('users')],
            'password'  => ['required', 'string', 'min:8'],
            'address'   => ['required', 'string', 'max:255'],
            'photo'     => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'logo'      => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    => $validator->errors(),
            ], 400);
        } 

        try {
            $name = $request->fname. ' '. $request->lname;
            $fname = $request->fname;
            $lname = $request->lname;
            $email = $request->email;
            $country_code = $request->country_code;
            $phone = $request->phone;
            $address = $request->address;
            $password = $request->password;

            $data = [
                'fname'     => $fname,
                'lname'     => $lname,
                'email'     => $email,
                'country_code' => $country_code,
                'phone'     => $phone,
                'password'  => Hash::make($password),
                'address'   => $address,
            ];

            $file = $request->file('photo');
            if ($file) {
                $extension = $file->getClientOriginalExtension();
                $filename = time().'.'.$extension;
                $image_url = $file->move('assets/uploads/agent-photos/', $filename);
                $data['photo'] = $image_url;
            }

            $logo = $request->file('logo'); 
            if ($logo) {
                $extension = $logo->getClientOriginalExtension();
                $logo_name = time().'.'.$extension;
                $logo_url = $logo->move('assets/uploads/agent-logos/', $logo_name);
                $data['logo'] = $logo_url; 
            }

            $agent = User::create($data);

            if ($agent) {
                $agent->notify(new AgentRegistration($name, $email, $password));

                $message = [
                    'title' => trans('msg.notification.agent_registered_title'),
                    'message' => trans('msg.notification.agent_registered_message', ['name' => $name]),
                ];

                $admin = Admin::first();
                $admin->notify(new AdminNotification($message));

                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.add.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.add.failed'),
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

    public function view(Request $request){
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
            $agent = User::where('id', '=', $request->agent_id)->first();
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

    public function update(Request $request) {
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
            $agent = User::where('id', '=', $request->agent_id)->first();
            if (!$agent) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.update.not-found'),
                ], 404);
            }
    
            $data = [
                'fname'     => $request->input('fname', $agent->fname),
                'lname'     => $request->input('lname', $agent->lname),
                'email'     => $request->input('email', $agent->email),
                'country_code' => $request->input('country_code', $agent->country_code),
                'phone'     => $request->input('phone', $agent->phone),
                'address'   => $request->input('address', $agent->address),
            ];
    
            $password = $request->input('password');
            if (!empty($password)) {
                $data['password'] = Hash::make($password);
            }
    
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
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.update.success'),
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
}
