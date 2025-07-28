// Main JavaScript for Siaga Bapok

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add fade-in animation to cards
    $('.card').addClass('fade-in');

    // Smooth scrolling for anchor links
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if(target.length) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });

    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut();

    // Add loading state to buttons on form submit
    $('form').on('submit', function() {
        $(this).find('button[type="submit"]').html('<span class="loading me-2"></span>Loading...');
    });
});

// Utility Functions
const SiagaBapok = {
    // Format number to Indonesian Rupiah
    formatRupiah: function(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    },

    // Format date to Indonesian format
    formatDate: function(dateString) {
        const months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        const date = new Date(dateString);
        const day = date.getDate();
        const month = months[date.getMonth()];
        const year = date.getFullYear();
        
        return `${day} ${month} ${year}`;
    },

    // Show loading spinner
    showLoading: function(element) {
        $(element).html('<span class="loading me-2"></span>Loading...');
    },

    // Hide loading spinner
    hideLoading: function(element, originalText) {
        $(element).html(originalText);
    },

    // Show notification
    showNotification: function(message, type = 'info') {
        const alertClass = `alert-${type}`;
        const notification = `
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('body').append(notification);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    },

    // Export table to CSV
    exportTableToCSV: function(tableId, filename) {
        const table = document.getElementById(tableId);
        const rows = Array.from(table.querySelectorAll('tr'));
        
        const csvContent = rows.map(row => {
            const cells = Array.from(row.querySelectorAll('th, td'));
            return cells.map(cell => {
                let text = cell.textContent.trim();
                // Remove extra whitespace and newlines
                text = text.replace(/\s+/g, ' ');
                // Escape quotes and wrap in quotes if contains comma
                if (text.includes(',') || text.includes('"')) {
                    text = '"' + text.replace(/"/g, '""') + '"';
                }
                return text;
            }).join(',');
        }).join('\n');
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename || 'data.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },

    // Initialize DataTables if available
    initDataTable: function(tableId, options = {}) {
        if (typeof $.fn.DataTable !== 'undefined') {
            const defaultOptions = {
                responsive: true,
                pageLength: 25,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            };
            
            const finalOptions = Object.assign(defaultOptions, options);
            return $(tableId).DataTable(finalOptions);
        }
    },

    // Chart.js default configuration
    getChartDefaults: function() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return SiagaBapok.formatRupiah(value);
                        }
                    }
                }
            }
        };
    },

    // Update chart data
    updateChart: function(chart, newData) {
        chart.data = newData;
        chart.update();
    },

    // Get price status badge
    getPriceStatusBadge: function(price, average) {
        const diffPercent = ((price - average) / average) * 100;
        
        if (diffPercent > 5) {
            return '<span class="badge bg-danger">Tinggi</span>';
        } else if (diffPercent < -5) {
            return '<span class="badge bg-success">Rendah</span>';
        } else {
            return '<span class="badge bg-secondary">Normal</span>';
        }
    },

    // Validate form data
    validateForm: function(formId) {
        const form = document.getElementById(formId);
        const inputs = form.querySelectorAll('input[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        return isValid;
    }
};

// Global error handler
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    SiagaBapok.showNotification('Terjadi kesalahan. Silakan refresh halaman.', 'danger');
});

// Print functionality
function printPage() {
    window.print();
}

// Back to top button
$(window).scroll(function() {
    if ($(this).scrollTop() > 100) {
        if ($('#backToTop').length === 0) {
            $('body').append(`
                <button id="backToTop" class="btn btn-primary position-fixed" 
                        style="bottom: 20px; right: 20px; z-index: 9999; border-radius: 50%; width: 50px; height: 50px;">
                    <i class="bi bi-arrow-up"></i>
                </button>
            `);
            
            $('#backToTop').on('click', function() {
                $('html, body').animate({scrollTop: 0}, 600);
            });
        }
        $('#backToTop').fadeIn();
    } else {
        $('#backToTop').fadeOut();
    }
});

// Make SiagaBapok globally available
window.SiagaBapok = SiagaBapok;