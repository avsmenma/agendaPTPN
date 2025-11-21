@extends('layouts/app')
@section('content')

<style>
  h2 {
    background: linear-gradient(135deg, #083E40 0%, #889717 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 20px;
    font-weight: 700;
  }

  .card {
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(8, 62, 64, 0.2), 0 2px 8px rgba(136, 151, 23, 0.1);
  }

  .search-box {
    background: linear-gradient(135deg, #ffffff 0%, #f8faf8 100%);
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(8, 62, 64, 0.1), 0 2px 8px rgba(136, 151, 23, 0.05);
    border: 1px solid rgba(8, 62, 64, 0.08);
  }

  .search-box .input-group {
    max-width: auto;
  }

  .search-box .input-group-text {
    background: white;
    border: 2px solid rgba(8, 62, 64, 0.1);
    border-right: none;
    border-radius: 10px 0 0 10px;
    padding: 10px 14px;
  }

  .search-box .form-control {
    border: 2px solid rgba(8, 62, 64, 0.1);
    border-left: none;
    border-radius: 0 10px 10px 0;
    padding: 10px 14px;
    font-size: 13px;
    transition: all 0.3s ease;
  }

  .search-box .form-control:focus {
    outline: none;
    border-color: #889717;
    box-shadow: 0 0 0 4px rgba(136, 151, 23, 0.1);
  }

  .table-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8faf8 100%);
    padding: 24px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(8, 62, 64, 0.1), 0 2px 8px rgba(136, 151, 23, 0.05);
    border: 1px solid rgba(8, 62, 64, 0.08);
  }

  .table {
    margin-bottom: 0;
    width: 100%;
    border-collapse: collapse;
  }

  .table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  .table thead {
    background: linear-gradient(135deg, #083E40 0%, #0a4f52 50%, #889717 100%) !important;
    display: table-header-group !important;
    visibility: visible !important;
    opacity: 1 !important;
  }

  .table thead th {
    color: white !important;
    font-weight: 600 !important;
    font-size: 14px !important;
    letter-spacing: 0.5px;
    padding: 16px 12px !important;
    border: none !important;
    vertical-align: middle !important;
    text-align: left !important;
    height: auto !important;
    line-height: normal !important;
    display: table-cell !important;
    visibility: visible !important;
  }

  .table thead tr {
    display: table-row !important;
    visibility: visible !important;
    height: auto !important;
  }

  .table tbody tr {
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
  }

  .table tbody tr:hover {
    background: linear-gradient(90deg, rgba(136, 151, 23, 0.05) 0%, transparent 100%);
    border-left: 3px solid #889717;
    transform: scale(1.002);
  }

  .table tbody td {
    padding: 14px 12px;
    font-size: 13px;
    vertical-align: middle;
    border-bottom: 1px solid rgba(8, 62, 64, 0.05);
  }

  /* Badge Styles */
  .badge-status {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
    display: inline-block;
  }

  .badge-selesai {
    background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
  }

  .badge-proses {
    background: linear-gradient(135deg, #ffc107 0%, #ffcd39 100%);
    color: #333;
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
  }

  /* Detail Row Styles */
  .detail-row {
    display: none;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
  }

  .detail-row.show {
    display: table-row;
  }

  .detail-content {
    padding: 20px;
    border-top: 2px solid rgba(8, 62, 64, 0.1);
  }

  .detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
  }

  .detail-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .detail-item {
    display: flex;
    flex-direction: column;
    gap: 6px; /* Gap untuk background spacing */
    padding: 12px;
    background: #ffffff; /* Putih bersih untuk contrast dengan label */
    border-radius: 8px;
    border: 1px solid #f1f5f9; /* Border yang sangat tipis */
    transition: all 0.2s ease;
    min-width: 0;
    width: 100%;
    overflow: visible;
    box-sizing: border-box;
  }

  .detail-item:hover {
    border-color: #e2e8f0;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .detail-label {
    display: inline-block; /* Inline block untuk background yang tepat */
    font-size: 11px;
    font-weight: 700; /* Extra bold */
    color: #374151; /* text-gray-700 - lebih gelap untuk kontras maksimal */
    text-transform: uppercase;
    letter-spacing: 0.7px;
    background: #f3f4f6; /* bg-gray-100 - background yang jelas terlihat */
    padding: 6px 10px; /* Padding yang visible */
    border-radius: 6px; /* Rounded corners yang lembut */
    border-left: 3px solid #6366f1; /* Aksen biru di kiri untuk visual distinction */
    margin-bottom: 2px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: normal;
    max-width: 100%;
    width: fit-content; /* Hanya selebar teks */
    min-width: 120px; /* Minimum width untuk konsistensi */
  }

  .detail-value {
    font-size: 14px;
    color: #111827; /* text-gray-900 - hampir hitam */
    font-weight: 600; /* Semi-bold untuk menonjol sebagai data utama */
    word-wrap: break-word;
    overflow-wrap: break-word;
    word-break: break-word;
    hyphens: auto;
    white-space: normal;
    max-width: 100%;
    width: 100%;
    overflow: visible;
    line-height: 1.6;
    padding: 4px 0; /* Sedikit padding atas/bawah */
    position: relative;
  }

  /* Special styling for different field types */
  .detail-value.text-danger {
    color: #dc2626;
    font-weight: 600;
  }

  .detail-value .badge {
    font-size: 11px;
    font-weight: 600;
  }

  /* Action Button Styles */
  .action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
  }

  .btn-action {
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #083E40 0%, #0a4f52 100%);
    color: white;
  }

  .btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(8, 62, 64, 0.3);
  }

  .btn-action i {
    font-size: 14px;
  }

  .btn-edit {
    background: linear-gradient(135deg, #083E40 0%, #0a4f52 100%);
  }

  .btn-upload {
    background: linear-gradient(135deg, #889717 0%, #9ab01f 100%);
    white-space: nowrap;
  }

  .btn-chevron {
    background: linear-gradient(135deg, #6c757d 0%, #868e96 100%);
    padding: 8px 12px;
  }

  .chevron-icon {
    transition: transform 0.3s ease;
  }

  .chevron-icon.rotate {
    transform: rotate(180deg);
  }

  .main-row {
    cursor: pointer;
  }

  /* Custom thead styling with gradient */
  .table-container .table-responsive table thead tr.table-dark {
    background: linear-gradient(135deg, #083E40 0%, #0a4f52 50%, #889717 100%) !important;
  }

  .table-container .table-responsive table thead tr.table-dark th {
    color: white !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    padding: 16px 12px !important;
    border: none !important;
  }

  /* Section Title Styling */
  .section-title {
    font-size: 18px;
    font-weight: 700;
    color: #083E40;
    margin-top: 30px;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 3px solid #889717;
    background: linear-gradient(135deg, #083E40 0%, #889717 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  /* Detail item full width for long text */
  .detail-item-full {
    grid-column: 1 / -1;
  }

  .btn-tambah {
    padding: 10px 24px;
    background: linear-gradient(135deg, #889717 0%, #9ab01f 100%);
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(136, 151, 23, 0.2);
  }

  .btn-tambah:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(136, 151, 23, 0.3);
  }

  .btn-excel {
    padding: 10px 24px;
    background: linear-gradient(135deg, #28a745 0%, #34ce57 100%);
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
  }

  .btn-excel:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
  }

  .filter-section select {
    padding: 10px 14px;
    border: 2px solid rgba(8, 62, 64, 0.1);
    border-radius: 10px;
    font-size: 13px;
    transition: all 0.3s ease;
    background: white;
    font-weight: 500;
  }

  .filter-section select:focus {
    outline: none;
    border-color: #889717;
    box-shadow: 0 0 0 4px rgba(136, 151, 23, 0.1);
  }

  /* Filter Tabs */
  .filter-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
    padding: 6px;
    background: linear-gradient(135deg, #ffffff 0%, #f8faf8 100%);
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(8, 62, 64, 0.08);
    border: 1px solid rgba(8, 62, 64, 0.08);
  }

  .filter-tab {
    flex: 1;
    padding: 12px 20px;
    text-align: center;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #083E40;
    background: transparent;
    border: none;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .filter-tab:hover {
    background: rgba(8, 62, 64, 0.05);
    color: #083E40;
    text-decoration: none;
  }

  .filter-tab.active {
    background: linear-gradient(135deg, #083E40 0%, #889717 100%);
    color: white !important;
    box-shadow: 0 4px 12px rgba(8, 62, 64, 0.2);
  }

  /* Filter Dropdown Button */
  .filter-dropdown {
    position: relative;
    display: inline-block;
  }

  .filter-btn {
    padding: 10px 20px;
    background: white;
    border: 2px solid rgba(8, 62, 64, 0.1);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #083E40;
  }

  .filter-btn:hover {
    border-color: #889717;
    background: rgba(136, 151, 23, 0.05);
  }

  .filter-btn i {
    font-size: 14px;
  }

  .filter-dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 8px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(8, 62, 64, 0.15);
    border: 1px solid rgba(8, 62, 64, 0.1);
    padding: 16px;
    min-width: 250px;
    z-index: 1000;
  }

  .filter-dropdown.active .filter-dropdown-content {
    display: block;
  }

  .filter-option {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-bottom: 6px;
  }

  .filter-option:hover {
    background: rgba(8, 62, 64, 0.05);
  }

  .filter-option input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin-right: 12px;
    cursor: pointer;
    accent-color: #083E40;
  }

  .filter-option label {
    cursor: pointer;
    margin: 0;
    font-size: 13px;
    font-weight: 500;
    color: #083E40;
    flex: 1;
  }

  /* Search box improvements */
  .search-filter-wrapper {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 0;
  }

  .search-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
  }
</style>

<h2 style="margin-bottom: 20px; font-weight: 700;">{{ $title }}</h2>

<!-- Search & Filter Box -->
<div class="search-box">
  <div class="search-filter-wrapper" style="flex: 1;">
    <div class="input-group" style="flex: 1; max-width: 500px;">
      <span class="input-group-text">
        <i class="fa-solid fa-magnifying-glass text-muted"></i>
      </span>
      <input type="text" class="form-control" placeholder="Cari nomor SPP, tanggal, atau nilai...">
    </div>

    <!-- Filter Dropdown dengan Checkbox -->
    <div class="filter-dropdown">
      <button class="filter-btn" onclick="toggleFilterDropdown()">
        <i class="fa-solid fa-filter"></i>
        Filter
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="filter-dropdown-content">
        <div class="filter-option">
          <input type="checkbox" id="filter-umur" name="filter-umur">
          <label for="filter-umur">Umur Dokumen</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-nilai" name="filter-nilai">
          <label for="filter-nilai">Nilai Rupiah</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-kategori" name="filter-kategori">
          <label for="filter-kategori">Kategori</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-jenis" name="filter-jenis">
          <label for="filter-jenis">Jenis Pembayaran</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-status" name="filter-status">
          <label for="filter-status">Status Pembayaran</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-status" name="filter-status">
          <label for="filter-status">Umur Dokumen</label>
        </div>
        <div class="filter-option">
          <input type="checkbox" id="filter-status" name="filter-status">
          <label for="filter-status">Nama Vendor</label>
        </div>
      </div>
    </div>
  </div>

  
  <div style="display: flex; gap: 12px; align-items: center;">
    <div class="filter-dropdown">
      <button class="filter-btn" onclick="toggleFilterDropdown()">
        <i class="fa-solid fa-filter"></i>
        Year
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="filter-dropdown-content">
        <select name="" id="" class="filter-option">
          <option value="" class="filter-nilai">
            2021
          </option>
          <option value="" class="filter-nilai">
            2021
          </option>
          <option value="" class="filter-nilai">
            2021
          </option>
        </select>
        
      </div>
    </div>
    <!-- <div class="filter-section">
      <select >
        <option>Year</option>
        <option>2025</option>
        <option>2024</option>
        <option>2023</option>
      </select>
    </div> -->
    <a href="{{ url('/pengembalian-dokumens') }}"><button class="btn-tambah">Dokumen Dikembalikan</button></a>
    <a href="#"><button class="btn-excel">Ekspor ke PDF</button></a>
  </div>
</div>

<!-- Tabel Dokumen Masuk -->
<div class="table-container">
  <h6>
    <span style="color: #1a4d3e; text-decoration: none; font-size: 24px;">Dokumen Masuk</span>
  </h6>

  <!-- Filter Tabs -->
  <div class="filter-tabs">
    <a href="#" class="filter-tab active" onclick="filterStatus('all', event)">
      <i class="fa-solid fa-list"></i> Show All
    </a>
    <a href="#" class="filter-tab" onclick="filterStatus('siap', event)">
      <i class="fa-solid fa-circle-check"></i> Sudah Siap Dibayar
    </a>
    <a href="#" class="filter-tab" onclick="filterStatus('belum', event)">
      <i class="fa-solid fa-clock"></i> Belum Siap Dibayar
    </a>
    <a href="#" class="filter-tab" onclick="filterStatus('selesai', event)">
      <i class="fa-solid fa-check-double"></i> Selesai
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr class="table table-dark">
          <th>No</th>
          <th>Tanggal Masuk</th>
          <th>Nomor SPP</th>
          <th>Tanggal SPP</th>
          <th>Nilai Rupiah</th>
          <th>Tanggal Dibayar</th>
          <th>Status</th>
          <th>Bukti</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <!-- Row 1 -->
        <tr class="main-row" onclick="toggleDetail(1)">
          <td style="text-align: center;">001</td>
          <td>01/01/2025 10:30</td>
          <td>001/SK/2025</td>
          <td>01/01/2025 10:30</td>
          <td>Rp12.0000.0000</td>
          <td>01/01/2025 10:30</td>
          <td><span class="badge-status badge-selesai">Sudah Dibayar</span></td>
          <td>
            <button class="btn-action btn-edit"><i class="fa-solid fa-eye"></i></button>
          </td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-chevron"><i class="fa-solid fa-chevron-down chevron-icon" id="chevron-1"></i></button>
            </div>
          </td>
        </tr>
        <tr class="detail-row" id="detail-1">
          <td colspan="9">
            <div class="detail-content">
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Tanggal Masuk</span>
                  <span class="detail-value">01/01/2025 10:30:00</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Bulan</span>
                  <span class="detail-value">Januari</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tahun</span>
                  <span class="detail-value">2025</span>
                </div>

                <div class="detail-item">
                  <span class="detail-label">No SPP</span>
                  <span class="detail-value">001/SK/2025</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal SPP</span>
                  <span class="detail-value">01/01/2025</span>
                </div>
                <div class="detail-item detail-item-full">
                  <span class="detail-label">Uraian SPP</span>
                  <span class="detail-value">Permohonan Anggaran untuk kegiatan operasional tahun 2025</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Nilai Rp</span>
                  <span class="detail-value">Rp.100000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Kategori</span>
                  <span class="detail-value">Iventasi On Farm</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">SubBagian Pekerjaan</span>
                  <span class="detail-value">Pengangkutan</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Jenis Pembayaran</span>
                  <span class="detail-value">Mitra</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Dibayar Kepada</span>
                  <span class="detail-value">Perkebunan Kelapa Sawit</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No Berita</span>
                  <span class="detail-value">5KTJ/TAN/BA/01/XII/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Berita Acara</span>
                  <span class="detail-value">31/12/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No PSK</span>
                  <span class="detail-value">5TAN/SPK-IPS/14/VI/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal PSK</span>
                  <span class="detail-value">19/09/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Akhir PSK</span>
                  <span class="detail-value">30/09/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No PO</span>
                  <span class="detail-value">4100249759</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No PR</span>
                  <span class="detail-value">4100249759</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No Miror</span>
                  <span class="detail-value">5201296642</span>
                </div>
              </div>

              <!-- Section Perpajakan -->
              <div class="section-title">Informasi Perpajakan</div>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Jenis PPh</span>
                  <span class="detail-value">PPh 21</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">DPP PPh</span>
                  <span class="detail-value">Rp. 4.500.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">PPh Terhutang</span>
                  <span class="detail-value">Rp. 225.000</span>
                </div>
              </div>
            </div>
          </td>
        </tr>

        <!-- Row 2 -->
        <tr class="main-row" onclick="toggleDetail(2)">
          <td style="text-align: center;">002</td>
          <td>01/01/2025 10:30</td>
          <td>002/SK/2025</td>
          <td>01/01/2025 10:30</td>
          <td>Rp12.0000.0000</td>
          <td>-</td>
          <td><span class="badge-status badge-proses">Siap Dibayar</span></td>
          <td>
            <button class="btn-action btn-upload"><i class="fa-solid fa-upload"></i> Upload Bukti Pembayaran</button>
          </td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-edit"><i class="fa-solid fa-pen"></i></button>
              <button class="btn-action btn-chevron"><i class="fa-solid fa-chevron-down chevron-icon" id="chevron-2"></i></button>
            </div>
          </td>
        </tr>
        <tr class="detail-row" id="detail-2">
          <td colspan="9">
            <div class="detail-content">
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Tanggal Masuk</span>
                  <span class="detail-value">01/01/2025 10:30:00</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Bulan</span>
                  <span class="detail-value">Januari</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tahun</span>
                  <span class="detail-value">2025</span>
                </div>

                <div class="detail-item">
                  <span class="detail-label">No SPP</span>
                  <span class="detail-value">002/SK/2025</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal SPP</span>
                  <span class="detail-value">01/01/2025</span>
                </div>
                <div class="detail-item detail-item-full">
                  <span class="detail-label">Uraian SPP</span>
                  <span class="detail-value">Permohonan Anggaran untuk kegiatan operasional tahun 2025</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Nilai Rp</span>
                  <span class="detail-value">Rp.100000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Kategori</span>
                  <span class="detail-value">Iventasi On Farm</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">SubBagian Pekerjaan</span>
                  <span class="detail-value">Pengangkutan</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Jenis Pembayaran</span>
                  <span class="detail-value">Mitra</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Dibayar Kepada</span>
                  <span class="detail-value">Perkebunan Kelapa Sawit</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No Berita</span>
                  <span class="detail-value">5KTJ/TAN/BA/01/XII/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Berita Acara</span>
                  <span class="detail-value">31/12/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No PSK</span>
                  <span class="detail-value">5TAN/SPK-IPS/14/VI/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal PSK</span>
                  <span class="detail-value">19/09/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Akhir PSK</span>
                  <span class="detail-value">30/09/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No PO</span>
                  <span class="detail-value">4100249759</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No PR</span>
                  <span class="detail-value">4100249759</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">No Miror</span>
                  <span class="detail-value">5201296642</span>
                </div>
              </div>

              <!-- Section Perpajakan -->
              <div class="section-title">Informasi Perpajakan</div>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Jenis PPh</span>
                  <span class="detail-value">PPh 23</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">DPP PPh</span>
                  <span class="detail-value">Rp. 6.000.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">PPh Terhutang</span>
                  <span class="detail-value">Rp. 120.000</span>
                </div>
              </div>
            </div>
          </td>
        </tr>

      </tbody>
    </table>
  </div>
</div>

<script>
  function toggleDetail(rowId) {
    const detailRow = document.getElementById('detail-' + rowId);
    const chevron = document.getElementById('chevron-' + rowId);

    if (detailRow.classList.contains('show')) {
      detailRow.classList.remove('show');
      chevron.classList.remove('rotate');
    } else {
      detailRow.classList.add('show');
      chevron.classList.add('rotate');
    }
  }

  // Search functionality
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-box input');

    if (searchInput) {
      searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('.main-row');

        tableRows.forEach(function(row) {
          const text = row.textContent.toLowerCase();
          if (text.includes(searchTerm)) {
            row.style.display = '';
            // Also show the detail row if it was visible
            const rowIndex = row.getAttribute('onclick').match(/\d+/)[0];
            const detailRow = document.getElementById('detail-' + rowIndex);
            if (detailRow && detailRow.classList.contains('show')) {
              detailRow.style.display = '';
            }
          } else {
            row.style.display = 'none';
            // Hide the detail row too
            const rowIndex = row.getAttribute('onclick').match(/\d+/)[0];
            const detailRow = document.getElementById('detail-' + rowIndex);
            if (detailRow) {
              detailRow.style.display = 'none';
            }
          }
        });
      });
    }
  });

  // Filter Status Tabs Functionality
  function filterStatus(status, event) {
    event.preventDefault();

    // Remove active class from all tabs
    const tabs = document.querySelectorAll('.filter-tab');
    tabs.forEach(tab => tab.classList.remove('active'));

    // Add active class to clicked tab
    event.target.closest('.filter-tab').classList.add('active');

    // Filter table based on status
    const tableRows = document.querySelectorAll('.main-row');

    tableRows.forEach(function(row) {
      const badge = row.querySelector('.badge-status');
      if (!badge) return;

      const badgeText = badge.textContent.toLowerCase();

      if (status === 'all') {
        row.style.display = '';
      } else if (status === 'siap' && badgeText.includes('siap dibayar')) {
        row.style.display = '';
      } else if (status === 'belum' && badgeText.includes('belum')) {
        row.style.display = '';
      } else if (status === 'selesai' && badgeText.includes('sudah dibayar')) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
        // Hide detail row if main row is hidden
        const rowIndex = row.getAttribute('onclick').match(/\d+/)[0];
        const detailRow = document.getElementById('detail-' + rowIndex);
        if (detailRow) {
          detailRow.style.display = 'none';
        }
      }
    });
  }

  // Toggle Filter Dropdown
  function toggleFilterDropdown() {
    const dropdown = document.querySelector('.filter-dropdown');
    dropdown.classList.toggle('active');
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    const dropdown = document.querySelector('.filter-dropdown');
    const filterBtn = document.querySelector('.filter-btn');

    if (dropdown && !dropdown.contains(event.target)) {
      dropdown.classList.remove('active');
    }
  });

  // Prevent dropdown from closing when clicking inside
  document.addEventListener('DOMContentLoaded', function() {
    const dropdownContent = document.querySelector('.filter-dropdown-content');
    if (dropdownContent) {
      dropdownContent.addEventListener('click', function(event) {
        event.stopPropagation();
      });
    }
  });
</script>

@endsection
