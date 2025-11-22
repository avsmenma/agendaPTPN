@extends('layouts/app')

@section('content')
<style>
/* Owner Dashboard - Modern Clean Style */
:root {
  --primary-color: #007bff;
  --success-color: #28a745;
  --warning-color: #ffc107;
  --danger-color: #dc3545;
  --info-color: #17a2b8;
  --secondary-color: #6f42c1;
  --light-bg: #f8f9fa;
  --border-color: #dee2e6;
  --text-primary: #343a40;
  --text-secondary: #6c757d;
  --text-muted: #adb5bd;
}

body {
  background: #ffffff !important;
  color: var(--text-primary);
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Header Styles */
.dashboard-header {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: white;
  padding: 1.5rem 0;
  margin-bottom: 2rem;
  border-radius: 0;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.header-title {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
  letter-spacing: -0.5px;
}

/* Main Layout */
.dashboard-container {
  display: flex;
  gap: 1.5rem;
  min-height: calc(100vh - 120px);
}

.document-list {
  flex: 1;
  min-width: 0;
}

.info-panel {
  width: 380px;
  min-width: 380px;
  position: sticky;
  top: 0;
}

@media (max-width: 1200px) {
  .dashboard-container {
    flex-direction: column;
  }

  .info-panel {
    width: 100%;
    position: static;
  }
}

/* Document Cards */
.document-card {
  background: white;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 1.25rem;
  margin-bottom: 1rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.08);
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
}

.document-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  border-color: var(--primary-color);
}

.document-card.overdue {
  border-left: 4px solid var(--danger-color);
}

.document-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.document-number {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 0.5rem;
}

.document-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.expand-icon {
  width: 32px;
  height: 32px;
  background: var(--light-bg);
  border: 1px solid var(--border-color);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.expand-icon:hover {
  background: var(--primary-color);
  color: white;
  transform: scale(1.1);
}

.expand-icon.expanded {
  transform: rotate(180deg);
}

.document-body {
  margin-bottom: 1rem;
}

.document-value {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--success-color);
  margin-bottom: 0.5rem;
}

.document-info {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
}

.document-status {
  display: inline-block;
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
}

/* Progress Bar */
.progress-container {
  margin-bottom: 1rem;
}

.progress-bar {
  height: 8px;
  background: #e9ecef;
  border-radius: 4px;
  overflow: hidden;
  position: relative;
}

.progress-fill {
  height: 100%;
  border-radius: 4px;
  transition: width 0.5s ease;
  position: relative;
}

.progress-fill::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 100%);
}

.progress-percentage {
  font-size: 0.75rem;
  font-weight: 500;
  color: var(--text-secondary);
  margin-top: 0.25rem;
}

/* Timeline */
.document-timeline {
  display: none;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px dashed var(--border-color);
  max-height: 0;
  overflow: hidden;
  transition: all 0.3s ease;
}

.document-timeline.show {
  display: block;
  max-height: 2000px;
  overflow-y: auto;
}

.timeline-event {
  background: var(--light-bg);
  border-left: 4px solid;
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 0 8px 8px 0;
}

.timeline-event.created {
  border-left-color: var(--success-color);
}

.timeline-event.sent {
  border-left-color: var(--primary-color);
}

.timeline-event.processed {
  border-left-color: var(--warning-color);
}

.timeline-event.returned {
  border-left-color: var(--danger-color);
}

.timeline-event.completed {
  border-left-color: var(--success-color);
}

.timeline-icon {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  line-height: 1;
}

.timeline-title {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.timeline-time {
  font-size: 0.8rem;
  color: var(--text-muted);
  margin-bottom: 0.75rem;
}

.timeline-info {
  font-size: 0.8rem;
  color: var(--text-secondary);
  line-height: 1.5;
}

.timeline-info-item {
  margin-bottom: 0.25rem;
}

.timeline-info-item strong {
  color: var(--text-primary);
}

/* Info Panel */
.info-panel {
  background: white;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.info-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border-color);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
}

.stat-card {
  background: var(--light-bg);
  padding: 1rem;
  border-radius: 8px;
  text-align: center;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--primary-color);
  margin-bottom: 0.25rem;
}

.stat-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  font-weight: 500;
}

/* Activity Section */
.activity-section {
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.activity-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-height: 200px;
  overflow-y: auto;
}

.activity-item {
  padding: 0.75rem;
  background: var(--light-bg);
  border-radius: 8px;
  font-size: 0.875rem;
  border-left: 3px solid var(--info-color);
}

.activity-item.success {
  border-left-color: var(--success-color);
}

.activity-item.warning {
  border-left-color: var(--warning-color);
}

.activity-item.danger {
  border-left-color: var(--danger-color);
}

/* Animations */
@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideUp {
  from {
    opacity: 1;
    transform: translateY(0);
  }
  to {
    opacity: 0;
    transform: translateY(-20px);
  }
}

.timeline-event {
  animation: slideDown 0.3s ease-out;
}

.document-timeline.collapsing .timeline-event {
  animation: slideUp 0.3s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
  .dashboard-container {
    flex-direction: column;
  }

  .info-panel {
    width: 100%;
    position: static;
  }

  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .header-title {
    font-size: 1.5rem;
  }

  .document-card {
    padding: 1rem;
  }

  .document-number {
    font-size: 1rem;
  }
}

/* Loading State */
.loading-skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

.skeleton-card {
  height: 150px;
  border-radius: 12px;
  margin-bottom: 1rem;
}

/* Scrollbar Styling */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Utility Classes */
.text-primary { color: var(--text-primary); }
.text-secondary { color: var(--text-secondary); }
.text-muted { color: var(--text-muted); }
.text-success { color: var(--success-color); }
.text-warning { color: var(--warning-color); }
.text-danger { color: var(--danger-color); }
.text-info { color: var(--info-color); }

.bg-light { background-color: var(--light-bg); }
.bg-success { background-color: var(--success-color); }
.bg-warning { background-color: var(--warning-color); }
.bg-danger { background-color: var(--danger-color); }
.bg-info { background-color: var(--info-color); }

.rounded { border-radius: 8px; }
.rounded-pill { border-radius: 50px; }
.shadow-sm { box-shadow: 0 2px 4px rgba(0,0,0,0.08); }
.shadow { box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
</style>

<!-- Dashboard Header -->
<div class="dashboard-header">
  <div class="container">
    <h1 class="header-title">
      🎯 OWNER COMMAND CENTER
    </h1>
    <p class="text-white-50 mb-0">Real-Time Document Monitoring & Analytics</p>
  </div>
</div>

<div class="container">
  <div class="dashboard-container">
    <!-- Document List (60%) -->
    <div class="document-list">
      <div id="documentList">
        @if($documents->isEmpty())
          <div class="text-center py-5">
            <div class="mb-4">
              <i class="fas fa-inbox fa-3x text-muted"></i>
            </div>
            <h4 class="text-muted">Belum ada dokumen</h4>
            <p class="text-muted">Dokumen akan muncul di sini ketika dibuat</p>
          </div>
        @else
          @foreach($documents as $index => $dokumen)
            <div class="document-card {{ $dokumen['is_overdue'] ? 'overdue' : '' }}"
                 data-document-id="{{ $dokumen['id'] }}"
                 onclick="toggleTimeline({{ $dokumen['id'] }})">
              <div class="document-header">
                <div>
                  <div class="document-number">
                    📄 #{{ $index + 1 }} - {{ $dokumen['nomor_agenda'] }}
                  </div>
                  <div class="document-info">
                    {{ $dokumen['nomor_spp'] }}
                  </div>
                </div>
                <div class="document-actions">
                  <div class="expand-icon" id="expand-{{ $dokumen['id'] }}">
                    <i class="fas fa-chevron-down"></i>
                  </div>
                </div>
              </div>

              <div class="document-body">
                <div class="document-value">
                  Rp. {{ number_format($dokumen['nilai_rupiah'], 0, ',', '.') }}
                </div>
                <div class="document-info">
                  📍 <strong>Posisi:</strong> {{ $dokumen['current_handler'] ?? 'Tidak ada' }}
                </div>
                @if($dokumen['deadline_info'])
                <div class="document-info">
                  ⏰ <strong>Deadline:</strong>
                  <span class="text-{{ $dokumen['deadline_info']['class'] }}">
                    {{ $dokumen['deadline_info']['text'] }}
                  </span>
                </div>
                @endif
              </div>

              <div class="progress-container">
                <div class="progress-bar">
                  <div class="progress-fill"
                       style="width: {{ $dokumen['progress_percentage'] }}%; background: {{ $dokumen['progress_color'] }};">
                  </div>
                </div>
                <div class="progress-percentage">
                  {{ $dokumen['progress_percentage'] }}%
                </div>
              </div>

              <div class="document-status">
                <span style="background-color: {{ $dokumen['status_badge_color'] }};">
                  {{ ucfirst(str_replace('_', ' ', $dokumen['status'])) }}
                </span>
                @if($dokumen['is_overdue'])
                  <span class="ms-2" style="background-color: var(--danger-color);">
                    Overdue
                  </span>
                @endif
              </div>

              <!-- Timeline (Hidden by default) -->
              <div class="document-timeline" id="timeline-{{ $dokumen['id'] }}">
                <div class="timeline-loading d-none">
                  <div class="skeleton-card loading-skeleton"></div>
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    </div>

    <!-- Info Panel (40%) -->
    <div class="info-panel">
      <!-- Statistics -->
      <div class="info-title">
        📊 STATISTIK
      </div>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-value">{{ $stats['total_documents'] }}</div>
          <div class="stat-label">Total Dokumen</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ $stats['active_processing'] }}</div>
          <div class="stat-label">Sedang Diproses</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ $stats['completed_today'] }}</div>
          <div class="stat-label">Selesai Hari Ini</div>
        </div>
        <div class="stat-card">
          <div class="stat-value text-danger">{{ $stats['overdue_documents'] }}</div>
          <div class="stat-label">Overdue</div>
        </div>
        <div class="stat-card">
          <div class="stat-value">{{ $stats['avg_processing_time'] }}</div>
          <div class="stat-label">Rata-rata Proses</div>
        </div>
      </div>

      <!-- Department Performance -->
      <div class="activity-section">
        <div class="activity-title">
          ⚡ PERFORMA DEPARTEMEN
        </div>
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-value text-success">{{ $stats['fastest_department'] }}</div>
            <div class="stat-label">Tercepat</div>
          </div>
          <div class="stat-card">
            <div class="stat-value text-danger">{{ $stats['slowest_department'] }}</div>
            <div class="stat-label">Terlama</div>
          </div>
        </div>
      </div>

      <!-- Activity Today -->
      <div class="activity-section">
        <div class="activity-title">
          📈 AKTIVITAS HARI INI
        </div>
        <div class="activity-list">
          <div class="activity-item success">
            <strong>5 dokumen</strong> berhasil diselesaikan
            <br><small>{{ now()->format('H:i') }} WIB</small>
          </div>
          <div class="activity-item warning">
            <strong>2 dokumen</strong> mendekati deadline
            <br><small>2 jam lalu</small>
          </div>
          <div class="activity-item danger">
            <strong>1 dokumen</strong> terlambat dari deadline
            <br><small>{{ now()->format('H:i') }} WIB</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Timeline Modal -->
<div class="modal fade" id="timelineModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">📋 Track Record Dokumen</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="timelineContent">
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3">Memuat track record dokumen...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Global variables
let expandedCards = new Set();

// Toggle timeline for document
function toggleTimeline(documentId) {
  const timelineElement = document.getElementById(`timeline-${documentId}`);
  const expandIcon = document.getElementById(`expand-${documentId}`);

  if (expandedCards.has(documentId)) {
    // Collapse
    timelineElement.classList.remove('show');
    expandIcon.classList.remove('expanded');
    expandedCards.delete(documentId);
  } else {
    // Expand - fetch timeline data
    expandIcon.classList.add('expanded');
    loadDocumentTimeline(documentId);
  }
}

// Load document timeline via AJAX
function loadDocumentTimeline(documentId) {
  const timelineElement = document.getElementById(`timeline-${documentId}`);

  // Show loading skeleton
  timelineElement.innerHTML = `
    <div class="timeline-loading">
      <div class="skeleton-card loading-skeleton"></div>
    </div>
  `;

  // Fetch timeline data
  fetch(`/owner/api/document-timeline/${documentId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        renderTimeline(timelineElement, data.timeline);
        expandedCards.add(documentId);

        // Add show class after content is loaded
        setTimeout(() => {
          timelineElement.classList.add('show');
        }, 50);
      } else {
        throw new Error('Failed to load timeline');
      }
    })
    .catch(error => {
      console.error('Error loading timeline:', error);
      timelineElement.innerHTML = `
        <div class="alert alert-danger m-3">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Gagal memuat track record: ${error.message}
        </div>
      `;
      expandIcon.classList.remove('expanded');
    });
}

// Render timeline events
function renderTimeline(container, timeline) {
  if (!timeline || timeline.length === 0) {
    container.innerHTML = `
      <div class="alert alert-info m-3">
        <i class="fas fa-info-circle me-2"></i>
        Belum ada aktivitas untuk dokumen ini
      </div>
    `;
    return;
  }

  let timelineHTML = '';

  timeline.forEach((event, index) => {
    const eventIcon = event.icon;
    const eventTitle = event.title;
    const eventTime = event.timestamp;
    const duration = event.duration || '';
    const info = event.info || {};

    // Build info HTML
    let infoHTML = '';
    Object.entries(info).forEach(([key, value]) => {
      infoHTML += `
        <div class="timeline-info-item">
          <strong>${key}:</strong> ${value}
        </div>
      `;
    });

    if (duration) {
      infoHTML += `
        <div class="timeline-info-item">
          <strong>Durasi:</strong> ${duration}
        </div>
      `;
    }

    timelineHTML += `
      <div class="timeline-event ${event.type}">
        <div class="timeline-icon">${eventIcon}</div>
        <div class="timeline-title">${eventTitle}</div>
        <div class="timeline-time">📅 ${eventTime}</div>
        ${infoHTML ? `<div class="timeline-info">${infoHTML}</div>` : ''}
      </div>
    `;
  });

  container.innerHTML = timelineHTML;
}

// Modal for detailed timeline view
function showDetailedTimeline(documentId) {
  const modal = new bootstrap.Modal(document.getElementById('timelineModal'));
  const modalContent = document.getElementById('timelineContent');

  modal.show();

  // Load timeline data
  loadDocumentTimelineInModal(documentId, modalContent);
}

// Load timeline in modal
function loadDocumentTimelineInModal(documentId, container) {
  container.innerHTML = `
    <div class="text-center py-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-3">Memuat track record lengkap...</p>
    </div>
  `;

  fetch(`/owner/api/document-timeline/${documentId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        renderDetailedTimeline(container, data.dokumen, data.timeline);
      } else {
        throw new Error('Failed to load timeline');
      }
    })
    .catch(error => {
      console.error('Error loading timeline:', error);
      container.innerHTML = `
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Gagal memuat track record: ${error.message}
        </div>
      `;
    });
}

// Render detailed timeline in modal
function renderDetailedTimeline(container, dokumen, timeline) {
  let html = `
    <div class="document-header-modal">
      <h6 class="mb-1">📄 ${dokumen.nomor_agenda}</h6>
      <div class="document-info-modal">
        <div class="row">
          <div class="col-6">
            <strong>Nomor SPP:</strong> ${dokumen.nomor_spp}<br>
            <strong>Uraian:</strong> ${dokumen.uraian_spp}
          </div>
          <div class="col-6">
            <strong>Nilai:</strong> Rp. ${dokumen.nilai_rupiah.toLocaleString('id-ID')}<br>
            <strong>Dibuat:</strong> ${dokumen.created_at}
          </div>
        </div>
      </div>
    </div>
    <div class="timeline-modal">
  `;

  timeline.forEach((event, index) => {
    const eventIcon = event.icon;
    const eventTitle = event.title;
    const eventTime = event.timestamp;
    const duration = event.duration || '';
    const info = event.info || {};

    // Build info HTML
    let infoHTML = '';
    Object.entries(info).forEach(([key, value]) => {
      infoHTML += `
        <div class="timeline-info-item">
          <strong>${key}:</strong> ${value}
        </div>
      `;
    });

    if (duration) {
      infoHTML += `
        <div class="timeline-info-item">
          <strong>Durasi:</strong> ${duration}
        </div>
      `;
    }

    if (event.total_duration) {
      infoHTML += `
        <div class="timeline-info-item text-success font-weight-bold">
          <strong>Total Waktu Proses:</strong> ${event.total_duration}
        </div>
      `;
    }

    html += `
      <div class="timeline-event ${event.type}">
        <div class="timeline-icon">${eventIcon}</div>
        <div class="timeline-title">${eventTitle}</div>
        <div class="timeline-time">📅 ${eventTime}</div>
        ${infoHTML ? `<div class="timeline-info">${infoHTML}</div>` : ''}
      </div>
    `;
  });

  html += `
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Tutup
      </button>
    </div>
  `;

  container.innerHTML = html;
}

// Auto-refresh functionality (optional)
let refreshInterval;

function startAutoRefresh() {
  // Refresh every 30 seconds
  refreshInterval = setInterval(() => {
    location.reload();
  }, 30000);
}

function stopAutoRefresh() {
  if (refreshInterval) {
    clearInterval(refreshInterval);
    refreshInterval = null;
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
  // Collapse all timelines by default
  document.querySelectorAll('.document-timeline').forEach(timeline => {
    timeline.style.maxHeight = '0';
    timeline.style.overflow = 'hidden';
  });

  // Optional: Start auto-refresh
  // startAutoRefresh();
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
  // Press 'R' to refresh
  if (e.key === 'r' && !e.ctrlKey && !e.metaKey) {
    location.reload();
  }

  // Press 'Esc' to close all expanded cards
  if (e.key === 'Escape') {
    expandedCards.forEach(documentId => {
      toggleTimeline(documentId);
    });
  }
});
</script>

@endsection