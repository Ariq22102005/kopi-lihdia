<?php

namespace Tests\Feature;

use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesShopData;
use Tests\TestCase;

class ReservationTest extends TestCase
{
    use RefreshDatabase, CreatesShopData;

    private function payload(array $override = []): array
    {
        $cfg = config('kopilihdia.reservation');

        return array_merge([
            'name'   => 'Budi',
            'phone'  => '081234567890',
            'date'   => now($cfg['timezone'])->addDays(2)->toDateString(),
            'time'   => Reservation::slots()[2],
            'guests' => 4,
            'notes'  => 'Dekat jendela',
        ], $override);
    }

    public function test_guest_can_make_a_reservation(): void
    {
        $this->post('/reservasi', $this->payload())
            ->assertRedirect(route('home') . '#reservasi')
            ->assertSessionHas('reservation');

        $this->assertDatabaseHas('reservations', ['name' => 'Budi', 'guests' => 4, 'status' => 'pending']);
    }

    public function test_guest_count_cannot_exceed_the_limit(): void
    {
        $max = config('kopilihdia.reservation.max_guests');

        $this->post('/reservasi', $this->payload(['guests' => $max + 1]))->assertSessionHasErrors('guests');
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_time_must_be_one_of_the_available_slots(): void
    {
        $this->post('/reservasi', $this->payload(['time' => '03:00']))->assertSessionHasErrors('time');
    }

    public function test_date_cannot_be_in_the_past(): void
    {
        $date = now(config('kopilihdia.reservation.timezone'))->subDay()->toDateString();

        $this->post('/reservasi', $this->payload(['date' => $date]))->assertSessionHasErrors('date');
    }

    public function test_only_admin_can_manage_reservations(): void
    {
        $this->post('/reservasi', $this->payload());
        $reservation = Reservation::first();

        $this->actingAs($this->makeCustomer())->get('/admin/reservations')->assertForbidden();

        $this->actingAs($this->makeAdmin())->get('/admin/reservations')->assertOk()->assertSee($reservation->code);

        $this->actingAs($this->makeAdmin())
            ->patch("/admin/reservations/{$reservation->id}/status", ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertSame('confirmed', $reservation->fresh()->status);
    }

    public function test_slot_cannot_be_booked_beyond_its_capacity(): void
    {
        config(['kopilihdia.reservation.capacity_per_slot' => 10]);

        $this->post('/reservasi', $this->payload(['guests' => 8]))->assertSessionHas('reservation');

        // Tinggal 2 kursi di jam yang sama
        $this->post('/reservasi', $this->payload(['name' => 'Sari', 'guests' => 4]))->assertSessionHasErrors('time');
        $this->assertDatabaseCount('reservations', 1);

        $this->post('/reservasi', $this->payload(['name' => 'Rina', 'guests' => 2]))->assertSessionHas('reservation');
        $this->assertDatabaseCount('reservations', 2);
    }

    public function test_cancelled_reservation_frees_the_seats(): void
    {
        config(['kopilihdia.reservation.capacity_per_slot' => 10]);

        $this->post('/reservasi', $this->payload(['guests' => 10]));
        Reservation::first()->update(['status' => Reservation::STATUS_CANCELLED]);

        $this->post('/reservasi', $this->payload(['name' => 'Sari', 'guests' => 6]))->assertSessionHas('reservation');
    }

    public function test_availability_endpoint_reports_seats_left(): void
    {
        config(['kopilihdia.reservation.capacity_per_slot' => 10]);
        $payload = $this->payload(['guests' => 7]);
        $this->post('/reservasi', $payload);

        $this->getJson('/reservasi/ketersediaan?date=' . $payload['date'])
            ->assertOk()
            ->assertJsonPath('left.' . $payload['time'], 3);
    }
}
