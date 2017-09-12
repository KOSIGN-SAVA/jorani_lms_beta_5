
<?php 
/**
 * This view allows to create a new employee
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>


            <script type="text/javascript" src="<?php echo base_url();?>assets/js/jsencrypt.min.js"></script>
            
           
            
            <script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
            <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/selectize.bootstrap2.css" />

        <!-- external libs from cdnjs -->


        
        <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery-ui.min.js"></script>

        <!-- PivotTable.js libs from ../dist -->
        <link rel="stylesheet" type="text/css" href="<?php echo base_url();?>assets/css/pivot.css">

        <script type="text/javascript" src="<?php echo base_url();?>assets/js/pivot.js"></script>

        <script type="text/javascript" src="<?php echo base_url();?>assets/js/jquery.ui.touch-punch.min.js"></script>

        <script type="text/javascript">
    // This example is the most basic usage of pivotUI()

    $(function(){
        $("#output").pivotUI(
            [
                {color: "blue", shape: "circle", test: "aaa"},
                {color: "blue", shape: "circle", test: "aaa"},
                {color: "red", shape: "triangle", test: "bbb"},
            ],
            {
                rows: ["color"],
                rows: ["test"],
                cols: ["shape"],
            }
        );
     });
        </script>

        

        <div id="output" style="margin: 30px;"></div>

