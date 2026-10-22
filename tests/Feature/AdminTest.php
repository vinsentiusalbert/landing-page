<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_can_be_repeated_and_account_can_login(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->assertDatabaseCount('users', 1);
        $user = User::where('email', 'admin@prismadana.test')->firstOrFail();
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check('123456', $user->password));
        $this->post('/admin/login', ['email' => 'admin@prismadana.test', 'password' => '123456'])
            ->assertSessionHasNoErrors()->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    private function admin(): User
    {
        return User::factory()->create(['email' => 'admin@example.test', 'is_admin' => true]);
    }

    private function registration(array $data = []): Registration
    {
        return Registration::create(array_merge([
            'nama' => 'Pendaftar Uji', 'no_hp' => '081234567890',
            'jenis_usaha' => 'Kuliner', 'pinjaman_diajukan' => '25000000',
        ], $data));
    }

    public function test_guests_cannot_access_list_details_or_export(): void
    {
        $registration = $this->registration();
        foreach (['/admin', '/admin/export', '/admin/pendaftar/'.$registration->id] as $url) {
            $this->get($url)->assertRedirect('/admin/login')->assertDontSee('Pendaftar Uji');
        }
        $this->get('/admin/login')->assertOk()->assertSee('Masuk ke admin');
    }

    public function test_regular_users_are_forbidden_even_with_authenticated_sessions(): void
    {
        $this->actingAs(User::factory()->create());
        $registration = $this->registration();
        foreach (['/admin', '/admin/export', '/admin/pendaftar/'.$registration->id] as $url) {
            $this->get($url)->assertForbidden();
        }
    }

    public function test_admin_can_login_and_logout(): void
    {
        $admin = $this->admin();
        $this->post('/admin/login', ['email' => 'ADMIN@example.test', 'password' => 'password'])
            ->assertSessionHasNoErrors()->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
        $this->get('/admin/login')->assertRedirect('/admin');
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->assertGuest();
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_invalid_credentials_do_not_login_or_flash_password(): void
    {
        $this->admin();
        $this->from('/admin/login')->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'incorrect'])
            ->assertRedirect('/admin/login')->assertSessionHasErrors('email')->assertSessionMissing('_old_input.password');
        $this->assertGuest();
    }

    public function test_regular_user_cannot_login_to_admin(): void
    {
        $user = User::factory()->create();
        $this->post('/admin/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_failed_logins_are_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'incorrect'])->assertSessionHasErrors('email');
        }
        $this->post('/admin/login', ['email' => 'admin@example.test', 'password' => 'incorrect'])
            ->assertSessionHasErrors('email', fn ($message) => str_contains($message, 'Terlalu banyak percobaan'));
        $this->assertGuest();
    }

    public function test_admin_sees_records_and_uncached_private_responses(): void
    {
        $this->registration();
        $response = $this->actingAs($this->admin())->get('/admin')->assertOk()
            ->assertSee('Pendaftar Uji')->assertSee('081234567890')->assertSee('25.000.000');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_empty_dashboard_renders(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk()->assertSee('Belum ada pendaftaran.');
    }

    public function test_search_and_date_filters_work_together(): void
    {
        $match = $this->registration(['nama' => 'Target Usaha']);
        $match->forceFill(['created_at' => '2026-10-20 12:00:00'])->save();
        $old = $this->registration(['nama' => 'Target Lama']);
        $old->forceFill(['created_at' => '2026-09-01 12:00:00'])->save();
        $this->registration(['nama' => 'Nama Berbeda']);
        $this->actingAs($this->admin())->get('/admin?q=Target&from=2026-10-20&to=2026-10-20')
            ->assertOk()->assertSee('Target Usaha')->assertDontSee('Target Lama')->assertDontSee('Nama Berbeda');
    }

    public function test_invalid_date_filter_is_rejected(): void
    {
        $this->actingAs($this->admin())->from('/admin')->get('/admin?from=2026-10-20&to=2026-09-01')
            ->assertRedirect('/admin')->assertSessionHasErrors('to');
    }

    public function test_pagination_is_used_and_keeps_search(): void
    {
        for ($i = 0; $i < 16; $i++) {
            $this->registration(['nama' => 'Nama '.$i]);
        }
        $this->actingAs($this->admin())->get('/admin?q=Nama')->assertOk()
            ->assertSee('Nama 15')->assertDontSee('Nama 0')->assertSee('q=Nama', false)->assertSee('page=2', false);
        $this->get('/admin?q=Nama&page=2')->assertOk()->assertSee('Nama 0')->assertDontSee('Nama 15');
    }

    public function test_details_show_zero_values_and_escape_user_input(): void
    {
        $registration = $this->registration(['nama' => '<script>alert(1)</script>', 'pinjaman_eksisting' => 0, 'lama_usaha_thn' => 0]);
        $this->actingAs($this->admin())->get('/admin/pendaftar/'.$registration->id)->assertOk()
            ->assertSee('Pinjaman Berjalan')->assertSee('Rp 0,00')->assertSee('0 tahun')
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/admin/pendaftar/99999')->assertNotFound();
    }

    public function test_export_respects_filters_and_escapes_spreadsheet_formulas(): void
    {
        $this->registration(['nama' => '=SUM(1,1)', 'no_hp' => '+6281234567890', 'alamat' => "Alamat, dengan koma\ndan baris baru"]);
        $this->registration(['nama' => 'Tidak diekspor', 'jenis_usaha' => 'Jasa']);
        $response = $this->actingAs($this->admin())->get('/admin/export?q=Kuliner')->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString("'=SUM(1,1)", $csv);
        $this->assertStringContainsString("'+6281234567890", $csv);
        $this->assertStringContainsString('Alamat, dengan koma', $csv);
        $this->assertStringNotContainsString('Tidak diekspor', $csv);
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_create_admin_command_hashes_password_and_sets_role(): void
    {
        $this->artisan('admin:create', ['email' => 'owner@example.test', '--name' => 'Admin Uji'])
            ->expectsQuestion('Kata sandi (minimal 12 karakter)', 'SandiUji12345!')
            ->expectsQuestion('Ulangi kata sandi', 'SandiUji12345!')
            ->assertSuccessful();
        $user = User::where('email', 'owner@example.test')->firstOrFail();
        $this->assertTrue($user->is_admin);
        $this->assertTrue(Hash::check('SandiUji12345!', $user->password));
    }

    public function test_create_admin_command_rejects_mismatched_passwords(): void
    {
        $this->artisan('admin:create', ['email' => 'owner@example.test'])
            ->expectsQuestion('Kata sandi (minimal 12 karakter)', 'SandiUji12345!')
            ->expectsQuestion('Ulangi kata sandi', 'Berbeda12345!')
            ->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_create_admin_command_does_not_promote_existing_users(): void
    {
        $user = User::factory()->create();
        $this->artisan('admin:create', ['email' => $user->email])->assertFailed();
        $this->assertFalse($user->fresh()->is_admin);
    }
}
