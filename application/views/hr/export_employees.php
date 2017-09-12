<?php
/**
 * This view builds a Spreadsheet file containing the list of employees (from HR menu).
 * It differs from the Admin menu, because it doesn't export technical information
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('hr_export_employees_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('hr_export_employees_thead_id'));
$sheet->setCellValue('B1', lang('hr_export_employees_thead_firstname'));
$sheet->setCellValue('C1', lang('hr_export_employees_thead_lastname'));
$sheet->setCellValue('D1', lang('hr_employees_thead_khmer_name'));
$sheet->setCellValue('E1', lang('hr_employees_thead_korean_name'));
$sheet->setCellValue('F1', lang('hr_export_employees_thead_email'));
$sheet->setCellValue('G1', lang('hr_export_employees_thead_entity'));
$sheet->setCellValue('H1', lang('hr_export_employees_thead_contract'));
$sheet->setCellValue('I1', lang('hr_export_employees_thead_manager'));
$sheet->setCellValue('J1', lang('hr_employees_thead_dob'));
$sheet->setCellValue('K1', lang('global_gender'));
$sheet->setCellValue('L1', lang('global_education'));
$sheet->setCellValue('M1', lang('global_topik_lvl'));
$sheet->setCellValue('N1', lang('global_phone_number_1'));
$sheet->setCellValue('O1', lang('global_phone_number_2'));
$sheet->setCellValue('P1', lang('hr_employees_thead_identifier'));
$sheet->setCellValue('Q1', lang('hr_employees_thead_datehired'));
$sheet->setCellValue('R1', lang('hr_employees_thead_dateleave'));
$sheet->setCellValue('S1', lang('hr_employees_thead_position'));

$sheet->getStyle('A1:G1')->getFont()->setBold(true);
$sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$employees = $this->users_model->employeesOfEntity($id, $children, $filterActive, $criterion1, $date1, $criterion2, $date2);

$line = 2;
foreach ($employees as $employee) {
    $sheet->setCellValue('A' . $line, $employee->id);
    $sheet->setCellValue('B' . $line, $employee->firstname);
    $sheet->setCellValue('C' . $line, $employee->lastname);
    $sheet->setCellValue('D' . $line, $employee->khmer_name);
    $sheet->setCellValue('E' . $line, $employee->korean_name);
    $sheet->setCellValue('F' . $line, $employee->email);
    $sheet->setCellValue('G' . $line, $employee->entity);
    $sheet->setCellValue('H' . $line, $employee->contract);
    $sheet->setCellValue('I' . $line, $employee->manager_name);
    $sheet->setCellValue('J' . $line, $employee->dob);
    
    $gender =lang("global_gender_" . strtolower($employee->employee_gb));
    
    $sheet->setCellValue('K' . $line, $gender);
    $sheet->setCellValue('L' . $line, $employee->education);
    $sheet->setCellValue('M' . $line, $employee->topik_lvl);
    $sheet->setCellValue('N' . $line, $employee->cp1);
    $sheet->setCellValue('O' . $line, $employee->cp2);
    $sheet->setCellValue('P' . $line, $employee->identifier);
    
    $displayDate = "";
    if(!empty($employee->datehired)){
    	$date = new DateTime($employee->datehired);
    	$displayDate = $date->format(lang('global_date_format'));
    }
    $sheet->setCellValue('Q' . $line, $displayDate);
    
    $displayDateLeave = "";
    if(!empty($employee->dateleave)){
    	$dateLeave = new DateTime($employee->dateleave);
    	$displayDateLeave = $dateLeave->format(lang('global_date_format'));
    }
    $sheet->setCellValue('R' . $line, $displayDateLeave);
    
    $sheet->setCellValue('S' . $line, $employee->position);
    
    
    
    $line++;
}

//Autofit
foreach(range('A', 'G') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

exportSpreadsheet($this, 'employees');
