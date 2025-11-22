<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\DocumentTracking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    /**
     * Display the owner dashboard with document list and tracking
     */
    public function index()
    {
        // Get all documents with latest status
        $documents = $this->getDocumentsWithTracking();

        // Get statistics for the panel
        $stats = $this->getDashboardStats();

        return view('owner.dashboard', compact('documents', 'stats'))
            ->with('title', 'Owner Dashboard - Command Center')
            ->with('module', 'owner')
            ->with('menuDashboard', 'active')
            ->with('menuDokumen', '')
            ->with('menuDaftarDokumen', '')
            ->with('menuEditDokumen', '')
            ->with('menuRekapKeterlambatan', '')
            ->with('menuDaftarDokumenDikembalikan', '')
            ->with('menuRekapan', '')
            ->with('menuPengembalianKeBidang', '')
            ->with('menuTambahDokumen', '')
            ->with('dashboardUrl', '/owner/dashboard')
            ->with('dokumenUrl', '#')
            ->with('pengembalianUrl', '#')
            ->with('tambahDokumenUrl', '#');
    }

    /**
     * Get API endpoint for document timeline
     */
    public function getDocumentTimeline($id): JsonResponse
    {
        $dokumen = Dokumen::with(['dokumenPos', 'dokumenPrs', 'dibayarKepadas'])
            ->findOrFail($id);

        $timeline = $this->generateDocumentTimeline($dokumen);

        return response()->json([
            'success' => true,
            'dokumen' => [
                'id' => $dokumen->id,
                'nomor_agenda' => $dokumen->nomor_agenda,
                'nomor_spp' => $dokumen->nomor_spp,
                'uraian_spp' => $dokumen->uraian_spp,
                'nilai_rupiah' => $dokumen->nilai_rupiah,
                'status' => $dokumen->status,
                'current_handler' => $dokumen->current_handler,
                'created_at' => $dokumen->created_at->format('d M Y H:i'),
                'progress_percentage' => $this->calculateProgress($dokumen),
            ],
            'timeline' => $timeline
        ]);
    }

    /**
     * Get documents with their latest tracking status
     */
    private function getDocumentsWithTracking()
    {
        return Dokumen::with(['dokumenPos', 'dokumenPrs', 'dibayarKepadas'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($dokumen) {
                return [
                    'id' => $dokumen->id,
                    'nomor_agenda' => $dokumen->nomor_agenda,
                    'nomor_spp' => $dokumen->nomor_spp,
                    'uraian_spp' => $dokumen->uraian_spp,
                    'nilai_rupiah' => $dokumen->nilai_rupiah,
                    'status' => $dokumen->status,
                    'current_handler' => $dokumen->current_handler,
                    'created_at' => $dokumen->created_at->format('d M Y H:i'),
                    'progress_percentage' => $this->calculateProgress($dokumen),
                    'status_badge_color' => $this->getStatusBadgeColor($dokumen->status),
                    'progress_color' => $this->getProgressColor($dokumen->status),
                    'is_overdue' => $this->isDocumentOverdue($dokumen),
                    'deadline_info' => $this->getDeadlineInfo($dokumen),
                ];
            });
    }

    /**
     * Generate timeline events for a document
     */
    private function generateDocumentTimeline($dokumen)
    {
        $events = [];
        $previousTime = null;

        // Event 1: Dokumen Dibuat
        if ($dokumen->created_at) {
            $events[] = [
                'type' => 'created',
                'icon' => '✅',
                'title' => 'Dokumen Dibuat',
                'timestamp' => $dokumen->created_at->format('d M Y H:i'),
                'info' => [
                    'Dibuat oleh' => $this->getRoleDisplayName($dokumen->created_by),
                    'Nomor Agenda' => $dokumen->nomor_agenda,
                    'Nomor SPP' => $dokumen->nomor_spp,
                    'Nilai' => 'Rp. ' . number_format($dokumen->nilai_rupiah, 0, ',', '.'),
                    'Uraian' => $dokumen->uraian_spp,
                ]
            ];
            $previousTime = $dokumen->created_at;
        }

        // Event 2: Dikirim ke IbuB
        if ($dokumen->sent_to_ibub_at) {
            $duration = $previousTime ? $this->calculateDuration($previousTime, $dokumen->sent_to_ibub_at) : null;
            $events[] = [
                'type' => 'sent_to_ibub',
                'icon' => '🚀',
                'title' => 'Dikirim ke IbuB',
                'timestamp' => $dokumen->sent_to_ibub_at->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Pengirim' => 'IbuA',
                    'Penerima' => 'IbuB',
                    'Durasi dari dibuat' => $duration,
                ]
            ];
            $previousTime = $dokumen->sent_to_ibub_at;
        }

        // Event 3: Deadline Ditetapkan
        if ($dokumen->deadline_at) {
            $duration = $previousTime ? $this->calculateDuration($previousTime, $dokumen->deadline_at) : null;
            $events[] = [
                'type' => 'deadline_set',
                'icon' => '⏰',
                'title' => 'Deadline Ditetapkan',
                'timestamp' => $dokumen->deadline_at->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Durasi deadline' => $dokumen->deadline_days . ' hari',
                    'Catatan' => $dokumen->deadline_note,
                ]
            ];
        }

        // Event 4: Diproses IbuB
        if ($dokumen->processed_at) {
            $duration = $previousTime ? $this->calculateDuration($previousTime, $dokumen->processed_at) : null;
            $events[] = [
                'type' => 'processed_ibub',
                'icon' => '⚡',
                'title' => 'Diproses IbuB',
                'timestamp' => $dokumen->processed_at->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Handler' => 'IbuB',
                    'Durasi proses' => $duration,
                ]
            ];
            $previousTime = $dokumen->processed_at;
        }

        // Event 5: Dikirim ke Perpajakan
        if ($dokumen->sent_to_perpajakan_at) {
            $duration = $previousTime ? $this->calculateDuration($previousTime, $dokumen->sent_to_perpajakan_at) : null;
            $events[] = [
                'type' => 'sent_to_perpajakan',
                'icon' => '🚀',
                'title' => 'Dikirim ke Perpajakan',
                'timestamp' => $dokumen->sent_to_perpajakan_at->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Pengirim' => $this->getRoleDisplayName($dokumen->current_handler),
                    'Penerima' => 'Perpajakan',
                    'Durasi dari step sebelumnya' => $duration,
                ]
            ];
            $previousTime = $dokumen->sent_to_perpajakan_at;
        }

        // Event 6: Diproses Perpajakan
        if ($dokumen->processed_perpajakan_at) {
            $duration = $previousTime ? $this->calculateDuration($previousTime, $dokumen->processed_perpajakan_at) : null;
            $events[] = [
                'type' => 'processed_perpajakan',
                'icon' => '⚡',
                'title' => 'Diproses Perpajakan',
                'timestamp' => $dokumen->processed_perpajakan_at->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Handler' => 'Perpajakan',
                    'Status Perpajakan' => $dokumen->status_perpajakan,
                    'Durasi proses' => $duration,
                ]
            ];
            $previousTime = $dokumen->processed_perpajakan_at;
        }

        // Event 7: Dikirim ke Akutansi
        if ($dokumen->sent_to_akutansi_at) {
            $duration = $previousTime ? $this->calculateDuration($previousTime, $dokumen->sent_to_akutansi_at) : null;
            $events[] = [
                'type' => 'sent_to_akutansi',
                'icon' => '🚀',
                'title' => 'Dikirim ke Akutansi',
                'timestamp' => $dokumen->sent_to_akutansi_at->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Pengirim' => 'Perpajakan',
                    'Penerima' => 'Akutansi',
                    'Durasi dari step sebelumnya' => $duration,
                ]
            ];
            $previousTime = $dokumen->sent_to_akutansi_at;
        }

        // Event 8: Dikembalikan
        if ($dokumen->returned_to_ibua_at || $dokumen->department_returned_at || $dokumen->bidang_returned_at) {
            $returnTime = $dokumen->returned_to_ibua_at ?? $dokumen->department_returned_at ?? $dokumen->bidang_returned_at;
            $duration = $previousTime ? $this->calculateDuration($previousTime, $returnTime) : null;

            $events[] = [
                'type' => 'returned',
                'icon' => '↩️',
                'title' => 'Dikembalikan',
                'timestamp' => $returnTime->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Dikembalikan oleh' => $this->getRoleDisplayName($dokumen->current_handler),
                    'Dikembalikan ke' => $this->getReturnDestination($dokumen),
                    'Alasan' => $dokumen->alasan_pengembalian ?? $dokumen->department_return_reason ?? $dokumen->bidang_return_reason,
                ]
            ];
            $previousTime = $returnTime;
        }

        // Event 9: Deadline Selesai (jika ada)
        if ($dokumen->deadline_completed_at) {
            $duration = $previousTime ? $this->calculateDuration($previousTime, $dokumen->deadline_completed_at) : null;
            $events[] = [
                'type' => 'deadline_completed',
                'icon' => '✅',
                'title' => 'Deadline Selesai',
                'timestamp' => $dokumen->deadline_completed_at->format('d M Y H:i'),
                'duration' => $duration,
                'info' => [
                    'Status' => 'Deadline terpenuhi',
                    'Durasi' => $duration,
                ]
            ];
        }

        // Event 10: Dokumen Selesai
        if (in_array($dokumen->status, ['approved_data_sudah_terkirim', 'selesai'])) {
            $completedTime = $dokumen->updated_at;
            $totalDuration = $dokumen->created_at ? $this->calculateDuration($dokumen->created_at, $completedTime) : null;

            $events[] = [
                'type' => 'completed',
                'icon' => '🎉',
                'title' => 'Dokumen Selesai',
                'timestamp' => $completedTime->format('d M Y H:i'),
                'total_duration' => $totalDuration,
                'info' => [
                    'Status Akhir' => $dokumen->status === 'approved_data_sudah_terkirim' ? 'Approved Data Sudah Terkirim' : 'Selesai',
                    'Total waktu proses' => $totalDuration,
                    'Diselesaikan oleh' => $this->getRoleDisplayName($dokumen->current_handler),
                ]
            ];
        }

        return $events;
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $now = Carbon::now();

        return [
            'total_documents' => Dokumen::count(),
            'active_processing' => Dokumen::whereNotIn('status', ['approved_data_sudah_terkirim', 'rejected_data_tidak_lengkap'])->count(),
            'completed_today' => Dokumen::where('status', 'approved_data_sudah_terkirim')
                ->whereDate('updated_at', $now->toDateString())
                ->count(),
            'overdue_documents' => Dokumen::whereNotNull('deadline_at')
                ->where('deadline_at', '<', $now)
                ->whereNotIn('status', ['approved_data_sudah_terkirim', 'rejected_data_tidak_lengkap'])
                ->count(),
            'avg_processing_time' => $this->calculateAverageProcessingTime(),
            'fastest_department' => $this->getFastestDepartment(),
            'slowest_department' => $this->getSlowestDepartment(),
        ];
    }

    /**
     * Calculate progress percentage based on status
     */
    private function calculateProgress($dokumen)
    {
        $progressMap = [
            'draft' => 0,
            'sedang diproses' => 10,
            'menunggu_verifikasi' => 15,
            'pending_approval_ibub' => 25,
            'sent_to_ibub' => 30,
            'proses_ibub' => 40,
            'sent_to_perpajakan' => 50,
            'proses_perpajakan' => 60,
            'sent_to_akutansi' => 70,
            'proses_akutansi' => 75,
            'menunggu_approved_pengiriman' => 85,
            'proses_pembayaran' => 90,
            'approved_data_sudah_terkirim' => 100,
            'rejected_data_tidak_lengkap' => 100,
            'selesai' => 100,
        ];

        return $progressMap[$dokumen->status] ?? 0;
    }

    /**
     * Get status badge color
     */
    private function getStatusBadgeColor($status)
    {
        $colorMap = [
            'draft' => '#6c757d', // Gray
            'sedang diproses' => '#007bff', // Blue
            'menunggu_verifikasi' => '#007bff', // Blue
            'pending_approval_ibub' => '#ffc107', // Yellow
            'sent_to_ibub' => '#17a2b8', // Cyan
            'proses_ibub' => '#ffc107', // Yellow
            'sent_to_perpajakan' => '#17a2b8', // Cyan
            'proses_perpajakan' => '#17a2b8', // Cyan
            'sent_to_akutansi' => '#6f42c1', // Purple
            'proses_akutansi' => '#6f42c1', // Purple
            'menunggu_approved_pengiriman' => '#fd7e14', // Orange
            'proses_pembayaran' => '#6f42c1', // Purple
            'approved_data_sudah_terkirim' => '#28a745', // Green
            'rejected_data_tidak_lengkap' => '#dc3545', // Red
            'selesai' => '#28a745', // Green
        ];

        return $colorMap[$status] ?? '#6c757d';
    }

    /**
     * Get progress bar color
     */
    private function getProgressColor($status)
    {
        $progress = $this->calculateProgress((object)['status' => $status]);

        if ($progress <= 30) return '#dc3545'; // Red
        if ($progress <= 60) return '#ffc107'; // Yellow
        if ($progress <= 90) return '#17a2b8'; // Blue
        return '#28a745'; // Green
    }

    /**
     * Check if document is overdue
     */
    private function isDocumentOverdue($dokumen)
    {
        if (!$dokumen->deadline_at) return false;

        return Carbon::now()->greaterThan($dokumen->deadline_at) &&
               !in_array($dokumen->status, ['approved_data_sudah_terkirim', 'rejected_data_tidak_lengkap']);
    }

    /**
     * Get deadline information
     */
    private function getDeadlineInfo($dokumen)
    {
        if (!$dokumen->deadline_at) return null;

        $now = Carbon::now();
        $deadline = Carbon::parse($dokumen->deadline_at);

        if ($now->greaterThan($deadline)) {
            return [
                'text' => 'Terlambat ' . $now->diffInDays($deadline) . ' hari',
                'class' => 'overdue'
            ];
        } else {
            return [
                'text' => $now->diffInDays($deadline) . ' hari lagi',
                'class' => 'on-time'
            ];
        }
    }

    /**
     * Calculate duration between two dates
     */
    private function calculateDuration($from, $to)
    {
        $from = Carbon::parse($from);
        $to = Carbon::parse($to);
        $diff = $from->diff($to);

        $parts = [];
        if ($diff->y > 0) $parts[] = $diff->y . ' tahun';
        if ($diff->m > 0) $parts[] = $diff->m . ' bulan';
        if ($diff->d > 0) $parts[] = $diff->d . ' hari';
        if ($diff->h > 0) $parts[] = $diff->h . ' jam';
        if ($diff->i > 0) $parts[] = $diff->i . ' menit';

        return empty($parts) ? 'kurang dari 1 menit' : implode(' ', $parts);
    }

    /**
     * Get display name for role
     */
    private function getRoleDisplayName($role)
    {
        $roleMap = [
            'ibuA' => 'Ibu A',
            'ibuB' => 'Ibu B',
            'perpajakan' => 'Perpajakan',
            'akutansi' => 'Akutansi',
            'pembayaran' => 'Pembayaran',
        ];

        return $roleMap[$role] ?? $role;
    }

    /**
     * Get return destination
     */
    private function getReturnDestination($dokumen)
    {
        if ($dokumen->returned_to_ibua_at) return 'Ibu A';
        if ($dokumen->department_returned_at) return 'Ibu A (Department)';
        if ($dokumen->bidang_returned_at) return 'Ibu B';
        return 'Unknown';
    }

    /**
     * Calculate average processing time
     */
    private function calculateAverageProcessingTime()
    {
        $completedDocs = Dokumen::where('status', 'approved_data_sudah_terkirim')
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        if ($completedDocs->isEmpty()) {
            return '0 hari';
        }

        $totalHours = $completedDocs->sum(function($doc) {
            return $doc->created_at->diffInHours($doc->updated_at);
        });

        $avgHours = $totalHours / $completedDocs->count();

        if ($avgHours < 24) {
            return round($avgHours, 1) . ' jam';
        } else {
            return round($avgHours / 24, 1) . ' hari';
        }
    }

    /**
     * Get fastest processing department
     */
    private function getFastestDepartment()
    {
        $departments = ['ibuB', 'perpajakan', 'akutansi', 'pembayaran'];
        $avgTimes = [];

        foreach ($departments as $dept) {
            $avgTimes[$dept] = $this->getDepartmentAvgProcessingTime($dept);
        }

        return empty($avgTimes) ? '-' : array_keys($avgTimes, min($avgTimes))[0];
    }

    /**
     * Get slowest processing department
     */
    private function getSlowestDepartment()
    {
        $departments = ['ibuB', 'perpajakan', 'akutansi', 'pembayaran'];
        $avgTimes = [];

        foreach ($departments as $dept) {
            $avgTimes[$dept] = $this->getDepartmentAvgProcessingTime($dept);
        }

        return empty($avgTimes) ? '-' : array_keys($avgTimes, max($avgTimes))[0];
    }

    /**
     * Get department average processing time
     */
    private function getDepartmentAvgProcessingTime($dept)
    {
        $completedDocs = Dokumen::where('status', 'approved_data_sudah_terkirim')
            ->where('created_by', $dept)
            ->whereNotNull('created_at')
            ->whereNotNull('updated_at')
            ->get();

        if ($completedDocs->isEmpty()) {
            return 999; // High number for departments with no completed docs
        }

        $totalHours = $completedDocs->sum(function($doc) {
            return $doc->created_at->diffInHours($doc->updated_at);
        });

        return $totalHours / $completedDocs->count();
    }
}