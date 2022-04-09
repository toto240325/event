<?php 

function _date($format = "r", $timestamp = false, $timezone = false)
{
    $userTimezone = new DateTimeZone(!empty($timezone) ? $timezone : 'GMT');
    $gmtTimezone = new DateTimeZone('GMT');
    $myDateTime = new DateTime(($timestamp != false ? date("r", (int) $timestamp) : date("r")), $gmtTimezone);
    $offset = $userTimezone->getOffset($myDateTime);
    return date($format, ($timestamp != false ? (int) $timestamp : $myDateTime->format('U')) + $offset);
}

// $today = _date("Y-m-d", false, 'Europe/Paris');
// echo "currtime" . $currTime."\n";


function convert_UTC_to_CET($time_str) {
  $src_dt_str = $time_str;
  // $src_tz =  new DateTimeZone('Europe/London');
  $src_tz =  new DateTimeZone('UTC');
  $dest_tz = new DateTimeZone('Europe/Paris');
  $dt = new DateTime($src_dt_str, $src_tz);
  $dt->setTimeZone($dest_tz);
  $dest_dt_str = $dt->format('Y-m-d H:i:s');
  return $dest_dt_str;
}

?>