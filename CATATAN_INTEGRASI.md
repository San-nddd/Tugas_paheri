# Catatan Integrasi

## 1. Registrasi middleware `role`
File `app/Http/Middleware/CheckRole.php` perlu didaftarkan sebagai alias.

**Laravel 11+ (bootstrap/app.php):**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

**Laravel 10 ke bawah (app/Http/Kernel.php):**
```php
protected $middlewareAliases = [
    // ...
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

## 2. Konfigurasi Auth Guard
Karena kolom login memakai `kata_sandi` (bukan `password`) dan model
`Pengguna` (bukan `User`), sesuaikan `config/auth.php`:
```php
'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\Pengguna::class,
    ],
],
```

## 3. Urutan Migration
File migration diberi prefix angka urut (`000001` s.d. `000008`) agar
Laravel menjalankannya sesuai dependensi Foreign Key:
`pengguna` → `turnamen` → `tim` → `pemain` → `pendaftaran` →
`roster_turnamen` → `pertandingan` → SQL View/Trigger/Procedure.
Sesuaikan nama file dengan timestamp asli saat disalin ke folder
`database/migrations` proyek Anda.

## 4. Controller yang direferensikan di routes/api.php
Routing sudah dirancang lengkap dengan pemisahan namespace
(`Api\Public`, `Api\Admin`, `Api\Penyelenggara`, `Api\Kapten`) sesuai
RBAC. Controller-controller tersebut belum dibuatkan karena di luar
4 poin tugas spesifik yang diminta — beri tahu saya jika Anda ingin
saya generate implementasi controller-nya juga (mis. `PertandinganController@updateSkor`
yang memakai `UpdateSkorRequest` + trigger status turnamen otomatis).

## 5. Memanggil Stored Procedure dari Laravel
```php
DB::statement('CALL generate_bracket(?)', [$turnamen->id_turnamen]);
```

## 6. Contoh pemakaian ApiResponser + Resource di controller
```php
use App\Traits\ApiResponser;

class PertandinganController extends Controller
{
    use ApiResponser;

    public function updateSkor(UpdateSkorRequest $request, Pertandingan $pertandingan)
    {
        $pertandingan->update($request->validated());
        $pertandingan->load(['tim1', 'tim2', 'timPemenang']);

        return $this->successResponse(
            new PertandinganResource($pertandingan),
            'Skor pertandingan berhasil diperbarui.'
        );
    }
}
```
