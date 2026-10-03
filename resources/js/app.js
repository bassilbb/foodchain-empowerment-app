import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
// Chart.js is already a project dependency; exposing it globally lets dashboard
// views render charts without a CDN request (works offline and in production).
window.Chart = Chart;

Alpine.start();
