<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $cfg = config('kopilihdia.reservation');
        $now = now($cfg['timezone']);
        $today = $now->toDateString();
        $last = $now->copy()->addDays((int) $cfg['days_ahead'])->toDateString();

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:100'],
            'phone'  => ['required', 'string', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'date'   => ['required', 'date_format:Y-m-d', "after_or_equal:$today", "before_or_equal:$last"],
            'time'   => ['required', Rule::in(Reservation::slots())],
            'guests' => ['required', 'integer', 'min:1', 'max:' . $cfg['max_guests']],
            'notes'  => ['nullable', 'string', 'max:300'],
        ], [
            'name.required'        => 'Nama wajib diisi.',
            'phone.required'       => 'Nomor telepon wajib diisi.',
            'phone.regex'          => 'Nomor telepon tidak valid.',
            'date.required'        => 'Pilih tanggal kedatangan.',
            'date.after_or_equal'  => 'Tanggal tidak boleh sebelum hari ini.',
            'date.before_or_equal' => "Reservasi hanya bisa untuk {$cfg['days_ahead']} hari ke depan.",
            'time.required'        => 'Pilih jam kedatangan.',
            'time.in'              => 'Jam yang dipilih tidak tersedia.',
            'guests.required'      => 'Isi jumlah orang.',
            'guests.min'           => 'Minimal 1 orang.',
            'guests.max'           => "Maksimal {$cfg['max_guests']} orang. Untuk rombongan lebih besar, hubungi kami langsung.",
            'notes.max'            => 'Catatan maksimal 300 karakter.',
        ]);

        // Jika memesan untuk hari ini, jamnya harus belum lewat
        if ($data['date'] === $today && $data['time'] <= $now->format('H:i')) {
            throw ValidationException::withMessages(['time' => 'Jam tersebut sudah lewat. Pilih jam lain.']);
        }

        // Cek kapasitas dan simpan dalam satu transaksi agar dua pemesan bersamaan tidak sama-sama lolos
        $reservation = DB::transaction(function () use ($data, $cfg, $request) {
            $taken = (int) Reservation::where('date', $data['date'])
                ->where('time', $data['time'])
                ->where('status', '!=', Reservation::STATUS_CANCELLED)
                ->lockForUpdate()
                ->sum('guests');

            $left = max(0, (int) $cfg['capacity_per_slot'] - $taken);

            if ($data['guests'] > $left) {
                throw ValidationException::withMessages([
                    'time' => $left > 0
                        ? "Jam ini tinggal {$left} kursi. Kurangi jumlah orang atau pilih jam lain."
                        : 'Jam ini sudah penuh. Pilih jam lain ya.',
                ]);
            }

            return Reservation::create([
                'code'    => Reservation::newCode(),
                'user_id' => $request->user()?->id,
                'name'    => $data['name'],
                'phone'   => $data['phone'],
                'date'    => $data['date'],
                'time'    => $data['time'],
                'guests'  => $data['guests'],
                'notes'   => $data['notes'] ?? null,
                'status'  => Reservation::STATUS_PENDING,
            ]);
        });

        return redirect()->to(route('home') . '#reservasi')->with('reservation', [
            'code'   => $reservation->code,
            'name'   => $reservation->name,
            'phone'  => $reservation->phone,
            'date'   => $reservation->date->translatedFormat('l, d F Y'),
            'time'   => $reservation->time,
            'guests' => $reservation->guests,
        ]);
    }

    /** Sisa kursi per jam untuk satu tanggal (dipakai form untuk menonaktifkan jam yang penuh). */
    public function availability(Request $request)
    {
        $data = $request->validate(['date' => ['required', 'date_format:Y-m-d']]);

        return response()->json(['left' => Reservation::seatsLeft($data['date'])]);
    }
}
