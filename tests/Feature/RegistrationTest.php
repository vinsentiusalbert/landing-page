<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_contains_all_fields_and_options(): void
    {
        $response = $this->get('/')->assertOk();
        foreach (['nama', 'jenis_kelamin', 'tanggal_lahir', 'no_hp', 'jenis_usaha', 'lama_usaha_thn', 'omset_bulanan', 'pinjaman_eksisting', 'fasilitas_diajukan', 'pinjaman_diajukan', 'alamat'] as $field) {
            $response->assertSee('name="'.$field.'"', false);
        }
        foreach ([...Registration::BUSINESS_TYPES, ...Registration::FACILITIES] as $option) {
            $response->assertSee($option);
        }
    }

    public function test_complete_registration_is_saved_and_success_is_shown(): void
    {
        $data = [
            'nama' => 'Pendaftar Pengujian', 'jenis_kelamin' => 'Wanita',
            'tanggal_lahir' => '1990-05-17', 'no_hp' => '081234567890',
            'jenis_usaha' => 'Kuliner', 'lama_usaha_thn' => 3,
            'omset_bulanan' => '15000000.25', 'pinjaman_eksisting' => '0',
            'fasilitas_diajukan' => 'Modal Kerja', 'pinjaman_diajukan' => '25000000',
            'alamat' => 'Alamat usaha untuk pengujian',
        ];
        $this->post('/daftar', $data)->assertSessionHasNoErrors()->assertRedirect('/');
        $this->assertDatabaseHas('registrations', array_diff_key($data, ['tanggal_lahir' => true]));
        $this->assertSame('1990-05-17', Registration::first()->tanggal_lahir->format('Y-m-d'));
        $this->assertSame('15000000.25', Registration::first()->omset_bulanan);
        $this->get('/')->assertOk()->assertSee('Data pendaftaran Anda berhasil disimpan.');
    }

    public function test_only_name_and_phone_are_required(): void
    {
        $this->post('/daftar', ['nama' => 'Pendaftar Uji', 'no_hp' => '081234567890'])
            ->assertSessionHasNoErrors()->assertRedirect('/');
        $this->assertDatabaseHas('registrations', [
            'nama' => 'Pendaftar Uji', 'no_hp' => '081234567890',
            'omset_bulanan' => null, 'jenis_usaha' => null, 'tanggal_lahir' => null,
        ]);
    }

    public function test_formatted_phone_preserves_leading_zero(): void
    {
        $this->post('/daftar', ['nama' => 'Pendaftar Uji', 'no_hp' => '0812-3456-7890'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('registrations', ['no_hp' => '081234567890']);
    }

    public function test_international_indonesian_phone_format_is_accepted(): void
    {
        $this->post('/daftar', ['nama' => 'Pendaftar Uji', 'no_hp' => '+62 812 3456 7890'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('registrations', ['no_hp' => '+6281234567890']);
    }

    public function test_required_fields_have_indonesian_errors(): void
    {
        $this->from('/')->post('/daftar', [])->assertRedirect('/')
            ->assertSessionHasErrors(['nama' => 'Nama lengkap wajib diisi.', 'no_hp' => 'Nomor HP wajib diisi.']);
        $this->assertDatabaseCount('registrations', 0);
        $this->get('/')->assertOk()->assertSee('Pendaftaran belum terkirim.');
    }

    #[DataProvider('invalidFields')]
    public function test_invalid_data_is_rejected_and_name_is_preserved(string $field, mixed $value): void
    {
        $data = array_merge(['nama' => 'Nama dipertahankan', 'no_hp' => '081234567890'], [$field => $value]);
        $this->from('/')->post('/daftar', $data)->assertRedirect('/')
            ->assertSessionHasErrors($field)->assertSessionHasInput('nama', 'Nama dipertahankan');
        $this->assertDatabaseCount('registrations', 0);
        $this->get('/')->assertOk()->assertSee('Nama dipertahankan');
    }

    public static function invalidFields(): array
    {
        return [
            'invalid phone' => ['no_hp', '12345'],
            'malformed phone' => ['no_hp', ['081234567890']],
            'unknown gender' => ['jenis_kelamin', 'Tidak tersedia'],
            'unknown business' => ['jenis_usaha', 'Tidak tersedia'],
            'unknown facility' => ['fasilitas_diajukan', 'Tidak tersedia'],
            'future birth date' => ['tanggal_lahir', '2999-01-01'],
            'impossible birth date' => ['tanggal_lahir', '1999-02-31'],
            'fractional business age' => ['lama_usaha_thn', '1.5'],
            'negative turnover' => ['omset_bulanan', '-1'],
            'negative existing loan' => ['pinjaman_eksisting', '-1'],
            'negative requested loan' => ['pinjaman_diajukan', '-1'],
            'amount overflow' => ['pinjaman_diajukan', '1000000000000'],
            'too many decimals' => ['omset_bulanan', '1500.123'],
            'address too long' => ['alamat', str_repeat('a', 1001)],
        ];
    }

    public function test_submissions_are_rate_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/daftar', [])->assertSessionHasErrors();
        }
        $this->post('/daftar', [])->assertStatus(429);
        $this->assertDatabaseCount('registrations', 0);
    }
}
