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
        'registration-subject' => 'Your Agent Credentials For Hotel Aggregator',
        'salutation' => 'Hello',
        'email' => 'Your Email',
        'password' => 'Your Password',
        'registration-msg' => 'Please keep your credentials secure.',
        'Login' => 'Login',
        'registration-thanks' => 'Thank you for using our service!'
    ],

    'list' => [
        'success' => 'List Fetched Successfully',
        'failed'  => 'No Data Found',
    ],

    'add' => [
        'success' => 'Details Added Successfully',
        'failed'  => 'Add Failed',
    ],

    'update' => [
        'success' => 'Details Updated Successfully',
        'failed'  => 'Update Failed',
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
        'failed'  => 'No Data Found',
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
];
