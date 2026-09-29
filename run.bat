@echo off
chcp 65001 > nul
title Radiant Hotel - Hệ thống Quản lý Khách sạn
echo ========================================================
echo        🏨 RADIANT HOTEL - HOTEL MANAGEMENT SYSTEM
echo ========================================================
echo.
echo Đang mở trình duyệt tới trang web...
start http://127.0.0.1:8000
echo.
echo Đang khởi động máy chủ Laravel tại http://127.0.0.1:8000 ...
echo (Nhấn Ctrl + C để dừng máy chủ bất cứ lúc nào)
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause
