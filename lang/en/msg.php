<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Message Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during Message for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'localization' => 'language header is required',
    'validation'   => 'Validation Failed!',
    'error'        => 'Something went wrong, please try again...',
     
    'jwt' => [
        'TokenNotSet' => 'Bearer Token Not Set',
        'InvalidToken' => 'Invalid Bearer Token',
        'expiredToken' => 'Bearer Token Expired!',
        'TokenNotFound' => 'Bearer Token Not Found'
    ],

    'email' => [
        'agent_credentials' => [
            'subject' => 'Your Agent Credentials',
            'greeting' => 'Hello :name!',
            'email' => 'Your Email: :email',
            'password' => 'Your Password: :password',
            'keep_secure' => 'Please keep your credentials secure.',
            'login' => 'Login',
            'thank_you' => 'Thank you for using our service!',
        ],

        'reset-password' => [
            'subject' => 'Password Reset Successful',
            'greeting' => 'Hello :name!',
            'message' => 'Your password has been reset successfully.',
            'password' => 'Your new password is: :password',
            'keep_secure' => 'Please keep your credentials secure.',
            'login' => 'Login',
            'thank_you' => 'Thank you for using our service!',
        ],
    ],

    'notification' => [
        'agent_registered_title' => 'Agent Registration',
        'agent_registered_message' => 'Agent :name registered successfully.',
        'password_reset_request' => ':email have requested to reset his/her password',
        'password_reset_title' => 'Reset Password Request',

    ],

    'reset-password' => [
        // 'success' => 'Password Reset Successfully',
        // 'failed'  => 'Password Reset Failed',
        'success' => 'Password Reset Request Sent, Please check your email to get the new Passsword',
        'failed'  => 'Failed to Send Password Reset Request, Please try again...',
    ],

    'list' => [
        'success' => 'List Fetched Successfully',
        'failed'  => 'No Data Found',
    ],

    'add' => [
        'success' => 'Details Added Successfully',
        'failed'  => 'Add Failed',
    ],

    'delete' => [
        'success' => 'Record Deleted Successfully',
        'failed'  => 'Delete Failed',
    ],

    'update' => [
        'success' => 'Details Updated Successfully',
        'failed'  => 'Update Failed',
        'not-found' => 'User Not Found',
    ],

    'booking' => [
        'success' => 'Booking Successful',
        'failed'  => 'Booking Failed',
    ],

    'booking-cancel' => [
        'success' => 'Booking Cancelled',
        'failed'  => 'Booking Cancellation Failed',
    ],

    'detail' => [
        'success' => 'Details Fetched Successfully',
        'failed'  => 'Details Not Found',
        'not-found' => ':entity not found',
        'inactive' => 'Account blocked by Admin',
    ],   

    'login' => [
        'success' => 'Login Successful',
        'failed'  => 'Login Failed',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'Password Does Not Match!',
        'inactive' => 'Account blocked by Admin',
        'not-verified' => 'Email not Verified, please verify it first...',
        'not-social' => 'Unable to Find Social Account',
        'invalid-social' => 'Social Id Does Not Match, Please try again...',
        'invalid-email' => 'Invalid Email Address',
    ],

    'change-password' => [
        'success' => 'Password Updated Successfully',
        'failed'  => 'Unable to update Password, please try again...',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'Old Password Does Not Match!',
        'inactive' => 'Account blocked by Admin',
        'not-verified' => 'Email not Verified, please verify it first...'
    ],

    'change-status' => [
        'success' => 'Status Updated Successfully',
        'failed'  => 'Unable to update status, please try again...',
        'not-found' => ':entity is not found',        
        'inactive' => 'Account blocked by Admin',
        'not-verified' => 'Email not Verified, please verify it first...'
    ],
];
