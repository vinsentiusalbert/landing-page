<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    private function filteredQuery(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ], [
            'q.max' => 'Pencarian maksimal 150 karakter.',
            'from.date_format' => 'Tanggal awal tidak valid.',
            'to.date_format' => 'Tanggal akhir tidak valid.',
            'to.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        return Registration::query()
            ->when($filters['q'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama', 'like', '%'.$search.'%')
                        ->orWhere('no_hp', 'like', '%'.$search.'%')
                        ->orWhere('jenis_usaha', 'like', '%'.$search.'%');
                });
            })
            ->when($filters['from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
    }

    public function index(Request $request)
    {
        return view('admin.index', [
            'registrations' => $this->filteredQuery($request)->latest('id')->paginate(15)->withQueryString(),
            'total' => Registration::count(),
            'today' => Registration::whereDate('created_at', today())->count(),
            'requested' => Registration::sum('pinjaman_diajukan'),
        ]);
    }

    public function show(Registration $registration)
    {
        return view('admin.show', compact('registration'));
    }

    public function export(Request $request)
    {
        $query = $this->filteredQuery($request)->orderBy('id');

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['ID', 'Tanggal daftar', 'Nama lengkap', 'Jenis kelamin', 'Tanggal lahir', 'No. HP', 'Jenis usaha', 'Lama usaha (tahun)', 'Omset per bulan (Rp)', 'Pinjaman Berjalan (Rp)', 'Fasilitas diajukan', 'Pinjaman diajukan (Rp)', 'Alamat usaha']);

            foreach ($query->cursor() as $registration) {
                $row = [
                    $registration->id, $registration->created_at->format('Y-m-d H:i:s'),
                    $registration->nama, $registration->jenis_kelamin,
                    $registration->tanggal_lahir?->format('Y-m-d'), $registration->no_hp,
                    $registration->jenis_usaha, $registration->lama_usaha_thn,
                    $registration->omset_bulanan, $registration->pinjaman_eksisting,
                    $registration->fasilitas_diajukan, $registration->pinjaman_diajukan,
                    $registration->alamat,
                ];

                // Treat user-supplied formula prefixes as text in spreadsheet apps.
                $row = array_map(fn ($value) => is_string($value) && preg_match('/^[\s]*[=+@-]/u', $value) ? "'".$value : $value, $row);
                fputcsv($output, $row);
            }

            fclose($output);
        }, 'pendaftar-prisma-dana-'.now()->format('Y-m-d-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
