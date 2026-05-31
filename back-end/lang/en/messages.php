<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Messages
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'registered' => 'Registration successful',
        'login_success' => 'Login successful',
        'logout_success' => 'Logged out successfully',
        'token_refreshed' => 'Token has been refreshed',
        'invalid_credentials' => 'Invalid credentials',
        'user_not_found' => 'User not found',
        'unauthorized' => 'You do not have permission to access this resource',
        'token_expired' => 'Token has expired',
        'token_invalid' => 'Invalid token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Messages
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'name_required' => 'Name is required',
        'name_max' => 'Name must not exceed 255 characters',
        'email_required' => 'Email is required',
        'email_invalid' => 'Invalid email address',
        'email_unique' => 'Email has already been taken',
        'password_required' => 'Password is required',
        'password_min' => 'Password must be at least 8 characters',
        'password_confirmed' => 'Password confirmation does not match',
        'username_required' => 'Username is required',
        'failed' => 'Validation failed',
    ],

    /*
    |--------------------------------------------------------------------------
    | General Messages
    |--------------------------------------------------------------------------
    */
    'general' => [
        'success' => 'Success',
        'error' => 'An error occurred',
        'not_found' => 'Not found',
        'unauthorized' => 'Unauthorized',
        'server_error' => 'Internal server error',
        'created' => 'Created successfully',
        'updated' => 'Updated successfully',
        'deleted' => 'Deleted successfully',
    ],

    /*
    |--------------------------------------------------------------------------
    | Task Messages
    |--------------------------------------------------------------------------
    */
    'task' => [
        'created' => 'Task created successfully',
        'updated' => 'Task updated successfully',
        'deleted' => 'Task deleted successfully',
        'completed' => 'Task completed',
        'not_found' => 'Task not found',
    ],

    /*
    |--------------------------------------------------------------------------
    | Office VNPT Messages
    |--------------------------------------------------------------------------
    */
    'office' => [
        'login_failed' => 'Login failed',
        'connection_error' => 'Connection error',
        'request_failed' => 'Request failed with status :status',
    ],

    /*
    |--------------------------------------------------------------------------
    | VNPT Credential Messages
    |--------------------------------------------------------------------------
    */
    'vnpt' => [
        'credential_saved' => 'VNPT credentials saved',
        'credential_deleted' => 'VNPT credentials deleted',
        'not_configured' => 'VNPT account not configured',
    ],
];
