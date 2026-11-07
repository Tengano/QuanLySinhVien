<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mật khẩu mặc định
    |--------------------------------------------------------------------------
    |
    | Dùng chung cho 2 chỗ:
    | - Admin bấm "Reset" ở màn Quản trị tài khoản (users.reset).
    | - Tự tạo tài khoản đăng nhập khi thêm sinh viên (tick "Tạo tài khoản").
    |
    | Mật khẩu được băm bcrypt nhờ cast "hashed" của model NguoiDung.
    | Có thể ghi đè bằng biến môi trường QLSV_MAT_KHAU_MAC_DINH.
    |
    */

    'mat_khau_mac_dinh' => env('QLSV_MAT_KHAU_MAC_DINH', '11111111'),

];
