<?php include('controller.php')?>

<?php include('head.php'); ?>

<form method="POST" action="index.php?date=<?=$date?>">
  <div class="outer">
	  <div class="head"><h2 style="background-color: <?=$color?>">カレンダー : <?=$gengo?></h2></div>
	  <div class="main">
      <div class="main_cont">
        <div class="box_title">スケジュール</div>
           <div class="scroll_block">
             <ol class="defaultlist list">
				        <?php
                    if($holiday) $template->schedule_holiday($holiday);
                    $i=1;
        				    foreach($schedule as $s){
                      $template->schedule_plan($s,$i);
                      $i++;
        				    }

                    if(isset($movie[$date])) $template->movie_day($movie[$date]);
				        ?>
             </ol>
            </div>
			    </div>
		    <div>
        <?= $calender ?>
        <h4>スケジュール入力</h4>
		    <input type="text" name="plan" size="40" style="font-size:20px">
        <input type="text" class="timepicker" value="00:00" name="time" data-time-format="H:i"/>
        <input type="hidden" name="hidden" value="<?=$hidden?>" >
				<input class="submit" type="submit" value="登録">
	    </div>
		</div>
	</div>
</form>

<script src="js/index.js?date=<?=$d?>"></script>

</body>
</html>
