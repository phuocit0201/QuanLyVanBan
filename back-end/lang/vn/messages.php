<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Messages
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'registered' => 'Đăng ký thành công',
        'login_success' => 'Đăng nhập thành công',
        'logout_success' => 'Đăng xuất thành công',
        'token_refreshed' => 'Token đã được làm mới',
        'invalid_credentials' => 'Thông tin đăng nhập không hợp lệ',
        'user_not_found' => 'Người dùng không tìm thấy',
        'unauthorized' => 'Bạn không có quyền truy cập',
        'token_expired' => 'Token đã hết hạn',
        'token_invalid' => 'Token không hợp lệ',
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Messages
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'name_required' => 'Họ tên là bắt buộc',
        'name_max' => 'Họ tên không được vượt quá 255 ký tự',
        'email_required' => 'Email là bắt buộc',
        'email_invalid' => 'Email không hợp lệ',
        'email_unique' => 'Email đã được sử dụng',
        'password_required' => 'Mật khẩu là bắt buộc',
        'password_min' => 'Mật khẩu phải có ít nhất 8 ký tự',
        'password_confirmed' => 'Xác nhận mật khẩu không khớp',
        'username_required' => 'Tên đăng nhập là bắt buộc',
        'failed' => 'Xác thực thất bại',
    ],

    /*
    |--------------------------------------------------------------------------
    | General Messages
    |--------------------------------------------------------------------------
    */
    'general' => [
        'success' => 'Thành công',
        'error' => 'Đã xảy ra lỗi',
        'not_found' => 'Không tìm thấy',
        'unauthorized' => 'Không được phép',
        'server_error' => 'Lỗi máy chủ nội bộ',
        'created' => 'Tạo mới thành công',
        'updated' => 'Cập nhật thành công',
        'deleted' => 'Xóa thành công',
    ],

    /*
    |--------------------------------------------------------------------------
    | Task Messages
    |--------------------------------------------------------------------------
    */
    'task' => [
        'created' => 'Công việc đã được tạo',
        'updated' => 'Công việc đã được cập nhật',
        'deleted' => 'Công việc đã được xóa',
        'completed' => 'Công việc đã hoàn thành',
        'not_found' => 'Công việc không tìm thấy',
    ],

    /*
    |--------------------------------------------------------------------------
    | Office VNPT Messages
    |--------------------------------------------------------------------------
    */
    'office' => [
        'login_failed' => 'Đăng nhập thất bại',
        'connection_error' => 'Lỗi kết nối',
        'request_failed' => 'Yêu cầu thất bại với mã :status',
    ],

    /*
    |--------------------------------------------------------------------------
    | VNPT Credential Messages
    |--------------------------------------------------------------------------
    */
    'vnpt' => [
        'credential_saved' => 'Đã lưu thông tin đăng nhập VNPT',
        'credential_deleted' => 'Đã xóa thông tin đăng nhập VNPT',
        'not_configured' => 'Chưa cấu hình tài khoản VNPT',
    ],
];
