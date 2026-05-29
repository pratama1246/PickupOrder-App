<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    /**
     * Daftar semua pengguna sistem (/admin/pengguna).
     * Menampilkan mahasiswa dan vendor, mendukung pencarian dan filter role.
     */
    public function index(Request $request): View
    {
        $query = User::whereIn('role', ['mahasiswa', 'vendor'])->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.pengguna.index', compact('users'));
    }

    /**
     * Form tambah pengguna baru (/admin/pengguna/create).
     */
    public function create(): View
    {
        return view('admin.pengguna.create');
    }

    /**
     * Simpan pengguna baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => [$request->role === 'vendor' ? 'nullable' : 'required', 'string', 'max:50', 'unique:users,nim'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:mahasiswa,vendor'],
            'is_first_login' => ['required', 'boolean'],
        ]);

        if ($request->role === 'vendor') {
            $validated['nim'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['password_changed'] = false;

        User::create($validated);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Form edit data pengguna (/admin/pengguna/{id}/edit).
     */
    public function edit(int $id): View
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        return view('admin.pengguna.edit', compact('user'));
    }

    /**
     * Update data pengguna.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => [$request->role === 'vendor' ? 'nullable' : 'required', 'string', 'max:50', 'unique:users,nim,'.$id],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:mahasiswa,vendor'],
            'is_first_login' => ['required', 'boolean'],
        ]);

        if ($request->role === 'vendor') {
            $validated['nim'] = null;
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Toggle status aktif/nonaktif akun pengguna.
     * Menggunakan kolom is_first_login sebagai status aktif (sementara).
     * Implementasi penuh memerlukan kolom is_active di migrasi.
     */
    public function toggle(int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        // Toggle: jika is_first_login true berarti belum aktif sepenuhnya
        $user->update([
            'is_first_login' => ! $user->is_first_login,
        ]);

        $status = $user->is_first_login ? 'dinonaktifkan' : 'diaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    /**
     * Hapus akun pengguna dari database.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        $user->delete();

        return back()->with('success', "Akun {$user->name} berhasil dihapus.");
    }

    /**
     * Hapus beberapa akun pengguna sekaligus.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:users,id'],
        ]);

        $ids = array_filter($request->ids, function ($id) {
            return $id != auth()->id();
        });

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada pengguna valid yang dipilih untuk dihapus.');
        }

        User::whereIn('id', $ids)->whereIn('role', ['mahasiswa', 'vendor'])->delete();

        return back()->with('success', count($ids).' akun pengguna berhasil dihapus.');
    }

    /**
     * Aktifkan/Nonaktifkan beberapa akun pengguna sekaligus.
     */
    public function bulkToggle(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'exists:users,id'],
            'action' => ['required', 'in:activate,deactivate'],
        ]);

        $ids = array_filter($request->ids, function ($id) {
            return $id != auth()->id();
        });

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada pengguna valid yang dipilih.');
        }

        $isFirstLogin = $request->action === 'deactivate' ? true : false;
        User::whereIn('id', $ids)->whereIn('role', ['mahasiswa', 'vendor'])->update([
            'is_first_login' => $isFirstLogin,
        ]);

        $status = $request->action === 'activate' ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', count($ids)." akun pengguna berhasil $status.");
    }

    /**
     * Tampilkan form import pengguna CSV (/admin/pengguna/import).
     */
    public function importForm(): View
    {
        return view('admin.pengguna.import');
    }

    /**
     * Download template CSV untuk import pengguna.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_user_pnc.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Header kolom
            fputcsv($handle, ['nama', 'nim', 'email']);

            // Contoh baris data untuk panduan admin
            fputcsv($handle, ['Ahmad Dani', '22030101', 'ahmaddani@pnc.ac.id']);
            fputcsv($handle, ['Budi Santoso', '22030102', 'budi@pnc.ac.id']);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Memproses file CSV import data pengguna.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        $successCount = 0;
        $errors = [];
        $line = 1;

        if (($handle = fopen($filePath, 'r')) !== false) {
            // Read header
            $header = fgetcsv($handle, 1000, ',');

            // Normalisasi header (hapus BOM jika ada, trim whitespace, lowercase)
            if ($header) {
                // Hapus UTF-8 BOM jika ada di kolom pertama
                $header[0] = preg_replace('/[\x{00EF}\x{00BB}\x{00BF}]/u', '', $header[0]);
                $header = array_map('strtolower', array_map('trim', $header));
            }

            // Validasi format header
            $expectedHeader = ['nama', 'nim', 'email'];
            if (! $header || count(array_intersect($expectedHeader, $header)) !== 3) {
                fclose($handle);

                return back()->withErrors(['file' => 'Format kolom CSV tidak sesuai. Harus berisi kolom: nama, nim, email.']);
            }

            // Map header ke index
            $nameIdx = array_search('nama', $header);
            $nimIdx = array_search('nim', $header);
            $emailIdx = array_search('email', $header);

            DB::beginTransaction();
            try {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $line++;

                    // Skip empty rows
                    if (empty(array_filter($data))) {
                        continue;
                    }

                    $nama = isset($data[$nameIdx]) ? trim($data[$nameIdx]) : '';
                    $nim = isset($data[$nimIdx]) ? trim($data[$nimIdx]) : '';
                    $email = isset($data[$emailIdx]) ? trim($data[$emailIdx]) : '';

                    // Validasi baris data
                    $validator = Validator::make(
                        ['nama' => $nama, 'nim' => $nim, 'email' => $email],
                        [
                            'nama' => 'required|string|max:255',
                            'nim' => 'required|string|max:50|unique:users,nim',
                            'email' => 'required|email|max:255|unique:users,email',
                        ],
                        [
                            'nama.required' => 'Nama tidak boleh kosong.',
                            'nim.required' => 'NIM/NIP tidak boleh kosong.',
                            'nim.unique' => 'NIM/NIP sudah terdaftar.',
                            'email.required' => 'Email tidak boleh kosong.',
                            'email.email' => 'Format email tidak valid.',
                            'email.unique' => 'Email sudah terdaftar.',
                        ]
                    );

                    if ($validator->fails()) {
                        $rowErrors = implode(' ', $validator->errors()->all());
                        $errors[] = "Baris {$line} ({$nama}): {$rowErrors}";

                        continue;
                    }

                    // Create User
                    User::create([
                        'name' => $nama,
                        'nim' => $nim,
                        'email' => $email,
                        'password' => Hash::make('Pnc_'.$nim),
                        'role' => 'mahasiswa',
                        'is_first_login' => true,
                        'password_changed' => false,
                    ]);

                    $successCount++;
                }

                if (count($errors) > 0 && $successCount == 0) {
                    // Jika semua gagal, rollback saja
                    DB::rollBack();
                    fclose($handle);

                    return back()->withInput()->with('error_list', $errors)->withErrors(['file' => 'Gagal mengimpor data. Semua baris data tidak valid.']);
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                fclose($handle);

                return back()->withErrors(['file' => 'Terjadi kesalahan sistem saat membaca file: '.$e->getMessage()]);
            }

            fclose($handle);
        } else {
            return back()->withErrors(['file' => 'File CSV tidak dapat dibaca.']);
        }

        $message = "Berhasil mengimpor {$successCount} pengguna.";
        if (count($errors) > 0) {
            $message .= ' Namun ada '.count($errors).' baris data yang dilewati karena tidak valid.';

            return redirect()->route('admin.pengguna.index')
                ->with('success', $message)
                ->with('error_list', $errors);
        }

        return redirect()->route('admin.pengguna.index')->with('success', $message);
    }
}
