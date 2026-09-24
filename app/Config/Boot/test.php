<?php
$filterPath = APPPATH . 'Filters/RedirectFilter.php';
if (file_exists($filterPath)) {
    log_message('info', 'RedirectFilter.php EXISTS at: ' . $filterPath);
} else {
    log_message('error', 'RedirectFilter.php NOT FOUND at: ' . $filterPath);
}

log_message('info', 'Boot test.php loaded successfully');