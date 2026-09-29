import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

// ponytail: Chart global sekali di sini, register per-halaman menyusul di TAHAP 8/9
// (sweetalert2 & flatpickr TIDAK diimport global — YAGNI, import per-halaman saat dibutuhkan)
window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();
