<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $reservations = Reservation::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('code', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->query('status')))
            ->when($request->filled('date'), fn ($q) => $q->whereDate('date', $request->query('date')))
            ->orderByDesc('date')
            ->orderBy('time')
            ->paginate(12)
            ->withQueryString();

        $pendingCount = Reservation::where('status', Reservation::STATUS_PENDING)->count();

        return view('admin.reservations.index', compact('reservations', 'search', 'pendingCount'));
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Reservation::STATUSES))],
        ]);

        $reservation->update(['status' => $data['status']]);

        return back()->with('success', "Reservasi {$reservation->code} diubah menjadi " . strtolower(Reservation::STATUSES[$data['status']]) . '.');
    }
}
