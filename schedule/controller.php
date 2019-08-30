<?php
require_once 'settings.php';

try{
// class　を呼び出してるよ！
$calClass   = new calender();
$utileClass = new utile();
$modelClass = new model();
$template   = new template();

logger("======== start puroguramu ========");

// hidden date
$hidden = $utileClass->makeRandStr(10);
// 二重送信防止
if(isset($_SESSION['hidden']) && isset($_POST['hidden'])){
    $sess = $_SESSION['hidden'];
    $post = $_POST['hidden'];
    if($sess == $post){
        logger("controller:: error double post !!");
        $_POST = array();
        session_destroy();
        header("LOCATION: index.php" );
    }
}

/** $dateの形式
 * get で来る形式は　2015-11-22　この形
 */
// get
logger_r("controller:: GET : ",$_GET);
logger_r("controller:: POST: ",$_POST);
$date = "";
if(!empty($_GET['date'])){
  //　Getできたものが本当にdateかどうか
  if(!preg_match('/^([1-9][0-9]{3})\-(0[1-9]{1}|1[0-2]{1})\-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1})$/', $_GET['date'])){
    logger("controller:: error post!!");
    header("LOCATION: index.php" );
  }
	$date = $_GET['date'];
}

if(!$date){
	// 初期
  $ExpansionDateTime= new ExpansionDateTime('now');
  $date = $ExpansionDateTime->format('Y-m-d');
}else{
	//　選択
  $ExpansionDateTime= new ExpansionDateTime($date);
  $date = $ExpansionDateTime->format('Y-m-d');
}

logger("controller:: [select date] : " . $date);

// ==== post ====
if(!empty($_POST['plan'])){
  $plan = $_POST['plan'];
  $time = ($_POST['time']) ? $date.' '.$_POST['time'] : $date.' 00:00:00';
  $modelClass->reg_schedule($plan,$time);//登録
  $_SESSION['hidden'] = $_POST['hidden'];
}

if(!empty($_POST['delete'])){
  $delete = $_POST['sid'][$_POST['delete']-1];
  $modelClass->delete_schedule($delete);//削除
  $_SESSION['hidden'] = $_POST['hidden'];
}

if(!empty($_POST['change'])){
  $change = $_POST['sid'][$_POST['change']-1];
  $plan   = $_POST['change_plan'][$_POST['change']-1];
  $time   = ($_POST['change_time'][$_POST['change']-1]) ? $date.' '.$_POST['change_time'][$_POST['change']-1] : $date.' 00:00:00';
  $modelClass->change_schedule($change,$plan,$time);//変更
  $_SESSION['hidden'] = $_POST['hidden'];
}
// ==============

// 日付取得
$year  = $ExpansionDateTime->format('Y');
$month = $ExpansionDateTime->format('m');
$gengo = $ExpansionDateTime->gengo();
logger("controller:: [year] : ".$year." [month] : ".$month." [gengo] : ".$gengo);

// 選択した日のスケジュール
$schedule = $modelClass->select_schedule($date);
logger_r("controller:: [schedule]",$schedule);

// 選択した月のスケジュール
$reg_month = $year.'-'.$month;
$month_schedule = $modelClass->month_schedule($reg_month);
logger_r("controller:: [month_schedule]",$month_schedule);

$schedule_array = array();
foreach($month_schedule as $m){
  array_push($schedule_array,$m['sch']);
}

// movie
$movie = $utileClass->movie();

// calender
$calender = $calClass->create_calender($schedule_array,$date,$movie);

// holiday
$ExpansionDateTime= new ExpansionDateTime($date);
$holiday = $ExpansionDateTime->holiday();
logger("controller:: [holiday] : ".$holiday);

// image color
$color = $utileClass->monthColor($month);
logger("controller:: [color] : ".$color);

logger("======== end puroguramu ========");

}catch(Exception $e){
    echo $e->getMessage();
}

?>
