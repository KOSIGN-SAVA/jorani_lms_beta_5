<?php
/**
 * This view builds a Spreadsheet file containing the list of users.
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */

$sheet = $this->excel->setActiveSheetIndex(0);
$sheet->setTitle(mb_strimwidth(lang('users_export_title'), 0, 28, "..."));  //Maximum 31 characters allowed in sheet title.
$sheet->setCellValue('A1', lang('users_export_thead_id'));
$sheet->setCellValue('B1', lang('users_export_thead_firstname'));
$sheet->setCellValue('C1', lang('users_export_thead_lastname'));
$sheet->setCellValue('D1', lang('users_export_thead_login'));
$sheet->setCellValue('E1', lang('users_export_thead_email'));
$sheet->setCellValue('F1', lang('users_export_thead_manager'));

$sheet->getStyle('A1:F1')->getFont()->setBold(true);
$sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$users = $this->users_model->getUsersForExportExcel();
//echo $this->db->last_query();
//return;
$line = 2;
foreach ($users as $user) {
	$style = array(
			'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			)
	);
	$sheet->getStyle("D")->applyFromArray($style);
    $sheet->setCellValue('A' . $line, $user['id']);
    $sheet->setCellValue('B' . $line, $user['firstname']);
    $sheet->setCellValue('C' . $line, $user['lastname']);
    $sheet->setCellValue('D' . $line, $user['login']);
    $sheet->setCellValue('E' . $line, $user['email']);
    $sheet->setCellValue('F' . $line, $user['manager_lastname'] . " " . $user['manager_firstname']);
    $line++;
}

//Autofit
foreach(range('A', 'F') as $colD) {
    $sheet->getColumnDimension($colD)->setAutoSize(TRUE);
}

exportSpreadsheet($this, 'users');
