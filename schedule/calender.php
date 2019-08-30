<?php

class calender{

	// const wd = array("日","月","火","水","木","金","土");
	const wd = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");

	/**
   * カレンダー作成
   * @param $schedule_array スケジュール
	 * @param $datetime 選択された日付
	 * @param $movie 映画
   */
	public function create_calender($schedule_array,$datetime,$movie){
		$selectDateTime = new ExpansionDateTime($datetime);

		$year = $selectDateTime->format('Y');
		$month= $selectDateTime->format('m');
		$day  = $selectDateTime->format('d');
		logger("calender::create_calender [year] : ".$year." [month] :".$month." [day] :".$day);

		//月初め
    	$firstDate = new ExpansionDateTime('first day of '.$datetime);
		$firstWeek = $firstDate->format('w');
		// logger_r("calender::create_calender [firstDate] : ",$firstDate);
		logger("calender::create_calender [firstDate] : ".$firstDate->format('Y-m-d'));
		logger("calender::create_calender [firstWeek] : ".$firstWeek." [week] : ".self::wd[$firstWeek]);

		//月終わり
    	$lastDate  = new ExpansionDateTime('last day of '.$datetime);
		$lastWeek  = $lastDate->format('w');
		// logger_r("calender::create_calender [lastDate] : ",$lastDate);
		logger("calender::create_calender [lastDate] : ".$lastDate->format('Y-m-d'));
		logger("calender::create_calender [lastWeek] : ".$lastWeek." [week] : ".self::wd[$lastWeek]);

		// 次月、前月の始め
		$return_date = new ExpansionDateTime('last day of '.$datetime.' last month');
		$advance_date= new ExpansionDateTime('first day of '.$datetime.' next month');
		$rd = $return_date->format('Y-m-d');
		$ad = $advance_date->format('Y-m-d');
		logger("calender::create_calender [rd] : ".$rd);
		logger("calender::create_calender [ad] : ".$ad);

		// 現在選択している月の最後の日付を繰り返す
		$calender_count 	 = $lastDate->format('d');
		logger("calender::create_calender [calender_count] : ".$calender_count);

		// テーブルヘッダー部分
		$calender_header = $this->calender_header($year, $month, $day, $rd, $ad);
		$weekday_header  = $this->weekday_header();

		$st = '-'. ($firstWeek+1) .' days';
		logger("calender::create_calender [st] : ".$st);
		$firstDate->modify($st);

		// 初めの空白部分
		$firsttable = "";
		for($i=0;$i<$firstWeek;$i++){
			$backday = $firstDate->modify('+1 days');
			logger("calender::create_calender [backday] : ".$backday->format("Y-m-d"));
			logger("calender::create_calender [day] : ".$backday->format("j"));
			$date = $backday->format("Y-m-d");
			$daytext = $backday->format("j");

			$td = <<<HTML
			<td class='day--disabled'>
				<a href="?date={$date}">
					{$daytext}
				</a>
			</td>\n
HTML;
			$firsttable .= $td;
		}

		// カレンダー部分
		$calender = "";
		$today = date("Y-m-d");
		for($j=1;$j<=$calender_count;$j++){
			$date = $year .'-'. $month .'-'. sprintf("%02d",$j);
			$ExpansionDateTime = new ExpansionDateTime($date);
      		$holiday = $ExpansionDateTime->holiday();
			$weekday = $ExpansionDateTime->format('D');
			logger("calender::create_calender [date] : ".$date." [weekday] : ".$weekday." [holiday] : ".$holiday);

			if($j == $day){
				//選択した日付
				$color = " class='selectday'";
			}else{
				// 休日であるかどうか
				if($holiday){
					$color = " bgcolor='#ffffc0' title=".$holiday;
				}else{
					$color = $this->week_color($weekday);
				}
			}

			// 予定がある場合追加
			$daytext = "";
			if(in_array($date,$schedule_array)) $daytext .= '<i class="material-icons md-18">schedule</i>';

			if(isset($movie[$date])) $daytext .= '<i class="material-icons md-15">movie</i>';

			$daytext .= $j;

			$td = $this->nomal_table($color,$date,$daytext);

			if(($j+$i)%7 == 0){
				$td .= "\n</tr>\n<tr>\n";
			}

			$calender .= $td;
		}

		// 最後の空白部分
		$lasttable = "";
		for($k=0;$k<(6-$lastWeek);$k++){
			$nextday = $lastDate->modify('+1 days');
			logger("calender::create_calender [next_date] : ".$nextday->format("Y-m-d"));
			logger("calender::create_calender [day] : ".$nextday->format("j"));
			$date = $nextday->format("Y-m-d");
			$daytext = $nextday->format("j");

			$td = <<<HTML
			<td class='day--disabled'>
				<a href="?date={$date}">
					{$daytext}
				</a>
			</td>\n
HTML;
			$lasttable .= $td;
		}

		$html = <<<HTML
		<table>
			{$calender_header}
			{$weekday_header}
			<tr>
				{$firsttable}
				{$calender}
				{$lasttable}
			</tr>
		</table>\n
HTML;
		return $html;
	}

//====== private ========

	/**
	 * カレンダーのテーブルヘッダー作成
	 * @param $year 年
	 * @param $month 月
	 * @param $day 日
	 * @param $rd 先月の年月日
	 * @param $ad 来月の年月日
	 */
	private function calender_header($year, $month, $day, $rd, $ad){
		$calender_header = <<<HTML
			<tr>
				<th><a href="/schedule" ><i class="material-icons">today</i></a></th>
				<th colspan="5"></th>
				<th></th>
			</tr>
			<tr>
				<th><a href="?date={$rd}" ><i class="material-icons">chevron_left</i></a></th>
				<th colspan="5">{$year}/{$month}/{$day}</th>
				<th><a href="?date={$ad}" ><i class="material-icons">chevron_right</i></a></th>
			</tr>
HTML;
		return $calender_header;
	}

	/**
	 * カレンダーの曜日テーブルヘッダーの作成
	 */
	private function weekday_header(){
		// 曜日
		$weekday = "";
		foreach(self::wd as $value){
			$color = $this->week_color($value);
			$weekday .= "<th{$color}>{$value}</th>\n";
		}

		return "\n<tr>\n{$weekday}</tr>\n";
	}

	/**
	 * カレンダーの曜日によって色を変える
	 * @param $weekday
	 */
	private function week_color($weekday){
		$result = "";
		switch ($weekday) {
			case 'Sun':
			case '日':
				$result = ' bgcolor = "#FFE4E1" ';
			break;

			case 'Mon':
			case '月':
			break;

			case 'Tue':
			case '火':
			break;

			case 'Wed':
			case '水':
			break;

			case 'Thu':
			case '木':
			break;

			case 'Fri':
			case '金':
			break;

			case 'Sat':
			case '土':
				$result = ' bgcolor = "#E0FFFF" ';
			break;
		}
		return $result;
	}

	/**
	 * カレンダーの曜日によって色を変える
	 * @param $color css
	 * @param $date 年月日
	 * @param $day 日
	 */
	private function nomal_table($color,$date,$daytext){
		$td = <<<HTML
		<td{$color}>
			<a href="?date={$date}">
				{$daytext}
			</a>
		</td>\n
HTML;
		return $td;
	}

}

?>
