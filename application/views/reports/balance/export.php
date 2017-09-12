<?php
/**
 * This view builds a Spreadsheet file of the native report 'balance of leave requests'.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('reports_export_balance_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$result = array();
$summary = array();
$types = $this->types_model->getTypes();        
$users = $this->organization_model->allEmployees($_GET['entity'], $include_children);

foreach ($users as $user) {
    $result[$user->id]['identifier'] = $user->identifier;
    $result[$user->id]['firstname'] = $user->firstname;
    $result[$user->id]['lastname'] = $user->lastname;
    $result[$user->id]['datehired'] = $user->datehired;
    $result[$user->id]['department'] = $user->department;
    $result[$user->id]['position'] = $user->position;
    $result[$user->id]['contract'] = $user->contract;
    //Init type columns
    foreach ($types as $type) {
        //$result[$user->id][$type['name']] = '';
        $result[$user->id][$type['name']. "_entitled"] = '';
        $result[$user->id][$type['name']. "_taken"] = '';
        $result[$user->id][$type['name']. "_balance"] = ''; 
    }

    $summary = $this->leaves_model->getLeaveBalanceForEmployee($user->id, TRUE, $refDate);
    if (count($summary) > 0 ) {
        foreach ($summary as $key => $value) {
            //$result[$user->id][$key] = round($value[1] - $value[0], 3, PHP_ROUND_HALF_DOWN);
            $result[$user->id][$key . "_entitled"] = $value[1];
            $result[$user->id][$key . "_taken"] = $value[0];
            $result[$user->id][$key . "_balance"] = round($value[1] - $value[0], 3, PHP_ROUND_HALF_DOWN);
        }
    }
}

$max = 0;
$line = 2;
$i18n = array("identifier", "firstname", "lastname", "datehired", "department", "position", "contract");
foreach ($result as $row) {
    $index = 1;
    foreach ($row as $key => $value) {
        if ($line == 2) {
            $colidx = $this->excel->column_name($index) . '1';
            $colidx2 = $this->excel->column_name($index) . '2';
            if (in_array($key, $i18n)) {
            	$sheet->mergeCells($colidx.':'.$colidx2);
                //$sheet->setCellValue($colidx, $colidx);
            	$sheet->setCellValue($colidx, lang($key));
                
            } /*  else {
                $sheet->setCellValue($colidx, $key);
            }   */
            $max++;
        }
        
        $newLine = $line + 1;
        $colidx = $this->excel->column_name($index) . $newLine;
        $sheet->setCellValue($colidx, $value);
        $index++;
    }
    
    if($line == 2){
    	$newIndex = 8;
    	foreach ($types as $type) {
    
    		$startIndex = $newIndex;
    		$endIndex = $newIndex + 2;
    		$newIndex = $endIndex;
    		
    		//$colidx = $this->excel->column_name($newIndex) . '1'; 
    
    		$starColIdx = $this->excel->column_name($startIndex) . '1';
    		$endColIdx = $this->excel->column_name($endIndex) . '1';
    		
    		$entitleColIdx = $this->excel->column_name($startIndex) . '2';
    		$takenColIdx = $this->excel->column_name($startIndex +1) . '2';
    		$balanceColIdx = $this->excel->column_name($startIndex + 2) . '2';
    		
    		$sheet->setCellValue($entitleColIdx, "Entitled");
    		$sheet->setCellValue($takenColIdx, "Taken");
    		$sheet->setCellValue($balanceColIdx, "Balance");
    		
    		$sheet->mergeCells($starColIdx.':'.$endColIdx);
    		$sheet->setCellValue($starColIdx, $type['name']);
    		
    		$newIndex++;
    		//$max++;
    
    		//$thead .= '<th colspan="3" style="text-align: center;">' . $type['name'] . '</th>';
    		//$sheet->mergeCells($starColIdx.':'.$endColIdx);
    		//$sheet->setCellValue($starColIdx, $type['name']);
    		// $thead2.= '<th>Entitled</th>' . '<th>Taken</th>' . '<th>Balance</th>';
    	};
    }
    
    $line++;
}

$colidx = $this->excel->column_name($max) . '1';
$sheet->getStyle('A1:' . $colidx)->getFont()->setBold(true);
$sheet->getStyle('A1:' . $colidx)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//Autofit
for ($ii=1; $ii <7; $ii++) {
    $col = $this->excel->column_name($ii);
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

//Autofit
for ($ii=7; $ii <8 + 12; $ii++) {
	$col = $this->excel->column_name($ii);
	$sheet->getColumnDimension($col)->setAutoSize(false);
}


exportSpreadsheet($this, 'leave_balance');
