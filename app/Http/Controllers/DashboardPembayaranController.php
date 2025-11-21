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

    public function dokumens(){
        $data = array(
            "title" => "Daftar Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDokumen' => 'Active',
            'menuDaftarDokumen' => 'Active',
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
        $data = array(
            "title" => "Daftar Pengembalian Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDokumen' => 'Active',
            'menuDaftarDokumenDikembalikan' => 'Active',
        );
        return view('pembayaran.dokumens.pengembalianPembayaran', $data);
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

    public function diagram(){
        $data = array(
            "title" => "Diagram Pembayaran",
            "module" => "pembayaran",
            "menuDashboard" => "",
            'menuDiagram' => 'Active',
        );
        return view('pembayaran.diagramPembayaran', $data);
    }
}

