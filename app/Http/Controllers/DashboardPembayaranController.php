<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dokumen;

class DashboardPembayaranController extends Controller
{
    public function index(){
        // Get statistics
        $totalDokumen = Dokumen::count();
        $totalSelesai = Dokumen::where('status', 'selesai')->count();
        $totalProses = Dokumen::where('status', 'sedang diproses')->count();
        $totalDikembalikan = Dokumen::where('status', 'dikembalikan')->count();

        // Get latest documents (5 most recent)
        $dokumenTerbaru = Dokumen::latest('tanggal_masuk')
            ->take(5)
            ->get();

        $data = array(
            "title" => "Dashboard Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "Active",
            'menuDokumen' => '',
            'totalDokumen' => $totalDokumen,
            'totalSelesai' => $totalSelesai,
            'totalProses' => $totalProses,
            'totalDikembalikan' => $totalDikembalikan,
            'dokumenTerbaru' => $dokumenTerbaru,
        );
        return view('pembayaran.dashboardPembayaran', $data);
    }

    public function dokumens(Request $request){
        // Get status filter from request
        $statusFilter = $request->get('status_filter');

        // Build query for pembayaran documents
        $query = \App\Models\Dokumen::where(function($query) {
            $query->where('current_handler', 'pembayaran')
                  ->orWhere('status', 'sent_to_pembayaran')
                  ->orWhere(function($subQuery) {
                      $subQuery->where('status', 'sedang_diproses')
                               ->where('universal_approval_for', 'pembayaran');
                  })
                  ->orWhere('created_by', 'pembayaran');
        });

        // Apply status filter if specified
        if ($statusFilter) {
            switch ($statusFilter) {
                case 'belum_siap_dibayar':
                    $query->where(function($q) {
                        $q->where('status', 'sent_to_pembayaran')
                          ->orWhere('status', 'sedang_diproses');
                    });
                    break;
                case 'siap_dibayar':
                    $query->where('status', 'siap_dibayar');
                    break;
                case 'sudah_dibayar':
                    $query->where('status', 'sudah_dibayar');
                    break;
            }
        }

        // Get documents with ordering
        $dokumens = $query->orderByRaw("CASE
            WHEN status = 'sent_to_pembayaran' THEN 1
            WHEN current_handler = 'pembayaran' AND status = 'sedang_diproses' THEN 2
            ELSE 3
        END")
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

        $data = array(
            "title" => "Daftar Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDokumen' => 'Active',
            'menuDaftarDokumen' => 'Active',
            'dokumens' => $dokumens,
            'statusFilter' => $statusFilter,
        );
        return view('pembayaran.dokumens.daftarPembayaran', $data);
    }

    public function createDokumen(){
        $data = array(
            "title" => "Tambah Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDokumen' => 'Active',
            'menuTambahDokumen' => 'Active',
        );
        return view('pembayaran.dokumens.tambahPembayaran', $data);
    }

    public function storeDokumen(Request $request){
        // Implementation for storing document
        return redirect()->route('dokumensPembayaran.index')->with('success', 'Pembayaran berhasil ditambahkan');
    }

    public function editDokumen($id){
        $data = array(
            "title" => "Edit Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDokumen' => 'Active',
            'menuEditDokumen' => 'Active',
        );
        return view('pembayaran.dokumens.editPembayaran', $data);
    }

    public function updateDokumen(Request $request, $id){
        // Implementation for updating document
        return redirect()->route('dokumensPembayaran.index')->with('success', 'Pembayaran berhasil diperbarui');
    }

    public function destroyDokumen($id){
        // Implementation for deleting document
        return redirect()->route('dokumensPembayaran.index')->with('success', 'Pembayaran berhasil dihapus');
    }

    public function pengembalian(){
        // Redirect ke daftar pembayaran karena tidak ada view pengembalian khusus
        return redirect()->route('dokumensPembayaran.index')->with('info', 'Halaman pengembalian diarahkan ke daftar pembayaran');
    }

    public function rekapanKeterlambatan(){
        $data = array(
            "title" => "Rekap Keterlambatan",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDokumen' => 'Active',
            'menuRekapKeterlambatan' => 'Active',
        );
        return view('pembayaran.dokumens.rekapanKeterlambatan', $data);
    }

    /**
     * Rekapan Dokumen Pembayaran
     * Menampilkan semua dokumen dengan filter dan statistik
     */
    public function rekapan()
    {
        // Get filter parameters
        $statusPembayaran = request('status_pembayaran');
        $year = request('year');
        $month = request('month');
        $search = request('search');
        $mode = request('mode', 'normal'); // normal or rekapan_table
        $selectedColumns = request('columns', []); // Array of selected columns in order

        // Handler yang dianggap "belum siap dibayar"
        $belumSiapHandlers = ['akuntansi', 'perpajakan', 'ibu_a', 'ibu_b'];

        // Base query - semua dokumen yang sudah melewati proses awal
        $query = Dokumen::whereNotNull('nomor_agenda');

        // Apply status filter based on new logic
        if ($statusPembayaran) {
            if ($statusPembayaran === 'belum_siap_dibayar') {
                // Belum siap = masih di akuntansi, perpajakan, ibu_a, ibu_b
                $query->whereIn('current_handler', $belumSiapHandlers);
            } elseif ($statusPembayaran === 'siap_dibayar') {
                // Siap dibayar = sudah di pembayaran tapi belum dibayar
                $query->where(function($q) {
                    $q->where('current_handler', 'pembayaran')
                      ->orWhere('status', 'sent_to_pembayaran');
                })->where(function($q) {
                    $q->whereNull('status_pembayaran')
                      ->orWhere('status_pembayaran', '!=', 'sudah_dibayar');
                });
            } elseif ($statusPembayaran === 'sudah_dibayar') {
                // Sudah dibayar
                $query->where('status_pembayaran', 'sudah_dibayar');
            }
        }

        if ($year) {
            $query->whereYear('created_at', $year);
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_agenda', 'like', "%{$search}%")
                  ->orWhere('nomor_spp', 'like', "%{$search}%")
                  ->orWhere('uraian_spp', 'like', "%{$search}%")
                  ->orWhere('dibayar_kepada', 'like', "%{$search}%");
            });
        }

        // Helper function to calculate computed status
        $getComputedStatus = function($doc) use ($belumSiapHandlers) {
            // Jika sudah dibayar
            if ($doc->status_pembayaran === 'sudah_dibayar') {
                return 'sudah_dibayar';
            }
            // Jika masih di akuntansi, perpajakan, ibu_a, ibu_b
            if (in_array($doc->current_handler, $belumSiapHandlers)) {
                return 'belum_siap_dibayar';
            }
            // Jika sudah di pembayaran tapi belum dibayar
            if ($doc->current_handler === 'pembayaran' || $doc->status === 'sent_to_pembayaran') {
                return 'siap_dibayar';
            }
            // Default
            return 'belum_siap_dibayar';
        };

        // For rekapan table mode - group by vendor
        $rekapanByVendor = null;
        if ($mode === 'rekapan_table' && !empty($selectedColumns)) {
            $allDocsForRekapan = (clone $query)->orderBy('dibayar_kepada')->get();

            // Add computed status to each document
            $allDocsForRekapan->each(function($doc) use ($getComputedStatus) {
                $doc->computed_status = $getComputedStatus($doc);
            });

            // Group by vendor
            $rekapanByVendor = $allDocsForRekapan->groupBy('dibayar_kepada')->map(function($docs, $vendor) {
                return [
                    'vendor' => $vendor ?: 'Tidak Diketahui',
                    'documents' => $docs,
                    'total_nilai' => $docs->sum('nilai_rupiah'),
                    'total_belum_dibayar' => $docs->where('computed_status', 'belum_siap_dibayar')->sum('nilai_rupiah'),
                    'total_siap_dibayar' => $docs->where('computed_status', 'siap_dibayar')->sum('nilai_rupiah'),
                    'total_sudah_dibayar' => $docs->where('computed_status', 'sudah_dibayar')->sum('nilai_rupiah'),
                    'count' => $docs->count(),
                ];
            });
        }

        // Get paginated results for normal mode
        $dokumens = $query->orderBy('created_at', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        // Add computed status to paginated results
        $dokumens->each(function($doc) use ($getComputedStatus) {
            $doc->computed_status = $getComputedStatus($doc);
        });

        // Calculate statistics
        $allDokumensQuery = Dokumen::whereNotNull('nomor_agenda');

        // Apply same filters for statistics
        if ($year) {
            $allDokumensQuery->whereYear('created_at', $year);
        }
        if ($month) {
            $allDokumensQuery->whereMonth('created_at', $month);
        }

        $allDokumensData = $allDokumensQuery->get();

        // Add computed status to all documents for statistics
        $allDokumensData->each(function($doc) use ($getComputedStatus) {
            $doc->computed_status = $getComputedStatus($doc);
        });

        $statistics = [
            'total_documents' => $allDokumensData->count(),
            'total_nilai' => $allDokumensData->sum('nilai_rupiah'),
            'by_status' => [
                'belum_dibayar' => $allDokumensData->where('computed_status', 'belum_siap_dibayar')->count(),
                'siap_dibayar' => $allDokumensData->where('computed_status', 'siap_dibayar')->count(),
                'sudah_dibayar' => $allDokumensData->where('computed_status', 'sudah_dibayar')->count(),
            ],
            'total_nilai_by_status' => [
                'belum_dibayar' => $allDokumensData->where('computed_status', 'belum_siap_dibayar')->sum('nilai_rupiah'),
                'siap_dibayar' => $allDokumensData->where('computed_status', 'siap_dibayar')->sum('nilai_rupiah'),
                'sudah_dibayar' => $allDokumensData->where('computed_status', 'sudah_dibayar')->sum('nilai_rupiah'),
            ],
        ];

        // Get available years for filter
        $availableYears = Dokumen::whereNotNull('nomor_agenda')
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Available columns for rekapan table
        $availableColumns = [
            'nomor_agenda' => 'Nomor Agenda',
            'dibayar_kepada' => 'Nama Vendor/Dibayar Kepada',
            'jenis_pembayaran' => 'Jenis Pembayaran',
            'jenis_sub_pekerjaan' => 'Jenis Subbagian',
            'nomor_mirror' => 'Nomor Mirror',
            'nomor_spp' => 'No SPP',
            'tanggal_spp' => 'Tanggal SPP',
            'tanggal_berita_acara' => 'Tanggal BA',
            'no_berita_acara' => 'Nomor BA',
            'tanggal_berakhir_ba' => 'Tanggal Akhir BA',
            'no_spk' => 'Nomor SPK',
            'tanggal_spk' => 'Tanggal SPK',
            'tanggal_berakhir_spk' => 'Tanggal Berakhir SPK',
            'umur_dokumen_tanggal_masuk' => 'Umur Dokumen (Berdasarkan Tanggal Masuk)',
            'umur_dokumen_tanggal_spp' => 'Umur Dokumen (Berdasarkan Tanggal SPP)',
            'umur_dokumen_tanggal_ba' => 'Umur Dokumen (Berdasarkan Tanggal BA)',
            'nilai_rupiah' => 'Nilai Rupiah',
            'nilai_belum_siap_bayar' => 'Nilai Rupiah Belum Siap Bayar',
            'nilai_siap_bayar' => 'Nilai Rupiah Sudah Siap Bayar',
            'nilai_sudah_dibayar' => 'Nilai Rupiah Sudah Dibayar',
        ];

        $data = [
            'title' => 'Rekapan Dokumen Pembayaran',
            'module' => 'pembayaran',
            'menuDashboard' => '',
            'menuDokumen' => 'Active',
            'menuRekapanDokumen' => 'Active',
            'dokumens' => $dokumens,
            'statistics' => $statistics,
            'selectedStatus' => $statusPembayaran,
            'selectedYear' => $year,
            'selectedMonth' => $month,
            'search' => $search,
            'availableYears' => $availableYears,
            'mode' => $mode,
            'selectedColumns' => $selectedColumns,
            'availableColumns' => $availableColumns,
            'rekapanByVendor' => $rekapanByVendor,
        ];

        return view('pembayaran.dokumens.rekapanDokumen', $data);
    }

    public function diagram(){
        // Get current year or from request
        $selectedYear = request('year', date('Y'));

        // Get available years for filter
        $availableYears = Dokumen::selectRaw('YEAR(tanggal_masuk) as year')
            ->whereNotNull('tanggal_masuk')
            ->where(function($query) {
                $query->where('current_handler', 'pembayaran')
                      ->orWhere('status', 'sent_to_pembayaran')
                      ->orWhere('created_by', 'pembayaran');
            })
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // If no years found, use current year
        if (empty($availableYears)) {
            $availableYears = [date('Y')];
        }

        // Initialize monthly data (1-12 for all months)
        $monthlyData = array_fill(0, 12, 0);
        $keterlambatanData = array_fill(0, 12, 0);
        $ketepatanData = array_fill(0, 12, 0);
        $selesaiData = array_fill(0, 12, 0);
        $tidakSelesaiData = array_fill(0, 12, 0);

        // Get monthly document statistics
        $monthlyStats = Dokumen::selectRaw('MONTH(tanggal_masuk) as month, COUNT(*) as count')
            ->whereYear('tanggal_masuk', $selectedYear)
            ->where(function($query) {
                $query->where('current_handler', 'pembayaran')
                      ->orWhere('status', 'sent_to_pembayaran')
                      ->orWhere('created_by', 'pembayaran');
            })
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Get keterlambatan data
        $keterlambatanStats = Dokumen::selectRaw('MONTH(tanggal_masuk) as month,
            AVG(CASE WHEN DATEDIFF(COALESCE(tanggal_selesai, NOW()), tanggal_masuk) > 7
                THEN DATEDIFF(COALESCE(tanggal_selesai, NOW()), tanggal_masuk) - 7
                ELSE 0 END) as avg_keterlambatan')
            ->whereYear('tanggal_masuk', $selectedYear)
            ->where(function($query) {
                $query->where('current_handler', 'pembayaran')
                      ->orWhere('status', 'sent_to_pembayaran')
                      ->orWhere('created_by', 'pembayaran');
            })
            ->groupBy('month')
            ->pluck('avg_keterlambatan', 'month')
            ->toArray();

        // Get completion statistics
        $completionStats = Dokumen::selectRaw('MONTH(tanggal_masuk) as month,
            SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) as selesai_count,
            SUM(CASE WHEN status != "selesai" THEN 1 ELSE 0 END) as tidak_selesai_count')
            ->whereYear('tanggal_masuk', $selectedYear)
            ->where(function($query) {
                $query->where('current_handler', 'pembayaran')
                      ->orWhere('status', 'sent_to_pembayaran')
                      ->orWhere('created_by', 'pembayaran');
            })
            ->groupBy('month')
            ->get();

        // Fill the data arrays
        foreach ($monthlyStats as $month => $count) {
            $monthlyData[$month - 1] = $count;
        }

        foreach ($keterlambatanStats as $month => $keterlambatan) {
            $keterlambatanData[$month - 1] = min($keterlambatan, 100); // Cap at 100%
            $ketepatanData[$month - 1] = max(0, 100 - $keterlambatan); // Complement
        }

        foreach ($completionStats as $stat) {
            $selesaiData[$stat->month - 1] = $stat->selesai_count;
            $tidakSelesaiData[$stat->month - 1] = $stat->tidak_selesai_count;
        }

        // Indonesian month names
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $data = array(
            "title" => "Diagram Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDokumen' => 'Active',
            'menuDiagram' => 'Active',
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'monthlyData' => $monthlyData,
            'keterlambatanData' => $keterlambatanData,
            'ketepatanData' => $ketepatanData,
            'selesaiData' => $selesaiData,
            'tidakSelesaiData' => $tidakSelesaiData,
            'months' => $months,
        );
        return view('pembayaran.diagramPembayaran', $data);
    }

    /**
     * Check for new documents sent to pembayaran
     */
    public function checkUpdates(Request $request)
    {
        try {
            $lastChecked = $request->input('last_checked', 0);

            // Convert timestamp to Carbon instance
            $lastCheckedDate = $lastChecked > 0
                ? \Carbon\Carbon::createFromTimestamp($lastChecked)
                : \Carbon\Carbon::now();

            // Cek dokumen baru yang dikirim ke pembayaran
            $newDocuments = Dokumen::where(function($query) {
                    $query->where('current_handler', 'pembayaran')
                          ->where('status', 'sent_to_pembayaran');
                })
                ->where('updated_at', '>', $lastCheckedDate)
                ->latest('updated_at')
                ->take(10)
                ->get();

            $totalDocuments = Dokumen::where(function($query) {
                    $query->where('current_handler', 'pembayaran')
                          ->orWhere('status', 'sent_to_pembayaran');
                })->count();

            return response()->json([
                'has_updates' => $newDocuments->count() > 0,
                'new_count' => $newDocuments->count(),
                'total_documents' => $totalDocuments,
                'new_documents' => $newDocuments->map(function($doc) {
                    return [
                        'id' => $doc->id,
                        'nomor_agenda' => $doc->nomor_agenda,
                        'nomor_spp' => $doc->nomor_spp,
                        'uraian_spp' => $doc->uraian_spp,
                        'nilai_rupiah' => $doc->nilai_rupiah,
                        'status' => $doc->status,
                        'sent_at' => $doc->updated_at ? $doc->updated_at->format('d/m/Y H:i') : '-',
                        'sent_from' => 'Akutansi',
                    ];
                }),
                'last_checked' => time()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to check updates: ' . $e->getMessage()
            ], 500);
        }
    }
}

