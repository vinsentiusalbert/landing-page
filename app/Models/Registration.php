<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    public const BUSINESS_TYPES = [
        'Warung Kelontong', 'Kuliner', 'Grosir', 'Fashion / Konveksi',
        'Pertanian', 'Peternakan', 'Jasa', 'Bengkel', 'Toko Online', 'Lainnya',
    ];

    public const FACILITIES = ['Modal Kerja', 'Multiguna', 'Investasi'];

    protected $fillable = [
        'nama', 'jenis_kelamin', 'tanggal_lahir', 'no_hp', 'jenis_usaha',
        'lama_usaha_thn', 'omset_bulanan', 'pinjaman_eksisting',
        'fasilitas_diajukan', 'pinjaman_diajukan', 'alamat',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'lama_usaha_thn' => 'integer',
            'omset_bulanan' => 'decimal:2',
            'pinjaman_eksisting' => 'decimal:2',
            'pinjaman_diajukan' => 'decimal:2',
        ];
    }
}
