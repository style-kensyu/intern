<?php

class model extends db{

function __construct(){
    parent:: __construct();
}

/**
 * スケジュール取得
 * @param $date 年月日
 */
function select_schedule($date){
    logger("model::select_schedule() [date]:" . $date);
    $sql = <<<SQL
SELECT sid,text,DATE_FORMAT(reg_date,'%H:%i') as time
FROM userschedule
WHERE reg_date LIKE '{$date}%'
AND status = 0
ORDER BY reg_date
SQL;

    $result = $this->query($sql);
    return $result;
}

/**
 * 月のスケジュール取得
 * @param $date 年月日
 */
function month_schedule($date){
    logger("model::select_schedule() [date]:" . $date);
    $sql = <<<SQL
SELECT DATE_FORMAT(reg_date,'%Y-%m-%d') as sch
FROM userschedule
WHERE reg_date LIKE '{$date}%'
AND status = 0
GROUP BY DATE_FORMAT(reg_date,'%Y-%m-%d')
SQL;
    $result = $this->query($sql);
    return $result;
}

/**
 * スケジュール登録
 * @param $plan 予定
 * @param $date 年月日時間
 */
function reg_schedule($plan,$date){
    logger("model::reg_schedule() [plan]:" . $plan . " [date]:" . $date);
  	$sql = "INSERT INTO userschedule (text,type,reg_date) VALUES ('{$plan}',1,'{$date}')";
    $result = $this->query($sql);
}

/**
 * スケジュール削除
 * @param $sid スケジュールID
 */
function delete_schedule($sid){
    logger("model::delete_schedule() [sid]:" . $sid);
    $sql = "UPDATE userschedule SET status = 1 WHERE sid = {$sid}";
    $result = $this->query($sql);
}

/**
 * スケジュール変更
 * @param $sid スケジュールID
 * @param $plan 予定
 * @param $date 年月日時間
 */
function change_schedule($sid,$plan,$date){
    logger("model::change_schedule() [sid]:" . $sid . " [plan]:" . $plan . " [date]:" .$date);
    $sql = "UPDATE userschedule SET ";

    if($plan) $sql .= "text = '{$plan}' ,";

    $sql .="reg_date = '{$date}' WHERE sid = {$sid}";
    $result = $this->query($sql);
}

/**
 * スケジュール変更
 * @param $sql クエリ
 */
function query($sql){
  logger("model::query() sql \n" . $sql);
  $result = $this->db->query($sql);
  return $result->fetchAll(PDO::FETCH_ASSOC);
}

}
