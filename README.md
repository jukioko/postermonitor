# postermonitor
Web-based viewer for posters and a list of upcoming events.

The viewer is only HTML and javascript, and depends on php scripts to get data via AJAX.
The javascript consists of jQuery, the lightslider-plugin (http://sachinchoolur.github.io/lightslider/), clock.js for the clock and finally viewer.js which contains all variables and functions for the activities list and posterviewer

The php-file check_poster.php is run daily in a cronjob to remove outdated posters.
poster/public# php -f poster/public/check_posters.php >> poster/public/log.html 2>&1
