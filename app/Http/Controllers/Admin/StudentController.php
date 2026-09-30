<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Danh sách sinh viên và tra cứu tình trạng thẻ.
     */
    public function index(Request $request)
    {
        $today = Carbon::today()->toDateString();

        $query = Student::query();

        // Tìm kiếm theo từ khoá (mã SV, tên, trường, sđt)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('university', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo tình trạng thẻ
        if ($filter = $request->input('card_status')) {
            if ($filter === 'valid') {
                $query->where('status', 'active')
                      ->where('card_expiry_date', '>=', $today);
            } elseif ($filter === 'expired') {
                $query->where(function ($q) use ($today) {
                    $q->where('card_expiry_date', '<', $today)
                      ->orWhere('status', '!=', 'active');
                });
            }
        }

        $students = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Thống kê nhanh
        $stats = [
            'total'   => Student::count(),
            'valid'   => Student::where('status', 'active')->where('card_expiry_date', '>=', $today)->count(),
            'expired' => Student::where('card_expiry_date', '<', $today)->orWhere('status', '!=', 'active')->count(),
        ];

        return view('admin.students.index', compact('students', 'stats'));
    }

    /**
     * Giao diện thêm sinh viên mới.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Lưu sinh viên mới vào cơ sở dữ liệu.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_code'     => 'required|string|max:50|unique:students,student_code',
            'name'             => 'required|string|max:255',
            'university'       => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'card_issue_date'  => 'nullable|date',
            'card_expiry_date' => 'required|date',
            'discount_percent' => 'required|integer|min:0|max:100',
            'status'           => 'required|in:active,graduated,locked',
            'notes'            => 'nullable|string|max:1000',
        ], [
            'student_code.required'     => 'Vui lòng nhập mã sinh viên.',
            'student_code.unique'       => 'Mã sinh viên này đã tồn tại trong hệ thống.',
            'name.required'             => 'Vui lòng nhập họ và tên sinh viên.',
            'card_expiry_date.required' => 'Vui lòng chọn ngày hết hạn thẻ sinh viên.',
            'discount_percent.required' => 'Vui lòng nhập mức giảm giá (%).',
            'discount_percent.min'      => 'Mức giảm giá không được âm.',
            'discount_percent.max'      => 'Mức giảm giá tối đa là 100%.',
        ]);

        $student = Student::create($validated);

        return redirect()->route('admin.students.index')
                         ->with('success', "Đã thêm sinh viên {$student->name} ({$student->student_code}) thành công!");
    }

    /**
     * Giao diện chỉnh sửa thông tin sinh viên / gia hạn thẻ.
     */
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Cập nhật thông tin sinh viên.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_code'     => 'required|string|max:50|unique:students,student_code,' . $student->id,
            'name'             => 'required|string|max:255',
            'university'       => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'phone'            => 'nullable|string|max:20',
            'card_issue_date'  => 'nullable|date',
            'card_expiry_date' => 'required|date',
            'discount_percent' => 'required|integer|min:0|max:100',
            'status'           => 'required|in:active,graduated,locked',
            'notes'            => 'nullable|string|max:1000',
        ], [
            'student_code.required'     => 'Vui lòng nhập mã sinh viên.',
            'student_code.unique'       => 'Mã sinh viên này đã được sử dụng.',
            'name.required'             => 'Vui lòng nhập họ tên sinh viên.',
            'card_expiry_date.required' => 'Vui lòng chọn ngày hết hạn thẻ.',
        ]);

        $student->update($validated);

        return redirect()->route('admin.students.index')
                         ->with('success', "Đã cập nhật thông tin sinh viên {$student->name} thành công!");
    }

    /**
     * Xóa sinh viên.
     */
    public function destroy(Student $student)
    {
        $name = $student->name;
        $student->delete();

        return redirect()->route('admin.students.index')
                         ->with('success', "Đã xóa sinh viên {$name} khỏi hệ thống!");
    }
}
