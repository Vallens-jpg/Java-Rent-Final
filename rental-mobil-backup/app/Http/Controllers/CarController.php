<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::where('status', 'available')->get();
        return view('cars.index', compact('cars'));
    }

    public function show(Car $car)
    {
        return view('cars.show', compact('car'));
    }

    // --- Admin Methods ---
    public function create()
    {
        // return view('admin.cars.create');
    }

    public function store(Request $request)
    {
        // Store logic
    }

    public function edit(Car $car)
    {
        // return view('admin.cars.edit', compact('car'));
    }

    public function update(Request $request, Car $car)
    {
        // Update logic
    }

    public function destroy(Car $car)
    {
        // Delete logic
    }
}
