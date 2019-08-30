<?php
  SESSION_START();
  date_default_timezone_set('Asia/Tokyo');

  // 読み込み
  require_once 'logger.php';
  require_once 'db.php';
  require_once 'calender.php';
  require_once 'expansiondatetime.php';
  require_once 'utile.php';
  require_once 'model.php';
  require_once 'template.php';

  $logger     = new logger();

  // logを管理してます。
  function logger($log=""){
    $time = date('Y-m-d H:i:s', time());
    error_log("[$time] $log\r\n",3,ini_get("error_log"));
  }

  function logger_r($text="",$log=[]){
    $time = date('Y-m-d H:i:s', time());
    
    if(count($log) == 0) $log = null;

    error_log("[$time] $text\r\n". print_r($log,true),3,ini_get("error_log"));
  }
?>
