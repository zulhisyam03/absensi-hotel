import './bootstrap';
/*
  Add custom scripts here
*/
import.meta.glob([
  '../assets/img/**',
  // '../assets/json/**',
  '../assets/vendor/fonts/**'
]);

// Import jQuery dan buat global (DataTables butuh ini)
import $ from 'jquery';
window.$ = window.jQuery = $;
window.jQuery = $;

// Import Bootstrap JS (termasuk Popper)
import 'bootstrap';
// Import DataTables core dan Bootstrap 5 integration
import 'datatables.net';
import 'datatables.net-bs5';
