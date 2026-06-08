<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function create($carId)
    {
        $car = \App\Models\Car::find($carId);

        if (!$car) {
            $car = new \App\Models\Car([
                'id' => $carId,
                'brand' => 'Innova Reborn (Simulasi)',
                'size' => 'Large',
                'transmission' => 'Manual',
                'plate_number' => 'AD 1945 WW',
                'price_per_hour' => 100000,
                'status' => 'available',
            ]);
        }

        return view('rentals.create', compact('car'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'duration' => 'required|numeric',
            'phone' => 'required|string',
            'ktp' => 'required|string',
            'address' => 'required|string',
        ]);

        $userId = auth()->id() ?? 1; // Fallback to 1 if not logged in
        $user = \App\Models\User::find($userId);
        if ($user) {
            $user->phone = $request->phone;
            $user->nik = $request->ktp;
            $user->address = $request->address;
            $user->save();
        }

        $car = \App\Models\Car::findOrFail($request->car_id);
        
        // Hitung harga di backend agar aman dan menghindari error validasi
        $total = $car->price_per_hour * 24 * $request->duration;
        if ($request->duration == 7) {
            $total = $total * 0.85; // Diskon promo 15%
        }
        
        $rental = new Rental();
        $rental->user_id = $userId;
        $rental->car_id = $car->id;
        $rental->start_time = now();
        $rental->end_time = now()->addDays((int) $request->duration);
        $rental->total_price = $total;
        $rental->status = 'pending';
        $rental->save();

        return redirect()->route('rentals.payment', $rental->id);
    }

    public function payment($id)
    {
        $rental = \App\Models\Rental::find($id);
        
        // Simulasi fallback
        if (!$rental) {
            abort(404, 'Pesanan tidak ditemukan');
        }

        return view('rentals.payment', compact('rental'));
    }

    public function confirm($id)
    {
        // Render halaman menunggu validasi
        return view('rentals.waiting', ['id' => $id]);
    }

    public function extend($id)
    {
        $rental = \App\Models\Rental::with('car')->find($id);
        
        if (!$rental) {
            abort(404, 'Pesanan tidak ditemukan');
        }

        return view('rentals.extend', compact('rental'));
    }

    public function submitExtend(Request $request, $id)
    {
        $rental = \App\Models\Rental::with('car')->find($id);
        if (!$rental || $rental->user_id != auth()->id()) {
            abort(403);
        }

        $days = (int) $request->extend_days;
        if ($days <= 0) $days = 1;

        // Harga dari mobil
        $pricePerDay = ($rental->car->price_per_hour ?? 50000) * 24;
        $cost = $pricePerDay * $days;

        // Simpan hari perpanjangan tapi JANGAN update end_time dulu
        $rental->extension_days = $days;
        $rental->extension_status = 'unpaid';
        $rental->save();

        return view('rentals.extend_payment', compact('rental', 'days', 'cost'));
    }

    public function payExtend($id)
    {
        $rental = \App\Models\Rental::find($id);
        if ($rental && $rental->user_id == auth()->id()) {
            $rental->extension_status = 'pending_verification';
            $rental->notification_dismissed = false;
            $rental->save();
            return redirect()->route('dashboard')->with('success', 'Pembayaran perpanjangan sedang diverifikasi oleh admin.');
        }
        return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
    }

    public function penalty($id)
    {
        $rental = \App\Models\Rental::find($id);
        
        if (!$rental) {
            $rental = new \App\Models\Rental([
                'id' => $id,
            ]);
            $rental->setRelation('car', new \App\Models\Car([
                'brand' => 'Innova Reborn',
                'size' => 'Large',
            ]));
        }

        return view('rentals.penalty', compact('rental'));
    }

    public function show(Rental $rental)
    {
        return view('rentals.show', compact('rental'));
    }

    // --- Admin Methods ---
    public function adminIndex()
    {
        $rentals = Rental::with(['user', 'car'])->latest()->get();
        return view('admin.rentals.index', compact('rentals'));
    }

    public function updateStatus(Request $request, Rental $rental)
    {
        // Update status logic
    }

    public function apiCheckStatus($id)
    {
        $rental = \App\Models\Rental::find($id);
        if ($rental) {
            return response()->json([
                'status' => $rental->status,
                'penalty_status' => $rental->penalty_status,
                'extension_status' => $rental->extension_status
            ]);
        }
        return response()->json(['status' => 'not_found', 'penalty_status' => null, 'extension_status' => null]);
    }

    public function dismissNotification($id)
    {
        $rental = \App\Models\Rental::find($id);
        if ($rental && $rental->user_id == auth()->id()) {
            $rental->notification_dismissed = true;
            $rental->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }

    public function payPenalty($id)
    {
        $rental = \App\Models\Rental::find($id);
        if ($rental && $rental->user_id == auth()->id()) {
            $rental->penalty_status = 'pending_verification';
            $rental->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 403);
    }
}
