@extends('layouts/app');
@section('content')
<style>
      h2 {
    background: linear-gradient(135deg, #083E40 0%, #889717 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 24px;
    font-weight: 700;
  }
      .chart-container {
    background: linear-gradient(135deg, #ffffff 0%, #f8faf8 100%);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 8px 32px rgba(8, 62, 64, 0.1), 0 2px 8px rgba(136, 151, 23, 0.05);
    border: 1px solid rgba(8, 62, 64, 0.08);
    position: relative;
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid rgba(8, 62, 64, 0.1);
  }

  .chart-title {
    font-size: 16px;
    font-weight: 700;
    color: #083E40;
    letter-spacing: 0.3px;
  }

  .chart-actions {
    display: flex;
    gap: 10px;
    align-items: center;
  }

  .chart-filter {
    padding: 8px 16px;
    border: 2px solid rgba(8, 62, 64, 0.1);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: white;
    color: #083E40;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .chart-filter:focus {
    outline: none;
    border-color: #889717;
    box-shadow: 0 0 0 4px rgba(136, 151, 23, 0.1);
  }
.chart-wrapper {
    position: relative;
    min-height: 300px;
    padding: 20px 0;
  }
    .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    position: absolute;
    top: 24px;
    right: 24px;
  }

   .status-dot.active {
    background: #28a745;
    box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
    animation: pulse 2s infinite;
  }
   canvas {
    max-height: 350px;
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

  /* Improved Search Box Layout */
  .search-box {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: nowrap;
  }

  .search-box .input-group {
    flex: 1;
    max-width: 400px;
    min-width: 250px;
  }

  .filter-section {
    flex-shrink: 0;
    position: relative;
  }

  /* Custom Dropdown Styling */
  .custom-dropdown {
    position: relative;
    display: inline-block;
  }

  .dropdown-btn {
    padding: 10px 16px;
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
    min-width: 120px;
    justify-content: space-between;
  }

  .dropdown-btn:hover {
    border-color: #889717;
    background: rgba(136, 151, 23, 0.05);
  }

  .dropdown-btn i {
    font-size: 12px;
    transition: transform 0.3s ease;
  }

  .custom-dropdown.active .dropdown-btn i {
    transform: rotate(180deg);
  }

  .dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    margin-top: 8px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(8, 62, 64, 0.15);
    border: 1px solid rgba(8, 62, 64, 0.1);
    padding: 8px;
    min-width: 100%;
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
  }

  .custom-dropdown.active .dropdown-content {
    display: block;
  }

  .dropdown-item {
    padding: 10px 14px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
    font-weight: 500;
    color: #083E40;
  }

  .dropdown-item:hover {
    background: rgba(8, 62, 64, 0.05);
  }

  .dropdown-item.selected {
    background: linear-gradient(135deg, #083E40 0%, #889717 100%);
    color: white;
  }

  /* Table Styling */
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

  .detail-label {
    font-size: 11px;
    font-weight: 600;
    color: #083E40;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .detail-value {
    font-size: 13px;
    color: #333;
    font-weight: 500;
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

  .detail-item-full {
    grid-column: 1 / -1;
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
</style>

<h2 style="margin-bottom: 20px; font-weight: 700;">{{ $title }}</h2>


<!-- Search & Filter Box -->
<div class="search-box">
  <div class="input-group">
    <span class="input-group-text">
      <i class="fa-solid fa-magnifying-glass text-muted"></i>
    </span>
    <input type="text" class="form-control" placeholder="Cari nomor SPP, tanggal, atau nilai...">
  </div>
  <div class="filter-section">
    <div class="custom-dropdown" id="yearDropdown">
      <button class="dropdown-btn" onclick="toggleDropdown('yearDropdown')">
        <span class="dropdown-label">Year</span>
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="dropdown-content">
        <div class="dropdown-item selected" onclick="selectDropdownItem('yearDropdown', 'Year', this)">Year</div>
        <div class="dropdown-item" onclick="selectDropdownItem('yearDropdown', '2025', this)">2025</div>
        <div class="dropdown-item" onclick="selectDropdownItem('yearDropdown', '2024', this)">2024</div>
        <div class="dropdown-item" onclick="selectDropdownItem('yearDropdown', '2023', this)">2023</div>
      </div>
    </div>
  </div>
  <div class="filter-section">
    <div class="custom-dropdown" id="showDropdown">
      <button class="dropdown-btn" onclick="toggleDropdown('showDropdown')">
        <span class="dropdown-label">Show</span>
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="dropdown-content">
        <div class="dropdown-item selected" onclick="selectDropdownItem('showDropdown', 'Show', this)">Show</div>
        <div class="dropdown-item" onclick="selectDropdownItem('showDropdown', 'All Dokumen', this)">All Dokumen</div>
        <div class="dropdown-item" onclick="selectDropdownItem('showDropdown', '10', this)">10</div>
        <div class="dropdown-item" onclick="selectDropdownItem('showDropdown', '20', this)">20</div>
      </div>
    </div>
  </div>
  <div class="filter-section">
    <div class="custom-dropdown" id="bagianDropdown">
      <button class="dropdown-btn" onclick="toggleDropdown('bagianDropdown')">
        <span class="dropdown-label">Bagian</span>
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="dropdown-content">
        <div class="dropdown-item selected" onclick="selectDropdownItem('bagianDropdown', 'Bagian', this)">Bagian</div>
        <div class="dropdown-item" onclick="selectDropdownItem('bagianDropdown', 'Yuni', this)">Yuni</div>
        <div class="dropdown-item" onclick="selectDropdownItem('bagianDropdown', 'Perpajakan', this)">Perpajakan</div>
        <div class="dropdown-item" onclick="selectDropdownItem('bagianDropdown', 'Akutansi', this)">Akutansi</div>
      </div>
    </div>
  </div>
  <a href="#"><button class="btn-excel">Ekspor ke PDF</button></a>
</div>

<div class="chart-container">
  <div class="status-dot active"></div>
  <div class="chart-header">
    <h3 class="chart-title">Statistik Keterlambatan</h3>
    <!-- <div class="chart-actions">
      <input type="number" name="quantity" id="23" min="1" max="10" class="dropTahun" placeholder="Tahun">
    </div> -->
  </div>
  <div class="chart-wrapper">
    <canvas id="lineChart"></canvas>
  </div>
</div>

<div class="table-container">
  <h6>
    <span style="color: #1a4d3e; text-decoration: none; font-size: 24px;">Daftar Dokumen Terlambat</span>
  </h6>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr class="table table-dark">
          <th>No</th>
          <th>Nomor SPP</th>
          <th>Tanggal Masuk</th>
          <th>Nilai Rupiah</th>
          <th>Durasi Keterlambatan</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <!-- Row 1 -->
        <tr class="main-row" onclick="toggleDetail(1)">
          <td style="text-align: center;">001</td>
          <td>001/SK/2025</td>
          <td>01/01/2025 10:30</td>
          <td>Rp12.000.000</td>
          <td>5 Hari</td>
          <td><span class="badge-status badge-selesai">Sudah Dibayar</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-chevron"><i class="fa-solid fa-chevron-down chevron-icon" id="chevron-1"></i></button>
            </div>
          </td>
        </tr>
        <tr class="detail-row" id="detail-1">
          <td colspan="7">
            <div class="detail-content">
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Nomor SPP</span>
                  <span class="detail-value">001/SK/2025</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Masuk</span>
                  <span class="detail-value">01/01/2025 10:30</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Nilai Rupiah</span>
                  <span class="detail-value">Rp 12.000.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Durasi Keterlambatan</span>
                  <span class="detail-value">5 Hari</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Target</span>
                  <span class="detail-value">27/12/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Selesai</span>
                  <span class="detail-value">01/01/2025</span>
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
                  <span class="detail-value">Rp 10.000.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">PPh Terhutang</span>
                  <span class="detail-value">Rp 500.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tarif PPh</span>
                  <span class="detail-value">5%</span>
                </div>
              </div>
            </div>
          </td>
        </tr>

        <!-- Row 2 -->
        <tr class="main-row" onclick="toggleDetail(2)">
          <td style="text-align: center;">002</td>
          <td>002/SK/2025</td>
          <td>02/01/2025 09:15</td>
          <td>Rp 8.500.000</td>
          <td>3 Hari</td>
          <td><span class="badge-status badge-selesai">Sudah Dibayar</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-chevron"><i class="fa-solid fa-chevron-down chevron-icon" id="chevron-2"></i></button>
            </div>
          </td>
        </tr>
        <tr class="detail-row" id="detail-2">
          <td colspan="7">
            <div class="detail-content">
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Nomor SPP</span>
                  <span class="detail-value">002/SK/2025</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Masuk</span>
                  <span class="detail-value">02/01/2025 09:15</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Nilai Rupiah</span>
                  <span class="detail-value">Rp 8.500.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Durasi Keterlambatan</span>
                  <span class="detail-value">3 Hari</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Target</span>
                  <span class="detail-value">30/12/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Selesai</span>
                  <span class="detail-value">02/01/2025</span>
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
                  <span class="detail-value">Rp 8.000.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">PPh Terhutang</span>
                  <span class="detail-value">Rp 160.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tarif PPh</span>
                  <span class="detail-value">2%</span>
                </div>
              </div>
            </div>
          </td>
        </tr>

        <!-- Row 3 -->
        <tr class="main-row" onclick="toggleDetail(3)">
          <td style="text-align: center;">003</td>
          <td>003/SK/2025</td>
          <td>03/01/2025 14:20</td>
          <td>Rp 15.000.000</td>
          <td>7 Hari</td>
          <td><span class="badge-status badge-selesai">Sudah Dibayar</span></td>
          <td>
            <div class="action-buttons">
              <button class="btn-action btn-chevron"><i class="fa-solid fa-chevron-down chevron-icon" id="chevron-3"></i></button>
            </div>
          </td>
        </tr>
        <tr class="detail-row" id="detail-3">
          <td colspan="7">
            <div class="detail-content">
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Nomor SPP</span>
                  <span class="detail-value">003/SK/2025</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Masuk</span>
                  <span class="detail-value">03/01/2025 14:20</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Nilai Rupiah</span>
                  <span class="detail-value">Rp 15.000.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Durasi Keterlambatan</span>
                  <span class="detail-value">7 Hari</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Target</span>
                  <span class="detail-value">27/12/2024</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tanggal Selesai</span>
                  <span class="detail-value">03/01/2025</span>
                </div>
              </div>

              <!-- Section Perpajakan -->
              <div class="section-title">Informasi Perpajakan</div>
              <div class="detail-grid">
                <div class="detail-item">
                  <span class="detail-label">Jenis PPh</span>
                  <span class="detail-value">PPh 4 Ayat 2</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">DPP PPh</span>
                  <span class="detail-value">Rp 15.000.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">PPh Terhutang</span>
                  <span class="detail-value">Rp 300.000</span>
                </div>
                <div class="detail-item">
                  <span class="detail-label">Tarif PPh</span>
                  <span class="detail-value">2%</span>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Chart 1: Line Chart - Statistik Jumlah Dokumen
  const lineCtx = document.getElementById('lineChart').getContext('2d');
  const lineChart = new Chart(lineCtx, {
    type: 'line',
    data: {
      labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
      datasets: [{
        label: 'Jumlah Dokumen',
        data: [65, 59, 80, 81, 56, 95, 70, 85, 60, 90, 75, 88],
        borderColor: '#083E40',
        backgroundColor: 'rgba(8, 62, 64, 0.1)',
        borderWidth: 3,
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#083E40',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(8, 62, 64, 0.9)',
          padding: 12,
          cornerRadius: 8,
          titleFont: {
            size: 14,
            weight: 'bold'
          },
          bodyFont: {
            size: 13
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(8, 62, 64, 0.05)',
            drawBorder: false
          },
          ticks: {
            color: '#083E40',
            font: {
              size: 12,
              weight: '500'
            }
          }
        },
        x: {
          grid: {
            display: false,
            drawBorder: false
          },
          ticks: {
            color: '#083E40',
            font: {
              size: 11,
              weight: '500'
            }
          }
        }
      }
    }
  });

  // Custom Dropdown Functions
  function toggleDropdown(dropdownId) {
    const dropdown = document.getElementById(dropdownId);
    const allDropdowns = document.querySelectorAll('.custom-dropdown');

    // Close all other dropdowns
    allDropdowns.forEach(dd => {
      if (dd.id !== dropdownId) {
        dd.classList.remove('active');
      }
    });

    // Toggle current dropdown
    dropdown.classList.toggle('active');
  }

  function selectDropdownItem(dropdownId, value, element) {
    const dropdown = document.getElementById(dropdownId);
    const label = dropdown.querySelector('.dropdown-label');
    const items = dropdown.querySelectorAll('.dropdown-item');

    // Update label
    label.textContent = value;

    // Remove selected class from all items
    items.forEach(item => item.classList.remove('selected'));

    // Add selected class to clicked item
    element.classList.add('selected');

    // Close dropdown
    dropdown.classList.remove('active');

    // Here you can add logic to filter data based on selection
    console.log(`Selected ${dropdownId}: ${value}`);
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', function(event) {
    if (!event.target.closest('.custom-dropdown')) {
      const allDropdowns = document.querySelectorAll('.custom-dropdown');
      allDropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
      });
    }
  });

  // Prevent dropdown from closing when clicking inside the button
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', function(event) {
      event.stopPropagation();
    });
  });

  // Toggle Detail Row Function
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
</script>

@endsection