<?php 
/**
 * This view allows to user can join member by create new account
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo lang("global_join")?></title>
<link href = "<?php echo base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href = "<?php echo base_url();?>assets/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
<link href = "<?php echo base_url();?>assets/css/jorani-0.5.0.css" rel="stylesheet">
<link href = "<?php echo base_url();?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href = "" rel="stylesheet">

<script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-2.2.0.min.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>assets/bootstrap/js/bootstrap.min.js"></script>
<link rel="icon" type="<?php echo base_url();?>image/x-icon" href="<?php echo base_url();?>favicon.ico" sizes="32x32">

<style type="text/css">
	.form1{
		max-width: 700px !important;
	    margin: auto;
	    background: #eeeeee;
	    padding: 10px;
	}
</style>
</head>
<body>
<!-- wrap -->
<div class="wrap" id="wrap">
<!-- container-fluid -->
<div class="container-fluid">
<!-- row-fluid -->
<div class="row-fluid form1">
<!-- span12 -->
<div class="span12">
<h2><?php echo lang('formapi_update_login');?></h2>

<?php echo validation_errors(); ?>

<?php
$attributes = array('id' => 'target');
echo form_open('join', $attributes); ?>

    <label for="login"><?php echo lang('users_create_field_login');?></label>
    <div class="input-append">
        <input type="text" name="login" id="login" required value = ""/>
        <a id="cmdRefreshLogin" class="btn btn-primary"><i class="icon-refresh icon-white"></i></a>
    </div>
   
</form>
<button id="send" class="btn btn-primary"><i class="icon-ok icon-white"></i>&nbsp;<?php echo lang('formapi_save_update');?></button>

<script type="text/javascript">


</script>

</body>

</html>