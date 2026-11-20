<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    public const STATUS_PENDING   = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING   => 'Menunggu',
        self::STATUS_CONFIRMED => 'Terkonfirmasi',
        self::STATUS_CANCELLED => 'Dibatalkan',
    ];

    protected $fillable = ['code', 'user_id', 'name', 'phone', 'date', 'time', 'guests', 'notes', 'status'];

    protected $casts = ['date' => 'date'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Daftar jam yang bisa dipesan, dibuat dari config/kopilihdia.php. */
    public static function slots(): array
    {
        $cfg = config('kopilihdia.reservation');
        $tz = $cfg['timezone'];

        $t = Carbon::createFromFormat('H:i', $cfg['open'], $tz);
        $end = Carbon::createFromFormat('H:i', $cfg['close'], $tz);

        $slots = [];
        while ($t->lt($end)) {
            $slots[] = $t->format('H:i');
            $t->addMinutes((int) $cfg['interval']);
        }

        return $slots;
    }

    /** Sisa kursi tiap jam pada tanggal tertentu: ['09:00' => 20, '10:00' => 6, ...] */
    public static function seatsLeft(string $date): array
    {
        $capacity = (int) config('kopilihdia.reservation.capacity_per_slot');

        $taken = self::whereDate('date', $date)
            ->where('status', '!=', self::STATUS_CANCELLED)
            ->selectRaw('time, SUM(guests) as total')
            ->groupBy('time')
            ->pluck('total', 'time');

        $left = [];
        foreach (self::slots() as $slot) {
            $left[$slot] = max(0, $capacity - (int) ($taken[$slot] ?? 0));
        }

        return $left;
    }

    public static function newCode(): string
    {
        do {
            $code = 'RSV-' . strtoupper(\Illuminate\Support\Str::random(6));
        } while (self::where('code', $code)->exists());

        return $code;
    }
}
