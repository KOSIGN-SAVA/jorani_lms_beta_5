<?php
/**
 * This view displays the native report listing the approved leave requests of employees attached to an entity.
 * User can change the month and year of execution (set by default to the previous month).
 * The content of this page is partially loaded by Ajax.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.4.3
 */
?>
        
<h2><?php echo lang('reports_leaves_title');?> &nbsp;<?php echo $help;?></h2>

<div class="row-fluid">
    <div class="span4">
        <label for="cboMonth"><?php echo lang('reports_leaves_month_field');?>
            <select name="cboMonth" id="cboMonth">
                <?php for ($ii=1; $ii<13;$ii++) {
                    if ($ii == date('m')) {
                        echo "<option val='" . $ii ."' selected>" . $ii ."</option>";
                    } else {
                        echo "<option val='" . $ii ."'>" . $ii ."</option>";
                    }
                }?>
                <!--<option val='0'><?php echo lang('All');?></option>//-->
            </select>
        </label>
        <label for="cboYear"><?php echo lang('reports_leaves_year_field');?>
            <select name="cboYear" id="cboYear">
                <?php $len =  date('Y');
                for ($ii=date('Y', strtotime('-6 year')); $ii<= $len; $ii++) {
                    if ($ii == date('Y')) {
                        echo "<option val='" . $ii ."' selected>" . $ii ."</option>";
                    } else {
                        echo "<option val='" . $ii ."'>" . $ii ."</option>";
                    }
                }?>
            </select>
        </label>
        <br />
    </div>
    <div class="span4">	
        <label for="txtEntity"><?php echo lang('reports_leaves_field_entity');?></label>
        <div class="input-append">
        <input type="text" id="txtEntity" name="txtEntity" readonly />
        <button id="cmdSelectEntity" class="btn btn-primary"><?php echo lang('reports_leaves_button_entity');?></button>
        </div>
        <label for="chkIncludeChildren">
                <input type="checkbox" id="chkIncludeChildren" name="chkIncludeChildren" checked /> <?php echo lang('reports_leaves_field_subdepts');?>
        </label>
    </div>
    <div class="span4">
        <div class="pull-right">    
            <label for="chkLeaveDetails">
                    <input type="checkbox" id="chkLeaveDetails" name="chkLeaveDetails" /> <?php echo lang('reports_leaves_field_leave_requests');?>
            </label>
            &nbsp;
            <button class="btn btn-primary" id="cmdLaunchReport"><i class="icon-file icon-white"></i>&nbsp; <?php echo lang('reports_leaves_button_launch');?></button>
            <button class="btn btn-primary" id="cmdExportReport"><i class="fa fa-file-excel-o"></i>&nbsp; <?php echo lang('reports_leaves_button_export');?></button>
        </div>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div id="reportResult"></div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div id="frmSelectEntity" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
         <h3><?php echo lang('reports_leaves_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn"><?php echo lang('reports_leaves_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('reports_leaves_popup_entity_button_cancel');?></a>
    </div>
</div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.pers-brow.js"></script>
<script type="text/javascript">

var entity = -1; //Id of the selected entity
var entityName = ''; //Label of the selected entity
var includeChildren = true;
var leaveDetails = false;
var month = <?php echo date('m');?>;
var year = <?php echo date('Y');?>;

function select_entity() {
    entity = $('#organization').jstree('get_selected')[0];
    entityName = $('#organization').jstree().get_text(entity);
    $('#txtEntity').val(entityName);
    $("#frmSelectEntity").modal('hide');
    $.cookie('rep_entity', entity);
    $.cookie('rep_entityName', entityName);
    $.cookie('rep_includeChildren', includeChildren);
}

$(document).ready(function() {
    //Init datepicker widget
    moment.locale('<?php echo $language_code;?>');
    $("#refdate").val(moment().format('L'));
    $('#refdate').datepicker();    
    
    $("#frmSelectEntity").alert();
    
    $("#cmdSelectEntity").click(function() {
        $("#frmSelectEntity").modal('show');
        $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
    });
    
    $("#cboMonth").click(function() {
        month = $("#cboMonth").val();
    });
    
    $("#cboYear").click(function() {
        year = $("#cboYear").val();
    });
    
    $('#cmdExportReport').click(function() {
        var rtpQuery = '<?php echo base_url();?>reports/leaves/export';
        var tmpUnix = moment($("#refdate").datepicker("getDate")).utc().unix();
        if (entity != -1) {
            rtpQuery += '?entity=' + entity;
        } else {
            rtpQuery += '?entity=0';
        }
        rtpQuery += '&month=' + month;
        rtpQuery += '&year=' + year;
        if ($('#chkIncludeChildren').prop('checked') == true) {
            rtpQuery += '&children=true';
        } else {
            rtpQuery += '&children=false';
        }
        if ($('#chkLeaveDetails').prop('checked') == true) {
            rtpQuery += '&requests=true';
        } else {
            rtpQuery += '&requests=false';
        }
        document.location.href = rtpQuery;
    });
    
    $('#cmdLaunchReport').click(function() {
        var ajaxQuery = '<?php echo base_url();?>reports/leaves/execute';
        var tmpUnix = moment($("#refdate").datepicker("getDate")).utc().unix();
        if (entity != -1) {
            ajaxQuery += '?entity=' + entity;
        } else {
            ajaxQuery += '?entity=0';
        }
        ajaxQuery += '&month=' + month;
        ajaxQuery += '&year=' + year;
        if ($('#chkIncludeChildren').prop('checked') == true) {
            ajaxQuery += '&children=true';
        } else {
            ajaxQuery += '&children=false';
        }
        if ($('#chkLeaveDetails').prop('checked') == true) {
            ajaxQuery += '&requests=true';
        } else {
            ajaxQuery += '&requests=false';
        }
        $('#reportResult').html("<img src='<?php echo base_url();?>assets/images/loading.gif' />");
        
        $.ajax({
          url: ajaxQuery
        })
        .done(function( data ) {
              $('#reportResult').html(data);
        });

    });
    
    //Toggle include sub-entities option
    $('#chkIncludeChildren').on('change', function() {
        includeChildren = $('#chkIncludeChildren').prop('checked');
        $.cookie('rep_includeChildren', includeChildren);
    });
    
    //Toggle include leave requests
    $('#chkLeaveDetails').on('change', function() {
        leaveDetails = $('#chkLeaveDetails').prop('checked');
        $.cookie('rep_leaveDetails', leaveDetails);
    });
    
    //Cookie has value ? take -1 by default
    if($.cookie('rep_entity') != null) {
        entity = $.cookie('rep_entity');
        entityName = $.cookie('rep_entityName');
        includeChildren = $.cookie('rep_includeChildren');
        leaveDetails = (typeof $.cookie('rep_leaveDetails') === 'undefined') ? "false" : $.cookie('rep_leaveDetails');
        //Parse boolean values
        includeChildren = $.parseJSON(includeChildren.toLowerCase());
        leaveDetails = $.parseJSON(leaveDetails.toLowerCase());
        $('#txtEntity').val(entityName);
        $('#chkIncludeChildren').prop('checked', includeChildren);
        $('#chkLeaveDetails').prop('checked', leaveDetails);
    } else { //Set default value
        $.cookie('rep_entity', entity);
        $.cookie('rep_entityName', entityName);
        $.cookie('rep_includeChildren', includeChildren);
        $.cookie('rep_leaveDetails', leaveDetails);
    }
});
</script>