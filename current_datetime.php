<!-- current_datetime.php -->
<?php
date_default_timezone_set('Asia/Bangkok');
$current_date_time = date('l, d F Y H:i:s');
?>
<p>Current Date and Time: <?php echo $current_date_time; ?></p>
