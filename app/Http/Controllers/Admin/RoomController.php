<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('roomType')->orderBy('room_number');

        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('room_type')) $query->where('room_type_id', $request->room_type);
        if ($request->filled('floor'))     $query->where('floor', $request->floor);

        $rooms     = $query->paginate(20);
        $roomTypes = RoomType::orderBy('name')->get();
        $floors    = Room::select('floor')->distinct()->orderBy('floor')->pluck('floor');

        return view('admin.rooms.index', compact('rooms', 'roomTypes', 'floors'));
    }

    public function create()
    {
        $roomTypes = RoomType::orderBy('name')->get();
        return view('admin.rooms.form', ['room' => new Room(), 'roomTypes' => $roomTypes, 'isEdit' => false]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number'   => 'required|string|unique:rooms,room_number',
            'room_type_id'  => 'required|exists:room_types,id',
            'floor'         => 'required|integer|min:1',
            'status'        => 'required|in:available,booked,occupied,maintenance,cleaning',
            'price_override' => 'nullable|numeric|min:0',
            'image'         => 'nullable|url|max:500',
        ]);

        Room::create($validated);
        return redirect()->route('admin.rooms.index')->with('success', "Phòng {$validated['room_number']} đã được tạo.");
    }

    public function edit(Room $room)
    {
        $roomTypes = RoomType::orderBy('name')->get();
        return view('admin.rooms.form', ['room' => $room, 'roomTypes' => $roomTypes, 'isEdit' => true]);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number'   => 'required|string|unique:rooms,room_number,' . $room->id,
            'room_type_id'  => 'required|exists:room_types,id',
            'floor'         => 'required|integer|min:1',
            'status'        => 'required|in:available,booked,occupied,maintenance,cleaning',
            'price_override' => 'nullable|numeric|min:0',
            'image'         => 'nullable|url|max:500',
        ]);

        $room->update($validated);
        return redirect()->route('admin.rooms.index')->with('success', "Phòng {$room->room_number} đã được cập nhật.");
    }

    public function destroy(Room $room)
    {
        if ($room->bookings()->whereIn('status', ['checked_in', 'confirmed', 'pending'])->exists()) {
            return back()->with('error', 'Không thể xoá phòng đang có đặt phòng hoạt động.');
        }
        $number = $room->room_number;
        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', "Phòng {$number} đã được xoá.");
    }
}
