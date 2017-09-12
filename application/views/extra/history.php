<?php 
/**
 * This partial view show the history of changes on a leave request
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.5.0
 */

?>
<?php 
	function timeFormart($time){
		 if(strlen($time) == 4){
        	$sH = substr($time, 0, 2);
        	$sM = substr($time, 2, 4);
        	return $sH . ":" . $sM;
		 }else{
		 	return $time;
		 }
	}

?>
<div class="row-fluid">
    <div class="span12">
<!--<table cellpadding="0" cellspacing="0" border="0" class="display" id="leaveshistory" width="100%">//-->
    <table class="table" id="leaveshistory" width="100%">
    <thead>
        <tr>
            <th class="muted"><?php echo lang('extra_history_thead_change_type');?></th>
            <th class="muted"><?php echo lang('extra_history_thead_changed_date');?></th>
            <th class="muted"><?php echo lang('extra_history_thead_changed_by');?></th>
            <th><?php echo lang('extra_history_thead_request_date');?></th>
            <th><?php echo lang('extra_history_thead_start_time');?></th>
            <th><?php echo lang('extra_history_thead_end_time');?></th>
            <th><?php echo lang('extra_history_thead_time_calc');?></th>
            <th><?php echo lang('extra_history_thead_duration');?></th>
            <th><?php echo lang('extra_history_thead_cause');?></th>
            <th><?php echo lang('extra_history_thead_status');?></th>
        </tr>
    </thead>
    <tbody>
<?php 

$history = array();
$lastObject = new stdClass;
$lastObject->date = '';
$lastObject->start_time = '';
$lastObject->end_time = '';
$lastObject->cause = '';
$lastObject->time_cnt = '';
$lastObject->duration = '';
$lastObject->status = '';

foreach ($events as $event) {
    $objLeave = new stdClass;
    $objLeave->typeIcon = '';
    switch ($event['change_type']) {
        case 0: $objLeave->typeIcon = 'icon-info-sign'; break;
        case 1: $objLeave->typeIcon = 'icon-plus'; break;
        case 2: $objLeave->typeIcon = 'icon-pencil'; break;
        case 3: $objLeave->typeIcon = 'icon-trash'; break;
    }
    
    //Pretty print leave request data
    $date = new DateTime($event['change_date']);
    $objLeave->change_date = $date->format(lang('global_date_format'));
    $objLeave->changed_by = $event['user_name'];
    $date = new DateTime($event['date']);
    $requestDate = $date->format(lang('global_date_format'));
    $objLeave->date = $requestDate;
    $objLeave->start_time = $event['start_time'];
    $objLeave->end_time = $event['end_time'];
    $objLeave->cause = $event['cause'];
    $objLeave->duration = $event['duration'];
    $objLeave->time_cnt = $event['time_cnt'];
    $objLeave->status = $event['status_name'];
    $fullObject = clone $objLeave;
    
    //Display only the cells with changes
    $objLeave->date = ($requestDate==$lastObject->date)?'':$requestDate;
    $objLeave->start_time = ($event['start_time']==$lastObject->start_time)?'':$event['start_time'];
    $objLeave->end_time = ($event['end_time']==$lastObject->end_time)?'':$event['end_time'];
    $objLeave->cause = ($event['cause']==$lastObject->cause)?'':$event['cause'];
    $objLeave->duration = ($event['duration']==$lastObject->duration)?'':$event['duration'];
    $objLeave->time_cnt = ($event['time_cnt']==$lastObject->time_cnt)?'':$event['time_cnt'];
    $objLeave->status = ($event['status_name']==$lastObject->status)?'':$event['status_name'];
    array_push($history, $objLeave);
    $lastObject = clone $fullObject;
}

if (!empty($extra)) {
    $objLeave = new stdClass;
    $objLeave->typeIcon = 'icon-arrow-right';   //Current value
    $objLeave->change_date = '';
    $objLeave->changed_by = '';
    $date = new DateTime($extra['date']);
    $objLeave->date = $date->format(lang('global_date_format'));
    $objLeave->start_time = $extra['end_time'];
    $objLeave->end_time =$extra['end_time'];
    $objLeave->cause = $extra['cause'];
    $objLeave->duration = $extra['duration'];
    $objLeave->time_cnt = $extra['time_cnt'];
    $objLeave->status = $extra['status_name'];
    array_push($history, $objLeave);
}

?>
        
    <?php foreach ($history as $objLeave): ?>
    <tr>
        <td class="muted"><i class="<?php echo $objLeave->typeIcon; ?>"></i></td>
        <td class="muted"><?php echo $objLeave->change_date; ?></td>
        <td class="muted"><?php echo $objLeave->changed_by; ?></td>
        <td><?php echo $objLeave->date; ?></td>
        <td><?php echo timeFormart($objLeave->start_time); ?></td>
        <td><?php echo timeFormart($objLeave->end_time); ?></td>
        <td><?php echo $objLeave->time_cnt; ?></td>
        <td><?php echo $objLeave->duration; ?></td>
        <td><?php echo $objLeave->cause; ?></td>
        <td><?php echo $objLeave->status; ?></td>
    </tr>
    <?php endforeach ?>
    </tbody>
</table>
    </div>
</div>
