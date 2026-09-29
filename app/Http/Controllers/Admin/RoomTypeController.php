<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->orderBy('base_price')->get();
        return view('admin.room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('admin.room-types.form', ['roomType' => new RoomType(), 'isEdit' => false]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'base_price'  => 'required|numeric|min:0',
            'capacity'    => 'required|integer|min:1|max:20',
            'area'        => 'nullable|numeric|min:0',
            'image'       => 'nullable|url|max:500',
        ]);

        RoomType::create($validated);
        return redirect()->route('admin.room-types.index')->with('success', "Loại phòng \"{$validated['name']}\" đã được tạo.");
    }

    public function edit(RoomType $roomType)
    {
        return view('admin.room-types.form', ['roomType' => $roomType, 'isEdit' => true]);
    }

    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'base_price'  => 'required|numeric|min:0',
            'capacity'    => 'required|integer|min:1|max:20',
            'area'        => 'nullable|numeric|min:0',
            'image'       => 'nullable|url|max:500',
        ]);

        $roomType->update($validated);
        return redirect()->route('admin.room-types.index')->with('success', "Loại phòng \"{$roomType->name}\" đã được cập nhật.");
    }

    public function destroy(RoomType $roomType)
    {
        if ($roomType->rooms()->exists()) {
            return back()->with('error', 'Không thể xoá loại phòng đang được sử dụng.');
        }
        $name = $roomType->name;
        $roomType->delete();
        return redirect()->route('admin.room-types.index')->with('success', "Loại phòng \"{$name}\" đã được xoá.");
    }
}
