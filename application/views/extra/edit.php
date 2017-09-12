<?php
/**
 * This view allows the modification of an overtime request.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<h2><?php echo lang('extra_edit_title');?><?php echo $extra['id']; ?>&nbsp;<span class="muted">(<?php echo $name ?>)</span>&nbsp;<?php echo $help;?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'frmEditExtra');
if (isset($_GET['source'])) {
    echo form_open('extra/edit/' . $id . '?source=' . $_GET['source'], $attributes);
} else {
    echo form_open('extra/edit/' . $id, $attributes);
} ?>

    <label for="viz_date"><?php echo lang('extra_edit_field_date');?></label>
    <input type="text" name="viz_date" id="viz_date" value="<?php $date = new DateTime($extra['date']); echo $date->format(lang('global_date_format'));?>" required />
    <input type="hidden" name="date" id="date" value="<?php echo $extra['date']; ?>" />
    
    <label required><?php echo lang('extra_edit_field_time');?></label>
    <span class="input-append date" id="stime">
		<input data-format="hh:mm" style="width: 60px;" type="text" name="start_time" id="start_time" value="<?php
			$stime = "";
			if(isset($extra['start_time']) && !empty($extra['start_time'])){
				$stime = $extra['start_time'];
				$stime = substr($stime, 0, 2) . ":" . substr($stime, 2, 4);
			}
			echo $stime; ?>">
		<span class="add-on">
		  <i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-calendar" id="btn_start_time">
		  </i>
		</span>
	</span>
	<strong> ~ </strong>
	<span class="input-append date" id="etime">
		<input data-format="hh:mm" style="width: 60px;" type="text" name="end_time" id="end_time" value="<?php
			$etime = "";
			if(isset($extra['end_time']) && !empty($extra['end_time'])){
				$etime = $extra['end_time'];
				$etime = substr($etime, 0, 2) . ":" . substr($etime, 2, 4);
			}
			echo $etime; ?>"> 
		<span class="add-on">
		  <i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-calendar" id="btn_end_time">
		  </i>
		</span>
	</span>
	&nbsp;&nbsp;
	<span class="date" id= "lb_dh">0h 0mn</span>
	<input  type= "hidden" value = "<?php 
		if(empty($extra['time_cnt'])){
			echo '0.00';
		}else{
			echo $extra['time_cnt'];
		}
		?>" name = "time_cnt" id="time_cnt">
    
    <label for="duration"><?php echo lang('extra_edit_field_duration');?></label>
    <input readonly type="text" name="duration" id="duration" value="<?php echo $extra['duration']; ?>" required />&nbsp;<span><?php echo lang('extra_edit_field_duration_description');?></span>
    
    <label for="cause"><?php echo lang('extra_edit_field_cause');?></label>
    <textarea name="cause" required><?php echo $extra['cause']; ?></textarea>
    
    <label for="status"><?php echo lang('extra_edit_field_status');?></label>
    <select name="status" required>
        <option value="1" <?php if ($extra['status'] == 1) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if (($extra['status'] == 2) || $this->config->item('extra_status_requested')) echo 'selected'; ?>><?php echo lang('Requested');?></optio
n>
        <?php if ($is_hr) {?>
        <option value="3" <?php if ($extra['status'] == 3) echo 'selected'; ?>><?php echo lang('Accepted');?></option>
        <option value="4" <?php if ($extra['status'] == 4) echo 'selected'; ?>><?php echo lang('Rejected');?></option>        
        <?php } ?>
    </select><br />
</form>
    
    <div class="row-fluid"><div class="span12">&nbsp;</div></div>
    <div class="row-fluid"><div class="span12">
        <button id="cmdEditExtra" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp; <?php echo lang('extra_edit_button_update');?></button>
        &nbsp;
    <?php if (isset($_GET['source'])) {?>
        <a href="<?php echo base_url() . $_GET['source']; ?>" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_cancel');?></a>
    <?php } else {?>
        <a href="<?php echo base_url(); ?>extra" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp;<?php echo lang('extra_edit_button_cancel');?></a>
    <?php } ?>
    </div></div>
    <div class="row-fluid"><div class="span12">&nbsp;</div></div>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<link rel="stylesheet" href="<?php echo base_url();?>assets/jquery-ui-timepicker-0.3.3/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/jquery-ui-timepicker-0.3.3/jquery.ui.timepicker.js?v=0.3.3"></script>

<?php require_once dirname(BASEPATH) . "/local/triggers/extra_view.php"; ?>

<script type="text/javascript">
	var wH = '<?php echo lang("extra_view_label_hours")?>';
	var wM = '<?php echo lang("extra_view_label_minute")?>';
    
    function validate_form() {
        var fieldname = "";
        
        //Call custom trigger defined into local/triggers/leave.js
        if (typeof triggerValidateEditForm == 'function') { 
           if (triggerValidateEditForm() == false) return false;
        }
        
        if ($('#viz_date').val() == "") fieldname = "<?php echo lang('extra_edit_field_date');?>";
        if ($('#duration').val() == "") fieldname = "<?php echo lang('extra_edit_field_duration');?>";
        if (Number($('#duration').val()) == 0) fieldname = "<?php echo lang('extra_create_field_duration');?>";
        if ($('#cause').val() == "") fieldname = "<?php echo lang('extra_edit_field_cause');?>";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('extra_create_mandatory_js_msg');?>);
            return false;
        }
    }

    //calculate time
    function calcTime(){
			 
		  var arrSTime = $("#start_time").val().split(":");
		  var arrETime = $("#end_time").val().split(":");
		  
		  
		  var sDate = new Date();
		  sDate.setHours(arrSTime[0]);
		  sDate.setMinutes(arrSTime[1]);

		  var eDate = new Date();
		  eDate.setHours(arrETime[0]);
		  eDate.setMinutes(arrETime[1]);

		  var diff = moment.duration(moment(eDate).diff(moment(sDate)));
		  var hDiff = parseInt(diff.asHours());
		  
		  var mDiff = diff.asMinutes();// % hDiff;
		  if(mDiff != 0 && hDiff != 0){
			  mDiff = parseInt(mDiff) % hDiff;
		  }
		  $("#duration").val(diff.asHours().toFixed(2));

		  var dh = diff.asHours().toFixed(2);
		  var duration = (dh * 0.125).toFixed(2);
		  var nm = Math.round(dh * 60);
		  var h = parseInt(nm / 60);
		  var m = nm % 60;
		  $("#lb_dh").text(h + wH +" "+ m + wM);
		  $("#time_cnt").val(dh);
		  $("#duration").val(duration); 
		  
			 
			  
	}

 // when start time change, update minimum for end timepicker
    function tpStartSelect( time, endTimePickerInst ) {
    	calcTime();
       $('#end_time').timepicker('option', {
           minTime: {
               hour: endTimePickerInst.hours,
               minute: endTimePickerInst.minutes
           }
       });
    }

    // when end time change, update maximum for start timepicker
    function tpEndSelect( time, startTimePickerInst ) {
    	calcTime();
       $('#start_time').timepicker('option', {
           maxTime: {
               hour: startTimePickerInst.hours,
               minute: startTimePickerInst.minutes
           }
       });
    }
    
    $(function () {
		var nm = 0;
		var h = 0;
		var m = 0;
		var nm = Number($("#time_cnt").val());
		console.log("mn",nm);
		if(nm != 0){
			nm = Math.round(nm * 60);
			h = parseInt(nm / 60);
			m = nm % 60;
		}
		
		$("#lb_dh").text(h + wH +" "+ m + wM);
		
        
        $("#btn_start_time").click(function(){
			$("#start_time").focus();
        });
        $("#btn_end_time").click(function(){
			$("#end_time").focus();
        });

        $('#start_time').blur(function(){
       	 calcTime();
        });

        $('#end_time').blur(function(){
       	 calcTime();
        });
        
    	$('#start_time').timepicker({
    			showLeadingZero: false,
    		    onSelect: tpStartSelect
    	});
    	$('#end_time').timepicker({
    	      showLeadingZero: false,
    	       onSelect: tpEndSelect
    	  });
        $("#viz_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: '<?php echo lang('global_date_js_format');?>',
            altFormat: "yy-mm-dd",
            altField: "#date"
        }, $.datepicker.regional['<?php echo $language_code;?>']);
        
        //Force decimal separator whatever the locale is
        $( "#duration" ).keyup(function() {
            var value = $("#duration").val();
            value = value.replace(",", ".");
            $("#duration").val(value);
        });
        
        $("#cmdEditExtra").click(function() {
            if (validate_form()) {
                $("#frmEditExtra").submit();
            }
        });
    });
</script>


