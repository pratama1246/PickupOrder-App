<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Canteen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class CanteenController extends Controller
{
    /**
     * Menampilkan daftar seluruh kantin terdaftar (/admin/canteen).
     * Mendukung penelusuran nama dan pemfilteran status buka/tutup warung.
     * Menggunakan pagination terintegrasi filter query string untuk menjaga parameter pencarian aktif.
     */
    public function index(Request $request): View
    {
        // Sanitasi parameter search untuk membatasi karakter khusus (hanya alfanumerik, spasi, @, titik, dan strip)
        if ($request->filled('search')) {
            $sanitizedSearch = preg_replace('/[^a-zA-Z0-9\s@\.\-]/', '', $request->input('search'));
            $request->merge(['search' => $sanitizedSearch]);
        }

        $query = Canteen::with('owner')->withAvg('reviews', 'rating')->withCount('menus');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_open', $request->status === 'buka');
        }

        $canteens = $query->latest()->paginate(10)->withQueryString();

        return view('admin.canteen', compact('canteens'));
    }

    /**
     * Menampilkan informasi detail performa satu kantin (/admin/canteen/{id}).
     * Memuat statistik keuangan dan performa transaksi lewat agregasi Eloquent dalam satu kueri.
     */
    public function show(int $id): View
    {
        $canteen = Canteen::with('owner')
            ->withCount('menus')
            ->withCount('orders')
            ->withCount(['orders as completed_orders_count' => function ($query) {
                $query->where('status', 'selesai');
            }])
            ->withSum(['orders as total_revenue' => function ($query) {
                $query->where('status', 'selesai');
            }], 'total_price')
            ->findOrFail($id);

        // Membagi parameter halaman menjadi 'menus_page' dan 'orders_page' agar aksi pagination
        // pada salah satu tabel tidak mereset status indeks pagination tabel lainnya di view yang sama.
        $menus = $canteen->menus()->latest()->paginate(5, ['*'], 'menus_page')->withQueryString();
        $orders = $canteen->orders()->with('user')->latest()->paginate(5, ['*'], 'orders_page')->withQueryString();

        return view('admin.canteen-show', compact('canteen', 'menus', 'orders'));
    }

    /**
     * Menampilkan formulir pendaftaran kantin baru (/admin/canteen/create).
     */
    public function create(): View
    {
        return view('admin.canteen-create');
    }

    /**
     * Menyimpan data kantin baru serta mendaftarkan akun Vendor secara otomatis.
     * Membuat email vendor secara dinamis berbasis slug nama kantin dan kata sandi bawaan,
     * untuk mempercepat proses onboarding vendor oleh administrator kampus.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'is_open' => ['boolean'],
        ]);

        // Mengonversi nama kantin menjadi slug tanpa spasi untuk membentuk base email institusi.
        // strip_tags dijalankan lebih dahulu untuk memastikan nama bersih dari tag HTML injeksi.
        $validated['name']        = strip_tags($validated['name']);
        $validated['description'] = strip_tags($validated['description'] ?? '');
        $cleanName = Str::slug($validated['name'], '');
        $baseEmail = $cleanName.'@pnc.ac.id';
        $email = $baseEmail;
        $counter = 1;

        // Loop pengecekan di database untuk memastikan email vendor unik dan mencegah collision data.
        while (User::where('email', $email)->exists()) {
            $email = $cleanName.$counter.'@pnc.ac.id';
            $counter++;
        }

        $password = 'pncpickup123';

        if ($request->hasFile('image')) {
            $filename = \Illuminate\Support\Str::random(40).'.webp';
            try {
                $image = Image::decode($request->file('image'));
                $image->cover(1200, 450); // Aspect ratio landscape cover
                $webp = $image->encode(new WebpEncoder(quality: 75));
                Storage::disk('public')->put('canteens/'.$filename, $webp->toString());
                $validated['image'] = 'canteens/'.$filename;
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Berkas gambar banner rusak atau tidak dapat diproses.'])->withInput();
            }
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use (&$validated, $email, $password) {
                // Pendaftaran otomatis user ber-role vendor untuk pengelolaan mandiri oleh pemilik warung.
                // Menggunakan forceCreate karena 'role' sengaja dikecualikan dari $fillable model User
                // demi mencegah Mass Assignment Privilege Escalation dari input publik.
                $user = User::forceCreate([
                    'name'             => 'Vendor '.$validated['name'],
                    'email'            => $email,
                    'password'         => Hash::make($password),
                    'role'             => 'vendor',
                    'is_first_login'   => true,
                    'password_changed' => false,
                ]);

                $validated['user_id'] = $user->id;

                Canteen::create($validated);
            });
        } catch (\Exception $e) {
            // Hapus file baru jika transaksi gagal
            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }
            return back()->withErrors(['image' => 'Gagal menambahkan kantin ke database. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('admin.canteen.index')
            ->with('success', "Kantin berhasil ditambahkan. Akun Vendor dibuat dengan Email: {$email} dan Password: {$password}");
    }

    /**
     * Menampilkan formulir penyuntingan data kantin (/admin/canteen/{id}/edit).
     */
    public function edit(int $id): View
    {
        $canteen = Canteen::with('owner')->findOrFail($id);

        return view('admin.canteen-edit', compact('canteen'));
    }

    /**
     * Memperbarui data profil kantin dan memperbarui banner gambar jika diunggah.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $canteen = Canteen::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'is_open' => ['boolean'],
        ]);

        // Sanitasi input teks agar tag HTML/link phishing tidak masuk ke database.
        $validated['name']        = strip_tags($validated['name']);
        $validated['description'] = strip_tags($validated['description'] ?? '');

        if ($request->hasFile('image')) {
            $filename = \Illuminate\Support\Str::random(40).'.webp';
            try {
                $image = Image::decode($request->file('image'));
                $image->cover(1200, 450); // Aspect ratio landscape cover
                $webp = $image->encode(new WebpEncoder(quality: 75));
                Storage::disk('public')->put('canteens/'.$filename, $webp->toString());
                $validated['image'] = 'canteens/'.$filename;
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Berkas gambar banner rusak atau tidak dapat diproses.'])->withInput();
            }
        }

        try {
            $oldImage = $canteen->image;
            $canteen->update($validated);

            // Jika DB berhasil diupdate, lakukan penghapusan file lama di disk
            if (isset($validated['image']) && $oldImage && ! str_starts_with($oldImage, 'assets/')) {
                Storage::disk('public')->delete($oldImage);
            }
        } catch (\Exception $e) {
            // Hapus file baru yang baru saja di-upload jika DB gagal diupdate
            if (isset($validated['image'])) {
                Storage::disk('public')->delete($validated['image']);
            }
            return back()->withErrors(['image' => 'Gagal memperbarui data kantin di database. Silakan coba lagi.'])->withInput();
        }

        return redirect()->route('admin.canteen.index')
            ->with('success', 'Data kantin berhasil diperbarui.');
    }

    /**
     * Menghapus kantin dan membersihkan file aset gambarnya secara tuntas dari disk server.
     */
    public function destroy(int $id): RedirectResponse
    {
        $canteen = Canteen::findOrFail($id);

        if ($canteen->image) {
            Storage::disk('public')->delete($canteen->image);
        }

        $canteen->delete();

        return redirect()->route('admin.canteen.index')->with('success', 'Kantin berhasil dihapus.');
    }

    /**
     * Menghapus banyak kantin sekaligus berdasarkan array ID yang dipilih.
     * Menggunakan perulangan manual agar penghapusan aset gambar di disk server tetap tereksekusi sebelum baris data dihapus.
     */
    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:canteens,id'],
        ]);

        $canteens = Canteen::whereIn('id', $request->ids)->get();

        foreach ($canteens as $canteen) {
            if ($canteen->image) {
                Storage::disk('public')->delete($canteen->image);
            }
            $canteen->delete();
        }

        return redirect()->route('admin.canteen.index')->with('success', 'Kantin terpilih berhasil dihapus.');
    }
}
