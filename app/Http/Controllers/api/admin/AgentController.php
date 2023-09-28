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
        ];

        $validator = Validator::make($request->all(), [
            'fname'     => ['required', 'string', 'max:255'],
            'lname'     => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users')],
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
            $phone = $request->phone;
            $address = $request->address;
            $password = $request->password;

            $data = [
                'fname'     => $fname,
                'lname'     => $lname,
                'email'     => $email,
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
}
