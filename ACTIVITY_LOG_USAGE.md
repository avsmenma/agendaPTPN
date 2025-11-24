# Panduan Penggunaan Activity Log System

## Penjelasan "Kurang dari 1 Menit"

"Kurang dari 1 menit" adalah durasi waktu yang dihabiskan dari stage sebelumnya hingga stage saat ini dimulai. Ini menunjukkan seberapa cepat dokumen diproses antara dua stage. Jika durasinya sangat singkat (kurang dari 1 menit), sistem akan menampilkan "kurang dari 1 menit".

## Cara Menggunakan Activity Log Helper

### 1. Import Helper

```php
use App\Helpers\ActivityLogHelper;
```

### 2. Contoh Penggunaan di Controller

#### Log ketika dokumen dibuat:
```php
ActivityLogHelper::logCreated($dokumen);
```

#### Log ketika dokumen dikirim:
```php
ActivityLogHelper::logSent($dokumen, 'ibuB'); // ke Ibu Yuni
ActivityLogHelper::logSent($dokumen, 'perpajakan'); // ke Team Perpajakan
ActivityLogHelper::logSent($dokumen, 'akutansi'); // ke Team Akutansi
ActivityLogHelper::logSent($dokumen, 'pembayaran'); // ke Team Pembayaran
```

#### Log ketika dokumen diterima:
```php
ActivityLogHelper::logReceived($dokumen, 'perpajakan');
```

#### Log ketika deadline di-set:
```php
ActivityLogHelper::logDeadlineSet($dokumen, 'perpajakan', [
    'deadline_at' => $deadlineAt,
    'deadline_days' => $days,
]);
```

#### Log ketika data di-edit:
```php
ActivityLogHelper::logDataEdited($dokumen, 'no_faktur', $oldValue, $newValue, 'perpajakan');
ActivityLogHelper::logDataEdited($dokumen, 'npwp', $oldValue, $newValue, 'perpajakan');
ActivityLogHelper::logDataEdited($dokumen, 'nomor_miro', $oldValue, $newValue, 'akutansi');
ActivityLogHelper::logDataEdited($dokumen, 'status_pembayaran', $oldValue, $newValue, 'pembayaran');
```

#### Log ketika form diisi:
```php
ActivityLogHelper::logFormFilled($dokumen, 'Form Perpajakan', 'perpajakan', [
    'npwp' => $npwp,
    'no_faktur' => $noFaktur,
]);
```

#### Log ketika dokumen dikembalikan:
```php
ActivityLogHelper::logReturned($dokumen, 'ibuB', $alasan, 'perpajakan');
```

#### Log ketika status diubah:
```php
ActivityLogHelper::logStatusChanged($dokumen, $oldStatus, $newStatus, 'perpajakan');
```

### 3. Lokasi untuk Menambahkan Log

#### Di DokumenController (IbuA):
- Setelah dokumen dibuat: `ActivityLogHelper::logCreated($dokumen);`
- Setelah dokumen dikirim ke IbuB: `ActivityLogHelper::logSent($dokumen, 'ibuB', 'ibuA');`

#### Di DashboardBController (IbuB):
- Setelah dokumen diterima: `ActivityLogHelper::logReceived($dokumen, 'ibuB');`
- Setelah dokumen diproses: `ActivityLogHelper::logStatusChanged($dokumen, $oldStatus, 'sedang diproses', 'ibuB');`
- Setelah dokumen dikirim ke Perpajakan: `ActivityLogHelper::logSent($dokumen, 'perpajakan', 'ibuB');`
- Setelah dokumen dikirim ke Akutansi: `ActivityLogHelper::logSent($dokumen, 'akutansi', 'ibuB');`
- Setelah dokumen dikembalikan: `ActivityLogHelper::logReturned($dokumen, 'ibuA', $alasan, 'ibuB');`

#### Di DashboardPerpajakanController:
- Setelah dokumen diterima: `ActivityLogHelper::logReceived($dokumen, 'perpajakan');`
- Setelah deadline di-set: `ActivityLogHelper::logDeadlineSet($dokumen, 'perpajakan', [...]);`
- Setelah data di-edit: `ActivityLogHelper::logDataEdited($dokumen, $field, $oldValue, $newValue, 'perpajakan');`
- Setelah form diisi: `ActivityLogHelper::logFormFilled($dokumen, 'Form Perpajakan', 'perpajakan', [...]);`
- Setelah dokumen dikirim ke Akutansi: `ActivityLogHelper::logSent($dokumen, 'akutansi', 'perpajakan');`
- Setelah dokumen dikembalikan: `ActivityLogHelper::logReturned($dokumen, 'ibuB', $alasan, 'perpajakan');`

#### Di DashboardAkutansiController:
- Setelah dokumen diterima: `ActivityLogHelper::logReceived($dokumen, 'akutansi');`
- Setelah data di-edit: `ActivityLogHelper::logDataEdited($dokumen, 'nomor_miro', $oldValue, $newValue, 'akutansi');`
- Setelah dokumen dikirim ke Pembayaran: `ActivityLogHelper::logSent($dokumen, 'pembayaran', 'akutansi');`

#### Di DashboardPembayaranController:
- Setelah dokumen diterima: `ActivityLogHelper::logReceived($dokumen, 'pembayaran');`
- Setelah deadline di-set: `ActivityLogHelper::logDeadlineSet($dokumen, 'pembayaran', [...]);`
- Setelah status diubah: `ActivityLogHelper::logStatusChanged($dokumen, $oldStatus, $newStatus, 'pembayaran');`
- Setelah link bukti di-upload: `ActivityLogHelper::logDataEdited($dokumen, 'link_bukti_pembayaran', null, $link, 'pembayaran');`

## Menjalankan Migration

Jalankan migration untuk membuat tabel:

```bash
php artisan migrate
```

## Menampilkan Log di View

Log sudah otomatis ditampilkan di halaman workflow (`/owner/workflow/{id}`) di bawah durasi setiap stage.

