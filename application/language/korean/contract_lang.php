<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

$lang['contract_index_title'] = '계약 목록';
$lang['contract_index_thead_id'] = 'ID';
$lang['contract_index_thead_name'] = '명칭';
$lang['contract_index_thead_start'] = '기간 시작';
$lang['contract_index_thead_end'] = '기간 종료';
$lang['contract_index_tip_delete'] = '계약 삭제';
$lang['contract_index_tip_edit'] = '계약 수정';
$lang['contract_index_tip_entitled'] = '부여된 연차일 수';
$lang['contract_index_tip_dayoffs'] = '비작업일과 주말';
$lang['contract_index_tip_exclude_types'] = 'Exclude leave types';
$lang['contract_index_button_export'] = '이 목록 내보내기';
$lang['contract_index_button_create'] = '계약 등록';
$lang['contract_index_popup_delete_title'] = 'Delete Contract';
$lang['contract_index_popup_delete_description'] = '계약을 삭제하려고 합니다. 이 과정은 되돌릴 수 없습니다.';
$lang['contract_index_popup_delete_confirm'] = '계속하시겠습니까?';
$lang['contract_index_popup_delete_button_yes'] = '예';
$lang['contract_index_popup_delete_button_no'] = '아니오';
$lang['contract_index_popup_entitled_title'] = '부여된 연차일 수';
$lang['contract_index_popup_entitled_button_cancel'] = '취소';
$lang['contract_index_popup_entitled_button_close'] = '닫기';

$lang['contract_exclude_title'] = 'Exclude leave types from a contract';
$lang['contract_exclude_description'] = 'You cannot exclude leave types already in use (used at least one time by en employee attached to the contract) and the default leave type (set on the contract or into the configuration file).';
$lang['contract_exclude_title_included'] = 'Included leave types';
$lang['contract_exclude_title_excluded'] = 'Excluded leave types';
$lang['contract_exclude_tip_include_type'] = 'Include this leave type';
$lang['contract_exclude_tip_exclude_type'] = 'Exclude this leave type';
$lang['contract_exclude_tip_already_used'] = 'This leave type is already in use';
$lang['contract_exclude_tip_default_type'] = 'You cannot exclude the default leave type';

$lang['contract_edit_title'] = '계약 수정';
$lang['contract_edit_description'] = '계약 수정 #';
$lang['contract_edit_field_name'] = '명칭';
$lang['contract_edit_field_start_month'] = '월 / 기간 시작';
$lang['contract_edit_field_start_day'] = '일 / 기간 시작';
$lang['contract_edit_field_end_month'] = '월 / 기간 종료';
$lang['contract_edit_field_end_day'] = '일 / 기간 종료';
$lang['contract_edit_default_leave_type'] = '기본 휴가 유형';
$lang['contract_edit_button_update'] = '계약 수정';
$lang['contract_edit_button_cancel'] = '취소';
$lang['contract_edit_msg_success'] = '계약이 성공적으로 수정되었습니다.';

$lang['contract_create_title'] = '새 계약 등록';
$lang['contract_create_field_name'] = '명칭';
$lang['contract_create_field_start_month'] = '월 / 기간 시작';
$lang['contract_create_field_start_day'] = '일 / 기간 시작';
$lang['contract_create_field_end_month'] = '월 / 기간 종료';
$lang['contract_create_field_end_day'] = '일 / 기간 종료';
$lang['contract_create_default_leave_type'] = '기본 휴가 유형';
$lang['contract_create_button_create'] = '계약 등록';
$lang['contract_create_button_cancel'] = '취소';
$lang['contract_create_msg_success'] = '계약이 성공적으로 등록되었습니다.';

$lang['contract_delete_msg_success'] = '계약이 성공적으로 삭제되었습니다.';

$lang['contract_export_title'] = '계약 목록';
$lang['contract_export_thead_id'] = 'ID';
$lang['contract_export_thead_name'] = '명칭';
$lang['contract_export_thead_start'] = '기간 시작';
$lang['contract_export_thead_end'] = '기간 종료';

$lang['contract_calendar_title'] = '비작업일 달력';
$lang['contract_calendar_description'] = '휴일, 주말은 기본적으로 구성되어 있지 않습니다. 개별적으로 편집하기위해 날짜를 클릭하거나 "Series"버튼을 사용하세요.';
$lang['contract_calendar_legend_title'] = '범례:';
$lang['contract_calendar_legend_allday'] = '전일';
$lang['contract_calendar_legend_morning'] = '오전';
$lang['contract_calendar_legend_afternoon'] = '오후';
$lang['contract_calendar_button_back'] = '계약 목록으로 돌아가기';
$lang['contract_calendar_button_series'] = 'Series of non-working days';
$lang['contract_calendar_popup_dayoff_title'] = '비작업일 수정';
$lang['contract_calendar_popup_dayoff_field_title'] = '제목';
$lang['contract_calendar_popup_dayoff_field_type'] = '유형';
$lang['contract_calendar_popup_dayoff_type_working'] = '작업일';
$lang['contract_calendar_popup_dayoff_type_off'] = '전일 비작업';
$lang['contract_calendar_popup_dayoff_type_morning'] = '오전 비작업';
$lang['contract_calendar_popup_dayoff_type_afternoon'] = '오후 비작업';
$lang['contract_calendar_popup_dayoff_button_delete'] = '삭제';
$lang['contract_calendar_popup_dayoff_button_ok'] = '확인';
$lang['contract_calendar_popup_dayoff_button_cancel'] = '취소';
$lang['contract_calendar_button_import'] = 'iCal 가져오기';
$lang['contract_calendar_prompt_import'] = '비작업일 iCal 파일 URL';

$lang['contract_calendar_popup_series_title'] = 'Edit a series of days off';
$lang['contract_calendar_popup_series_field_occurences'] = 'Mark every';
$lang['contract_calendar_popup_series_field_from'] = 'From';
$lang['contract_calendar_popup_series_button_current'] = '지금';
$lang['contract_calendar_popup_series_field_to'] = 'To';
$lang['contract_calendar_popup_series_field_as'] = 'As a';
$lang['contract_calendar_popup_series_field_as_working'] = 'Working day';
$lang['contract_calendar_popup_series_field_as_off'] = '전일 비작업';
$lang['contract_calendar_popup_series_field_as_morning'] = '오전 비작업';
$lang['contract_calendar_popup_series_field_as_afternnon'] = '오후 비작업';
$lang['contract_calendar_popup_series_field_title'] = '제목';
$lang['contract_calendar_popup_series_button_ok'] = '확인';
$lang['contract_calendar_popup_series_button_cancel'] = '취소';

$lang['contract_calendar_button_copy'] = '복사';
$lang['contract_calendar_copy_destination_js_msg'] = '먼저 계약을 선택하세요.';
$lang['contract_calendar_copy_msg_success'] = '데이터가 성공적으로 복사되었습니다.';
