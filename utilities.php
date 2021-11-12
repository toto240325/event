<?php 

  function convert_UTC_to_CET($time) {
    $src_dt = $time;
    $src_tz =  new DateTimeZone('Europe/London');
    $dest_tz = new DateTimeZone('Europe/Paris');
    $dt = new DateTime($src_dt, $src_tz);
    $dt->setTimeZone($dest_tz);
    $dest_dt = $dt->format('Y-m-d H:i:s');
    return $dest_dt;
  }

?>