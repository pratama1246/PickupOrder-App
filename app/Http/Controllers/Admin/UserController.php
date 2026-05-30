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
     * Menampilkan daftar seluruh pengguna sistem (/admin/users).
     * Membatasi pencarian hanya untuk role 'mahasiswa' dan 'vendor' demi menjaga keamanan akun administrator.
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

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan formulir pembuatan pengguna baru secara manual (/admin/users/create).
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan data pengguna baru secara manual ke database.
     * Mengatur validasi NIM secara kondisional (wajib untuk mahasiswa, opsional/null untuk vendor).
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

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    /**
     * Menampilkan formulir penyuntingan data pengguna (/admin/users/{id}/edit).
     */
    public function edit(int $id): View
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui data profil pengguna di sistem.
     * Kata sandi hanya dienkripsi ulang jika diisi baru oleh administrator.
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

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    /**
     * Mengubah status aktif/nonaktif akun pengguna secara cepat.
     * Menggunakan flag 'is_first_login' untuk memicu/menghalangi akses dashboard
     * guna menghindari penambahan kolom migrasi baru yang minim kegunaan.
     */
    public function toggle(int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        $user->update([
            'is_first_login' => ! $user->is_first_login,
        ]);

        $status = $user->is_first_login ? 'dinonaktifkan' : 'diaktifkan';

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    /**
     * Menghapus satu akun pengguna dari sistem.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = User::whereIn('role', ['mahasiswa', 'vendor'])->findOrFail($id);

        $user->delete();

        return back()->with('success', "Akun {$user->name} berhasil dihapus.");
    }

    /**
     * Menghapus beberapa akun sekaligus.
     * Memiliki sistem pengaman yang menyaring ID sendiri dari array input
     * agar administrator tidak sengaja menghapus akun aktif miliknya sendiri.
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
     * Mengaktifkan atau menonaktifkan banyak akun secara massal.
     * Menerapkan filter pelindung agar akun admin aktif tidak ikut terkunci.
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
     * Menampilkan halaman pengimporan data pengguna massal berbasis CSV (/admin/users/import).
     */
    public function importForm(): View
    {
        return view('admin.users.import');
    }

    /**
     * Mengirimkan berkas CSV kosong berisi header panduan ke peramban.
     * Menggunakan StreamedResponse agar berkas langsung teralirkan ke browser tanpa mengonsumsi memori server.
     */
    public function downloadTemplate(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_user_pnc.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['nama', 'nim', 'email']);

            fputcsv($handle, ['Ahmad Dani', '22030101', 'ahmaddani@pnc.ac.id']);
            fputcsv($handle, ['Budi Santoso', '22030102', 'budi@pnc.ac.id']);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Memproses berkas CSV yang diunggah dan mendaftarkan pengguna secara massal.
     * Menghapus UTF-8 BOM pada kolom pertama untuk mencegah error pemetaan header dari Microsoft Excel,
     * serta membungkus perulangan dalam Transaksi Database (DB Transaction) demi integritas data terimpor.
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
            $header = fgetcsv($handle, 1000, ',');

            // Normalisasi header: hapus UTF-8 BOM jika ada, trim spasi, dan jadikan huruf kecil.
            if ($header) {
                $header[0] = preg_replace('/[\x{00EF}\x{00BB}\x{00BF}]/u', '', $header[0]);
                $header = array_map('strtolower', array_map('trim', $header));
            }

            $expectedHeader = ['nama', 'nim', 'email'];
            if (! $header || count(array_intersect($expectedHeader, $header)) !== 3) {
                fclose($handle);

                return back()->withErrors(['file' => 'Format kolom CSV tidak sesuai. Harus berisi kolom: nama, nim, email.']);
            }

            $nameIdx = array_search('nama', $header);
            $nimIdx = array_search('nim', $header);
            $emailIdx = array_search('email', $header);

            DB::beginTransaction();
            try {
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $line++;

                    if (empty(array_filter($data))) {
                        continue;
                    }

                    $nama = isset($data[$nameIdx]) ? trim($data[$nameIdx]) : '';
                    $nim = isset($data[$nimIdx]) ? trim($data[$nimIdx]) : '';
                    $email = isset($data[$emailIdx]) ? trim($data[$emailIdx]) : '';

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

                    // Mendaftarkan akun mahasiswa baru dengan kata sandi bawaan berpola Pnc_[NIM].
                    // Flag is_first_login diatur true agar pengguna dipaksa mereset password saat masuk pertama kali.
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

            return redirect()->route('admin.users.index')
                ->with('success', $message)
                ->with('error_list', $errors);
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }
}
