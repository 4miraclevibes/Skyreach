<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\ContractDocument;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ContractFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guides_render_for_an_authenticated_user()
    {
        $staff = $this->user('Raka Putra', 'staff@mail.com', 'staff123', 'staff');

        $this->get('/guides')->assertRedirect('/login');
        $this->actingAs($staff)->get('/guides')->assertOk()->assertSee('Panduan pemakaian');
        $this->actingAs($staff)->get('/guides/technical')
            ->assertOk()
            ->assertSee('Laravel 8')
            ->assertSee('Fast setup')
            ->assertSee('MySQL');
    }

    public function test_guest_is_sent_to_login()
    {
        $this->get('/contracts')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('staff@mail.com');
    }

    public function test_staff_creates_and_supervisor_decides()
    {
        Storage::fake('local');
        $staff = $this->user('Raka Putra', 'staff@mail.com', 'staff123', 'staff');
        $supervisor = $this->user('Sinta Wijaya', 'supervisor@mail.com', 'supervisor', 'supervisor');

        $this->actingAs($supervisor)->get(route('contracts.create'))->assertForbidden();
        $this->actingAs($supervisor)->post(route('contracts.store'), [
            'subject' => 'Tidak boleh',
            'activation_date' => '2026-10-22',
        ])->assertForbidden();

        $this->actingAs($staff)->post(route('contracts.store'), [
            'subject' => 'Sewa kapasitas VSAT site Pontianak',
            'activation_date' => '2026-10-22',
            'documents' => [UploadedFile::fake()->image('antena.jpg')],
        ])->assertRedirect();

        $contract = Contract::first();
        $this->assertNotNull($contract);
        $this->assertMatchesRegularExpression('/^INV\/\d{2}\/\d{2}$/', $contract->number);
        $this->assertSame(Contract::STATUS_PENDING, $contract->status);
        $this->assertSame(1, ContractDocument::count());

        $this->actingAs($staff)->get(route('contracts.index'))
            ->assertOk()
            ->assertSee('Buat kontrak')
            ->assertDontSee('Setujui');

        $this->actingAs($staff)->get(route('contracts.index', ['detail' => $contract->id]))
            ->assertOk()
            ->assertSee('Edit kontrak')
            ->assertDontSee('Tolak kontrak');

        $this->actingAs($staff)->put(route('contracts.update', $contract), [
            'subject' => 'Sewa kapasitas VSAT site Pontianak diperbarui',
            'activation_date' => '2026-10-23',
        ])->assertRedirect();
        $this->assertSame('Sewa kapasitas VSAT site Pontianak diperbarui', $contract->fresh()->subject);

        $this->actingAs($staff)->post(route('contracts.approve', $contract))->assertForbidden();
        $this->actingAs($staff)->delete(route('contracts.destroy', $contract))->assertForbidden();

        $this->actingAs($supervisor)->post(route('contracts.reject', $contract), [])
            ->assertSessionHasErrors('reason');
        $this->assertSame(Contract::STATUS_PENDING, $contract->fresh()->status);

        $this->actingAs($supervisor)->get(route('contracts.index'))
            ->assertOk()
            ->assertSee('Setujui')
            ->assertDontSee('Buat kontrak');

        $this->actingAs($supervisor)->post(route('contracts.approve', $contract))->assertRedirect();
        $contract->refresh();
        $this->assertSame(Contract::STATUS_APPROVED, $contract->status);
        $this->assertSame($supervisor->id, $contract->approved_by);

        $this->actingAs($staff)->get(route('contracts.edit', $contract))
            ->assertRedirect(route('contracts.index', ['detail' => $contract->id]));

        $this->actingAs($supervisor)->delete(route('contracts.destroy', $contract))->assertRedirect();
        $this->assertSame(0, Contract::count());
    }

    public function test_supervisor_reject_locks_the_contract()
    {
        $staff = $this->user('Raka Putra', 'staff@mail.com', 'staff123', 'staff');
        $supervisor = $this->user('Sinta Wijaya', 'supervisor@mail.com', 'supervisor', 'supervisor');

        $this->actingAs($staff)->post(route('contracts.store'), [
            'subject' => 'Relokasi antena site Sorong',
            'activation_date' => '2026-10-21',
        ]);
        $contract = Contract::first();

        $this->actingAs($supervisor)->post(route('contracts.reject', $contract), [
            'reason' => 'Lampiran NPWP buram, mohon unggah ulang.',
        ])->assertRedirect();

        $contract->refresh();
        $this->assertSame(Contract::STATUS_REJECTED, $contract->status);
        $this->assertSame('Lampiran NPWP buram, mohon unggah ulang.', $contract->rejection_reason);

        $this->actingAs($staff)->put(route('contracts.update', $contract), [
            'subject' => 'Diubah diam-diam',
            'activation_date' => '2026-10-22',
        ])->assertRedirect(route('contracts.index', ['detail' => $contract->id]));
        $this->assertSame('Relokasi antena site Sorong', $contract->fresh()->subject);
    }

    public function test_contract_numbers_increase_inside_the_month()
    {
        $staff = $this->user('Raka Putra', 'staff@mail.com', 'staff123', 'staff');

        $this->actingAs($staff)->post(route('contracts.store'), [
            'subject' => 'Kontrak pertama',
            'activation_date' => '2026-10-02',
        ]);
        $this->actingAs($staff)->post(route('contracts.store'), [
            'subject' => 'Kontrak kedua',
            'activation_date' => '2026-10-03',
        ]);

        $numbers = Contract::orderBy('id')->pluck('number')->all();
        $this->assertCount(2, $numbers);
        $this->assertSame(1, (int) substr($numbers[1], -2) - (int) substr($numbers[0], -2));
    }

    private function user($name, $email, $password, $role)
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $role,
        ]);
    }
}
