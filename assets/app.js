/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import './styles/app.scss';
// Import Bootstrap DatetimePicker
//import 'eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css';
// import 'eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js';

// any CSS you import will output into a single css file (app.css in this case)
// import './styles/app.css';
import $ from 'jquery';
global.$ = global.jQuery = $;

// Import jQuery UI
import 'jquery-ui/ui/widgets/datepicker';
import 'jquery-ui/themes/base/all.css';