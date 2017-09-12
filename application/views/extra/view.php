<?php
/**
 * This view allows an employee to modify an overtime requests (may be accessed by HR admin).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<h2><?php echo lang('extra_view_title');?><?php echo $extra['id']; if ($name != "") {?>&nbsp;<span class="muted">(<?php echo $name; ?>)</span><?php } ?></h2>

    <label for="date" required><?php echo lang('extra_view_field_date');?></label>
    <input type="text" name="date"  value="<?php $date = new DateTime($extra['date']); echo $date->format(lang('global_date_format'));?>" readonly />
    
    <label required><?php echo lang('extra_view_field_time');?></label>
    <span class="input-append date" id="stime">
		<input readonly data-format="hh:mm" style="width: 60px;" type="text" value="<?php
			$stime = "";
			if(isset($extra['start_time']) && !empty($extra['start_time'])){
				$stime = $extra['start_time'];
				$stime = substr($stime, 0, 2) . ":" . substr($stime, 2, 4);
			}
			echo $stime; ?>">
		<span class="add-on">
		  <i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-calendar">
		  </i>
		</span>
	</span>
	<strong> ~ </strong>
	<span class="input-append date" id="etime">
		<input readonly data-format="hh:mm" style="width: 60px;" type="text" value="<?php
			$etime = "";
			if(isset($extra['end_time']) && !empty($extra['end_time'])){
				$etime = $extra['end_time'];
				$etime = substr($etime, 0, 2) . ":" . substr($etime, 2, 4);
			}
			echo $etime; ?>"> 
		<span class="add-on">
		  <i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-calendar">
		  </i>
		</span>
	</span>
	&nbsp;&nbsp;
	<span class="date" id= "lb_dh"></span>
	<input  type= "hidden" value = "<?php 
		if(empty($extra['time_cnt'])){
			echo '0.00';
		}else{
			echo $extra['time_cnt'];
		}
		?>" name = "time_cnt" id="time_cnt">

    
    <label for="duration" required><?php echo lang('extra_view_field_duration');?></label>
    <input type="text" name="duration"  value="<?php echo $extra['duration']; ?>" readonly />
    
    <label for="cause"><?php echo lang('extra_view_field_cause');?></label>
    <textarea name="cause" readonly><?php echo $extra['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('extra_view_field_status');?></label>
    <select name="status" readonly>
        <option selected><?php echo lang($extra['status_name']); ?></option>
    </select><br />
    
    <br /><br />
    <?php if (($extra['status'] == 1) || ($is_hr)) { ?>
    <a href="<?php echo base_url();?>extra/edit/<?php echo $extra['id'] ?>" class="btn btn-primary"><i class="icon-pencil icon-white"></i>&nbsp;<?php echo lang('extra_view_button_edit');?></a>
    &nbsp;
    <?php } ?>
    <a href="<?php echo base_url() . $source; ?>" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('extra_view_button_back_list');?></a>
    
    <script>
    var wH = '<?php echo lang("extra_view_label_hours")?>';
    var wM = '<?php echo lang("extra_view_label_minute")?>';
    $(function () {
    	var nm = 0;
    	var h = 0;
    	var m = 0;
    	var nm = Number($("#time_cnt").val());
    	if(nm != 0){
    		nm = Math.round(nm * 60);
    		h = parseInt(nm / 60);
    		m = nm % 60;
    	}
    	$("#lb_dh").text(h + wH +" "+ m + wM);
    });
    
    </script>
