<?php

class utile{
    function makeRandStr($length) {
      $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
      $r_str = null;
      for ($i = 0; $i < $length; $i++) {
          $r_str .= $str[rand(0, count($str) - 1)];
      }
      return $r_str;
    }

    function monthColor($month){
      $colors = [
        "#c9171e",
        "#c3d825",
        "#f5d1db",
        "#c89933",
        "#6c2463",
        "#47885e",
        "#bbbcde",
        "#b45e67",
        "#223a70",
        "#5654a2",
        "#eb6101",
        "#bbe2f1",
      ];

      return $colors[$month - 1];
    }

    function movie(){
        $items = [];
        if(isset($_SESSION['movie'])){
          $items = $this->movie_schedule($_SESSION['movie']);
          return $items;
        }

        $url = "https://eiga.com/movie/coming.ics";
        $result = $this->curl_get_contents($url);
        $_SESSION['movie'] = $result;

        if (!empty($result)) {
          $items = $this->movie_schedule($result);
        }

        return $items;
    }

    function movie_schedule($data){
      $items = $sort = array();
      $start = false;
      $count = 0;
      foreach(explode("\n", $data) as $row => $line) {
          // 1行目が「BEGIN:VCALENDAR」でなければ終了
          if (0 === $row && false === stristr($line, 'BEGIN:VCALENDAR')) {
              break;
          }
          // 改行などを削除
          $line = trim($line);
          // 「BEGIN:VEVENT」なら日付データの開始
          if (false !== stristr($line, 'BEGIN:VEVENT')) {
              $start = true;
          } elseif ($start) {
              // 「END:VEVENT」なら日付データの終了
              if (false !== stristr($line, 'END:VEVENT')) {
                  $start = false;
                  // 次のデータ用にカウントを追加
                  ++$count;
              } else {
                  // 配列がなければ作成
                  if (empty($items[$count])) {
                      $items[$count] = array('date' => null, 'title' => null);
                  }

                  // 「DTSTART;～」（対象日）の処理
                  if(0 === strpos($line, 'DTSTART:')) {
                      $date = explode(':', $line);
                      $date = end($date);

                      // date encode
                      $y = substr($date,0,4);
                      $m = substr($date,4,2);
                      $d = substr($date,6,2);

                      $items[$count]['date'] = $y.'-'.$m.'-'.$d;
                      // ソート用の配列にセット
                      $sort[$count] =  $y.'-'.$m.'-'.$d;
                  }
                  // 「SUMMARY:～」（名称）の処理
                  elseif(0 === strpos($line, 'SUMMARY:')) {
                      list($title) = explode('/', substr($line, 8));
                      $items[$count]['title'] = trim($title);
                  }
                  // 「UID:～」（URL）の処理
                  elseif(0 === strpos($line, 'UID:')){
                      $array = preg_split('/_/',$line);
                      $url = "https://eiga.com/movie/{$array[1]}";
                      $items[$count]['url'] = trim($url);
                  }

              }
          }
      }

      // 日付でソート
      $items = $this->array_combine_($sort, $items);
      ksort($items);

      return $items;
    }

    function array_combine_($keys, $values){
        $result = array();
        foreach ($keys as $i => $k) {
            $result[$k][] = $values[$i];
        }
        return $result;
    }

    function curl_get_contents($url, $timeout = 60){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

      $result = curl_exec($ch);
      curl_close($ch);

      return $result;

    }
}
