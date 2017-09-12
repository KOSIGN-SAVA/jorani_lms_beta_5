<?php
/**
 * This view lists the list overtime requests created by an employee (from HR menu).
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<div class="row-fluid">
    <div class="span12">

<h2><?php echo lang('hr_overtime_html_title');?><?php echo $user_id; ?>&nbsp;<span class="muted">(<?php echo $name ?>)</span></h2>

<?php echo $flash_partial_view;?>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="extras" width="100%">
    <thead>
        <tr>
            <th><?php echo lang('hr_overtime_thead_id');?></th>
            <th><?php echo lang('hr_overtime_thead_status');?></th>
            <th><?php echo lang('hr_overtime_thead_date');?></th>
            <th><?php echo lang('hr_overtime_thead_duration');?></th>
            <th><?php echo lang('hr_overtime_thead_cause');?></th>
        </tr>
    </thead>
    <tbody>
<?php foreach ($extras as $extra): 
    $date = new DateTime($extra['date']);
    $tmpDate = $date->getTimestamp();
    $date = $date->format(lang('global_date_format'));?>
    <tr>
        <td data-order="<?php echo $extra['id']; ?>">
            <a href="<?php echo base_url();?>extra/edit/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_overtime_thead_tip_edit');?>"><?php echo $extra['id'] ?></a>
            <div class="pull-right">
                &nbsp;
                <a href="<?php echo base_url();?>overtime/accept/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_overtime_thead_tip_accept');?>"><i class="icon-ok"></i></a>
                &nbsp;
                <a href="<?php echo base_url();?>overtime/reject/<?php echo $extra['id']; ?>?source=hr%2Fovertime%2F<?php echo $user_id; ?>" title="<?php echo lang('hr_overtime_thead_tip_reject');?>"><i class="icon-remove"></i></a>
                &nbsp;
                <a href="#" class="confirm-delete" data-id="<?php echo $extra['id'];?>" title="<?php echo lang('hr_overtime_thead_tip_delete');?>"><i class="icon-trash"></i></a>
                <?php if ($this->config->item('enable_history') == TRUE) { ?>
                &nbsp;
                <a href="#" class="show-history" data-id="<?php echo $extra['id'];?>" title="<?php echo lang('hr_overtime_thead_tip_history');?>"><i class="icon-time"></i></a>
                <?php } ?>
            </div>
        </td>
        <td><?php echo lang($extra['status_name']); ?></td>
        <td data-order="<?php echo $tmpDate; ?>"><?php echo $date; ?></td>
        <?php 
        $strDurationSms = $extra['duration'];
        
        if(strlen($extra["start_time"]) == 4 & strlen($extra["end_time"]) == 4){
        	 
        	$sH = substr($extra["start_time"], 0, 2);
        	$sM = substr($extra["start_time"], 2, 4);
        	$eH = substr($extra["end_time"], 0, 2);
        	$eM = substr($extra["end_time"], 2, 4);
        	 
        	$sMM = intval($sH) * 60 + intval($sM);
        	$eMM = intval($eH) * 60 + intval($eM);
        	 
        	$diffMM = $eMM - $sMM;
        	$diffH = intval($diffMM / 60);
        	$diffM = intval($diffMM % 60);
        	$strDurationSms .= " (" . $sH . ":" . $sM . " ~ " . $eH . ":" . $eM
        	. ", " . $diffH . lang("hr_overtime_label_hours") . " " . $diffM. lang("hr_overtime_label_minute"). ")";
        	 
        }
        ?>
        <td><?php echo $strDurationSms; ?></td>
        <td><?php echo $extra['cause']; ?></td>
    </tr>
<?php endforeach ?>
	</tbody>
</table>
	</div>
</div>

<div class="row-fluid">
    <div class="span12">&nbsp;</div>
</div>

<div class="row-fluid">
    <div class="span12">
      <a href="<?php echo base_url();?>hr/overtime/export/<?php echo $user_id; ?>" class="btn btn-primary"><i class="fa fa-file-excel-o"></i>&nbsp;<?php echo lang('hr_overtime_button_export');?></a>
      &nbsp;&nbsp;
      <a href="<?php echo base_url();?>hr/employees" class="btn btn-primary"><i class="icon-arrow-left icon-white"></i>&nbsp;<?php echo lang('hr_overtime_button_list');?></a>
    </div>
</div>

<div id="frmDeleteExtraRequest" class="modal hide fade">
    <div class="modal-header">
        <a href="#" class="close">&times;</a>
         <h3><?php echo lang('hr_overtime_popup_delete_title');?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo lang('hr_overtime_popup_delete_message');?></p>
        <p><?php echo lang('hr_overtime_popup_delete_question');?></p>
    </div>
    <div class="modal-footer">
        <a href="#" id="lnkDeleteExtra" class="btn btn-danger"><?php echo lang('hr_overtime_popup_delete_button_yes');?></a>
        <a href="#" onclick="$('#frmDeleteExtraRequest').modal('hide');" class="btn"><?php echo lang('hr_overtime_popup_delete_button_no');?></a>
    </div>
</div>

<!-- show history of overtime -->
<div id="frmShowHistory" class="modal hide fade">
    <div class="modal-body" id="frmShowHistoryBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmShowHistory').modal('hide');" class="btn"><?php echo lang('OK');?></a>
    </div>
</div>
<!-- //show history of overtime -->

<link href="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/css/jquery.dataTables.min.css" rel="stylesheet">
<script type="text/javascript" src="<?php echo base_url();?>assets/datatable/DataTables-1.10.11/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(function () {
    //Transform the HTML table in a fancy datatable
    var oTable = $('#extras').dataTable({
        order: [[ 2, "desc" ]],
        language: {
            decimal:            "<?php echo lang('datatable_sInfoThousands');?>",
            processing:       "<?php echo lang('datatable_sProcessing');?>",
            search:              "<?php echo lang('datatable_sSearch');?>",
            lengthMenu:     "<?php echo lang('datatable_sLengthMenu');?>",
            info:                   "<?php echo lang('datatable_sInfo');?>",
            infoEmpty:          "<?php echo lang('datatable_sInfoEmpty');?>",
            infoFiltered:       "<?php echo lang('datatable_sInfoFiltered');?>",
            infoPostFix:        "<?php echo lang('datatable_sInfoPostFix');?>",
            loadingRecords: "<?php echo lang('datatable_sLoadingRecords');?>",
            zeroRecords:    "<?php echo lang('datatable_sZeroRecords');?>",
            emptyTable:     "<?php echo lang('datatable_sEmptyTable');?>",
            paginate: {
                first:          "<?php echo lang('datatable_sFirst');?>",
                previous:   "<?php echo lang('datatable_sPrevious');?>",
                next:           "<?php echo lang('datatable_sNext');?>",
                last:           "<?php echo lang('datatable_sLast');?>"
            },
            aria: {
                sortAscending:  "<?php echo lang('datatable_sSortAscending');?>",
                sortDescending: "<?php echo lang('datatable_sSortDescending');?>"
            }
        }
    });

    //On showing the confirmation pop-up, add the user id at the end of the delete url action
    $('#frmDeleteExtraRequest').on('show', function() {
        var link = "<?php echo base_url();?>extra/delete/" + $(this).data('id');
        link += "?source=hr%2Fovertime%2F<?php echo $user_id; ?>";
        $("#lnkDeleteExtra").attr('href', link);
    })

    //Display a modal pop-up so as to confirm if a leave request has to be deleted or not
    //We build a complex selector because datatable does horrible things on DOM...
    //a simplier selector doesn't work when the delete is on page >1 
    $("#extras tbody").on('click', '.confirm-delete',  function(){
        var id = $(this).data('id');
        $('#frmDeleteExtraRequest').data('id', id).modal('show');
    });
    
    $('#frmDeleteExtraRequest').on('hidden', function() {
        $(this).removeData('modal');
    });

    <?php if ($this->config->item('enable_history') == TRUE) { ?>
    $('#frmShowHistory').on('hidden', function() {
        $("#frmShowHistoryBody").html('<img src="<?php echo base_url();?>assets/images/loading.gif">');
    });
    
    //Popup show history
    $("#extras tbody").on('click', '.show-history',  function(){
        $("#frmShowHistory").modal('show');
        $("#frmShowHistoryBody").load('<?php echo base_url();?>extra/' + $(this).data('id') +'/history');
    });
    <?php } ?>
});
</script>
