<?php
/**
 * This view allows the creation of a new overtime request.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('extra_create_title');?>&nbsp;<?php echo $help;?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'frmCreateExtra');
echo form_open('extra/create', $attributes) ?>

    <label for="viz_date" required><?php echo lang('extra_create_field_date');?></label>
    <input type="text" name="viz_date" id="viz_date" value="<?php echo set_value('date'); ?>" />
    <input type="hidden" name="date" id="date" />
    
    <label required><?php echo lang('extra_create_field_time');?></label>
    <span class="input-append date" id="stime">
		<input data-format="hh:mm" style="width: 60px;" type="text" name="start_time" id="start_time">
		<span class="add-on">
		  <i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-calendar" id="btn_start_time">
		  </i>
		</span>
	</span>
	<strong> ~ </strong>
	<span class="input-append date" id="etime">
		<input data-format="hh:mm" style="width: 60px;" type="text" name="end_time" id="end_time"> 
		<span class="add-on">
		  <i data-date-icon="icon-calendar" data-time-icon="icon-time" class="icon-calendar" id="btn_end_time">
		  </i>
		</span>
	</span>
	&nbsp;&nbsp;
	<span class="date" id= "lb_dh"></span>
	<input  type= "hidden" value = "0.00" name = "time_cnt" id="time_cnt">
	
    
    <label for="duration" required><?php echo lang('extra_create_field_duration');?></label>
    <input readonly type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" />&nbsp;<span><?php echo lang('extra_create_field_duration_description');?></span>
    
    <label for="cause"><?php echo lang('extra_create_field_cause');?></label>
    <textarea name="cause" id="cause"><?php echo set_value('cause'); ?></textarea>
    
    <label for="status" required><?php echo lang('extra_create_field_status');?></label>
    <select name="status">

        <option value="1" <?php if ($this->config->item('extra_status_requested') == FALSE) echo 'selected'; ?>><?php echo lang('Planned');?></option>
        <option value="2" <?php if ($this->config->item('extra_status_requested') == TRUE) echo 'selected'; ?>><?php echo lang('Requested');?></option>
    </select>
</form>

    <div class="row-fluid"><div class="span12">&nbsp;</div></div>
    <div class="row-fluid"><div class="span12">
        <button id="cmdCreateExtra" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp; <?php echo lang('extra_create_button_create');?></button>
        &nbsp;
        <a href="<?php echo base_url(); ?>extra" class="btn btn-danger"><i class="icon-remove icon-white"></i>&nbsp; <?php echo lang('extra_create_button_cancel');?></a>
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
<script src="<?php echo base_url();?>assets/js/moment-with-locales.min.js"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/jquery-ui-timepicker-0.3.3/jquery.ui.timepicker.js?v=0.3.3"></script>

<?php require_once dirname(BASEPATH) . "/local/triggers/extra_view.php"; ?>

<script type="text/javascript">
	var wH = '<?php echo lang("extra_view_label_hours")?>';
	var wM = '<?php echo lang("extra_view_label_minute")?>';
    function validate_form() {
        var fieldname = "";
        
        //Call custom trigger defined into local/triggers/leave.js
        if (typeof triggerValidateCreateForm == 'function') { 
           if (triggerValidateCreateForm() == false) return false;
        }
        
        if ($('#viz_date').val() == "") fieldname = "<?php echo lang('extra_create_field_date');?>";
        if ($('#duration').val() == "") fieldname = "<?php echo lang('extra_create_field_duration');?>";
        if (Number($('#duration').val()) == 0) fieldname = "<?php echo lang('extra_create_field_duration');?>";
        if ($('#cause').val() == "") fieldname = "<?php echo lang('extra_create_field_cause');?>";
        if (fieldname == "") {
            return true;
        } else {
            bootbox.alert(<?php echo lang('extra_create_mandatory_js_msg');?>);
            return false;
        }
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
        $("#duration").keyup(function() {
            var value = $("#duration").val();
            value = value.replace(",", ".");
            $("#duration").val(value);
        });
        
        $("#cmdCreateExtra").click(function() {
            if (validate_form()) {
                $("#frmCreateExtra").submit();
            }
        });

        //get default date
        /* var nDate = new Date();
        $("#viz_date").datepicker( "setDate" , nDate);
        var day = nDate.getDay();
        if(day > 0 && day <6){
            $('#start_time').timepicker('option',{defaultTime: '18:30'});
            $('#start_time').val("18:30");
            $('#end_time').timepicker('option', {
                minTime: {
                    hour: 18,
                    minute: 30
                }
            });
        } */
    });
    
</script>

<script type="text/javascript">
		

		  function resetDefaultTim(){
			var date = new Date();
			var strTime = date.getHours() + ":" + date.getMinutes();
			$("#start_time").val(strTime);
			$("#end_time").val(strTime);
			calcTime();
		  }

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
				  var dh = diff.asHours().toFixed(2);
				  var duration = (dh * 0.125).toFixed(2);
				  var nm = Math.round(dh * 60);
				  var h = parseInt(nm / 60);
				  var m = nm % 60;
				  $("#lb_dh").text(h + wH +" "+ m + wM);
				  $("#time_cnt").val(dh);
				  $("#duration").val(duration);
	
			  
		  }
		</script>
