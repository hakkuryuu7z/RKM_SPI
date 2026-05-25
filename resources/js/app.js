import './bootstrap';
import 'bootstrap'; // Ini framework Bootstrap-nya
import Swal from 'sweetalert2';

// Bikin Swal jadi global biar bisa dipanggil di file .blade.php
window.Swal = Swal;