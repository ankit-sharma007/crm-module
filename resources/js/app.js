import './bootstrap';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';
import Chart from 'chart.js/auto';

window.toastr = toastr;
window.Chart = Chart;

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
};



import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

