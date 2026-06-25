/**
 * Cuti DKP Lampung — Global JavaScript
 * DKP Provinsi Lampung
 */

document.addEventListener('DOMContentLoaded', function () {

    // ─── Sidebar Toggle ───────────────────────────────────────────────────
    const sidebar        = document.getElementById('sidebar');
    const sidebarToggle  = document.getElementById('sidebarToggle');
    const sidebarClose   = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() {
        if (sidebar)        sidebar.classList.add('show');
        if (sidebarOverlay) sidebarOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        if (sidebar)        sidebar.classList.remove('show');
        if (sidebarOverlay) sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (sidebarToggle)  sidebarToggle.addEventListener('click', openSidebar);
    if (sidebarClose)   sidebarClose.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    // ─── Auto-dismiss alerts ──────────────────────────────────────────────
    const alertFlashes = document.querySelectorAll('.alert-flash');
    alertFlashes.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // ─── DataTables (auto-init tables with class .datatable) ─────────────
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.datatable').each(function () {
            if (!$.fn.DataTable.isDataTable(this)) {
                $(this).DataTable({
                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/id.json',
                    },
                    pageLength: 10,
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                });
            }
        });
    }

    // ─── Flatpickr date inputs ─────────────────────────────────────────────
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.flatpickr-date', {
            locale: 'id',
            dateFormat: 'Y-m-d',
            allowInput: true,
        });
    }

    // ─── NIP input: angka saja ─────────────────────────────────────────────
    document.querySelectorAll('input[name="nip"]').forEach(el => {
        el.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // ─── Kode uppercase ───────────────────────────────────────────────────
    document.querySelectorAll('input[name="kode_bidang"], input[name="kode_jabatan"], input[name="kode_cuti"]')
        .forEach(el => {
            el.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        });

    // ─── Confirm delete buttons ────────────────────────────────────────────
    // (legacy support — preferred: use konfirmasiHapus() inline)
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const msg  = this.dataset.confirm || 'Apakah Anda yakin?';
            const form = this.closest('form');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Konfirmasi',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Lanjutkan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ef4444',
                }).then(r => { if (r.isConfirmed && form) form.submit(); });
            } else {
                if (confirm(msg) && form) form.submit();
            }
        });
    });

    // ─── Tooltip init ─────────────────────────────────────────────────────
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // ─── Active nav highlight fallback ────────────────────────────────────
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });

    // ─── File input preview ───────────────────────────────────────────────
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        input.addEventListener('change', function () {
            const previewId = this.dataset.preview;
            const preview   = document.getElementById(previewId);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // ─── Prevent double submit ────────────────────────────────────────────
    document.querySelectorAll('form:not(#formVerifikasi):not([data-no-dblclick])').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn && !btn.classList.contains('btn-no-disable')) {
                setTimeout(() => {
                    btn.disabled = true;
                    const original = btn.innerHTML;
                    btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Memproses...`;
                    // Re-enable setelah 8 detik sebagai fallback
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = original;
                    }, 8000);
                }, 0);
            }
        });
    });

    // ─── Number format display ────────────────────────────────────────────
    document.querySelectorAll('[data-number-format]').forEach(el => {
        const num = parseFloat(el.textContent);
        if (!isNaN(num)) {
            el.textContent = num.toLocaleString('id-ID');
        }
    });

});

// ─── Global helpers ───────────────────────────────────────────────────────────

/**
 * Tampilkan SweetAlert2 toast sederhana
 */
function showToast(msg, type = 'success') {
    if (typeof Swal === 'undefined') { alert(msg); return; }
    Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
    }).fire({ icon: type, title: msg });
}

/**
 * Format tanggal ke format Indonesia
 */
function formatTanggal(dateStr) {
    const bulan = ['Januari','Februari','Maret','April','Mei','Juni',
                   'Juli','Agustus','September','Oktober','November','Desember'];
    const d = new Date(dateStr);
    return `${d.getDate()} ${bulan[d.getMonth()]} ${d.getFullYear()}`;
}