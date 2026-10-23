<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\ContractDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_accounts_and_sample_contracts_are_seeded()
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'supervisor@mail.com',
            'name' => 'Sinta Wijaya',
            'role' => 'supervisor',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'staff@mail.com',
            'name' => 'Raka Putra',
            'role' => 'staff',
        ]);

        $staff = \App\Models\User::where('email', 'staff@mail.com')->first();
        $supervisor = \App\Models\User::where('email', 'supervisor@mail.com')->first();
        $this->assertTrue(Hash::check('staff123', $staff->password));
        $this->assertTrue(Hash::check('supervisor', $supervisor->password));

        $this->assertDatabaseHas('contracts', [
            'number' => 'INV/10/04',
            'subject' => 'Sewa kapasitas VSAT site Balikpapan',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('contracts', [
            'number' => 'INV/10/03',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('contracts', [
            'number' => 'INV/10/02',
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('contracts', [
            'number' => 'INV/09/18',
            'status' => 'rejected',
        ]);
        $this->assertDatabaseHas('contracts', [
            'number' => 'INV/09/14',
            'status' => 'approved',
        ]);

        $this->assertSame(8, Contract::where('status', 'pending')->count());
        $this->assertSame(14, Contract::where('status', 'approved')->whereBetween('approved_at', ['2026-10-01 00:00:00', '2026-10-31 23:59:59'])->count());
        $this->assertSame(3, Contract::where('status', 'rejected')->whereBetween('approved_at', ['2026-10-01 00:00:00', '2026-10-31 23:59:59'])->count());
        $this->assertSame(5, ContractDocument::whereDate('created_at', now()->toDateString())->count());

        $pdf = ContractDocument::where('original_name', 'Kontrak_VSAT_Balikpapan.pdf')->first();
        $this->assertNotNull($pdf);
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get($pdf->path));

        $this->post('/login', [
            'email' => 'staff@mail.com',
            'password' => 'staff123',
        ])->assertRedirect(route('dashboard'));
    }
}
