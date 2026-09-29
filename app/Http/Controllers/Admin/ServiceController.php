<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->paginate(20);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.form', ['service' => new Service(), 'isEdit' => false]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        Service::create($validated);
        return redirect()->route('admin.services.index')->with('success', "Dịch vụ \"{$validated['name']}\" đã được tạo.");
    }

    public function edit(Service $service)
    {
        return view('admin.services.form', ['service' => $service, 'isEdit' => true]);
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:200',
            'description' => 'nullable|string|max:500',
            'price'       => 'required|numeric|min:0',
            'is_active'   => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        $service->update($validated);
        return redirect()->route('admin.services.index')->with('success', "Dịch vụ \"{$service->name}\" đã được cập nhật.");
    }

    public function destroy(Service $service)
    {
        $name = $service->name;
        $service->delete();
        return redirect()->route('admin.services.index')->with('success', "Dịch vụ \"{$name}\" đã được xoá.");
    }
}
