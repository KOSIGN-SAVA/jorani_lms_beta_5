<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

$lang['leaves_summary_title'] = '내 요약';
$lang['leaves_summary_title_overtime'] = '초과 근무의 세부 사항 (휴가를 보상하기 위해 추가)';
$lang['leaves_summary_key_overtime'] = 'Catch up for';
$lang['leaves_summary_thead_type'] = '휴가 유형';
$lang['leaves_summary_thead_available'] = '사용가능한';
$lang['leaves_summary_thead_taken'] = '사용한';
$lang['leaves_summary_thead_entitled'] = '부여된';
$lang['leaves_summary_thead_description'] = '설명';
$lang['leaves_summary_tbody_empty'] = '이 기간에 대한 부여된 휴가일 또는 사용일 수가 없습니다. HR 담당자 또는 매니저에게 문의하세요.';
$lang['leaves_summary_flash_msg_error'] = '당신은 어떤 계약이 없는 것으로 보입니다. HR 담당자 또는 매니저에게 문의하세요.';
$lang['leaves_summary_date_field'] = '보고서 날짜';

$lang['leaves_index_title'] = '내 휴가 목록';
$lang['leaves_index_thead_tip_view'] = '조회';
$lang['leaves_index_thead_tip_edit'] = '수정';
$lang['leaves_index_thead_tip_cancel'] = '취소';
$lang['leaves_index_thead_tip_delete'] = '삭제';
$lang['leaves_index_thead_tip_history'] = '이력 조회';
$lang['leaves_index_thead_id'] = 'ID';
$lang['leaves_index_thead_start_date'] = '시작 일자';
$lang['leaves_index_thead_end_date'] = '종료 일자';
$lang['leaves_index_thead_cause'] = '사유';
$lang['leaves_index_thead_duration'] = '기간';
$lang['leaves_index_thead_type'] = '유형';
$lang['leaves_index_thead_status'] = '상태';
$lang['leaves_index_button_export'] = '이 목록을 내보내기';
$lang['leaves_index_button_create'] = '휴가 요청';
$lang['leaves_index_popup_delete_title'] = '휴가 요청 삭제';
$lang['leaves_index_popup_delete_message'] = '하나의 휴가 요청을 삭제하려고 하고, 이 절차는 돌이킬 수 없습니다.';
$lang['leaves_index_popup_delete_question'] = '계속 하시겠습니까?';
$lang['leaves_index_popup_delete_button_yes'] = '예';
$lang['leaves_index_popup_delete_button_no'] = '아니오';

$lang['leaves_history_thead_changed_date'] = '변경된 날짜';
$lang['leaves_history_thead_change_type'] = '변경 유형';
$lang['leaves_history_thead_changed_by'] = 'Changed By';
$lang['leaves_history_thead_start_date'] = '시작일';
$lang['leaves_history_thead_end_date'] = '종료일';
$lang['leaves_history_thead_cause'] = '사유';
$lang['leaves_history_thead_duration'] = '기간';
$lang['leaves_history_thead_type'] = '유형';
$lang['leaves_history_thead_status'] = '상태';

$lang['leaves_create_title'] = '내 휴가 요청';
$lang['leaves_create_field_start'] = '시작일';
$lang['leaves_create_field_end'] = '종료일';
$lang['leaves_create_field_type'] = '휴가 유형';
$lang['leaves_create_field_duration'] = '기간';
$lang['leaves_create_field_duration_message'] = '부여된 휴가 일을 초과합니다.';
$lang['leaves_create_field_overlapping_message'] = '동일한 기간 내에서 다른 휴가 요청을 요청했습니다.';
$lang['leaves_create_field_cause'] = '사유 (선택)';
$lang['leaves_create_field_status'] = '상태';
$lang['leaves_create_button_create'] = '휴가 요청';
$lang['leaves_create_button_cancel'] = '취소';
$lang['leaves_create_flash_msg_success'] = '휴가 유형이 성공적으로 등록되었습니다.';
$lang['leaves_create_flash_msg_error'] = '휴가 유형이 성공적으로 등록되거나 수정되었습니다, 그러나 당신은 매니저가 없습니다.';

$lang['leaves_flash_spn_list_days_off'] = '기간 내 %s 비작업일';
$lang['leaves_flash_msg_overlap_dayoff'] = '당신의 휴가 요청은 비근무일과 일치합니다.';

$lang['leaves_edit_html_title'] = '휴가 요청 수정';
$lang['leaves_edit_title'] = '휴가 요청 수정 #';
$lang['leaves_edit_field_start'] = '시작일';
$lang['leaves_edit_field_end'] = '종료일';
$lang['leaves_edit_field_type'] = '휴가 유형';
$lang['leaves_edit_field_duration'] = '기간';
$lang['leaves_edit_field_duration_message'] = '부여된 휴가일 수를 초과하고 있습니다.';
$lang['leaves_edit_field_cause'] = '사유 (선택)';
$lang['leaves_edit_field_status'] = '상태';
$lang['leaves_edit_button_update'] = '휴가 수정';
$lang['leaves_edit_button_cancel'] = '취소';
$lang['leaves_edit_flash_msg_error'] = '이미 제출한 휴가 요청은 수정할 수 없습니다';
$lang['leaves_edit_flash_msg_success'] = '휴가 요청이 성공적으로 수정되었습니다.';

$lang['leaves_validate_mandatory_js_msg'] = '"The field " + fieldname + " is mandatory."';
$lang['leaves_validate_flash_msg_no_contract'] = '당신은 어떤 계약이 없는 것으로 보입니다. HR 담당자 또는 매니저에게 문의하세요.';
$lang['leaves_validate_flash_msg_overlap_period'] = '서로 다른 년도의 휴가 요청을 만들 수 없습니다. 두 개의 서로 다른 휴가 요청을 만드십시오.';

$lang['leaves_cancel_flash_msg_error'] = '이 휴가 요청을 취소할 수 없습니다.';
$lang['leaves_cancel_flash_msg_success'] = '휴가 요청이 성공적으로 취소되었습니다.';
$lang['leaves_cancel_unauthorized_msg_error'] = '이미 지난 휴가 요청을 취소할 수 없습니다. 매니저에게 문의하세요.';

$lang['leaves_delete_flash_msg_error'] = '이 휴가 요청을 삭제할 수 없습니다.';
$lang['leaves_delete_flash_msg_success'] = '휴가 요청이 성공적으로 삭제되었습니다.';

$lang['leaves_view_title'] = '휴가 요청 조회 #';
$lang['leaves_view_html_title'] = '휴가 요청 조회';
$lang['leaves_view_field_start'] = '시작일';
$lang['leaves_view_field_end'] = '종료일';
$lang['leaves_view_field_type'] = '휴가 유형';
$lang['leaves_view_field_duration'] = '기간';
$lang['leaves_view_field_cause'] = '사유';
$lang['leaves_view_field_status'] = '상태';
$lang['leaves_view_button_edit'] = '수정';
$lang['leaves_view_button_back_list'] = '목록으로 돌아가기';

$lang['leaves_export_title'] = '휴가 목록';
$lang['leaves_export_thead_id'] = 'ID';
$lang['leaves_export_thead_start_date'] = '시작일';
$lang['leaves_export_thead_start_date_type'] = '오전/오후';
$lang['leaves_export_thead_end_date'] = '종료일';
$lang['leaves_export_thead_end_date_type'] = '오전/오후';
$lang['leaves_export_thead_cause'] = '사유';
$lang['leaves_export_thead_duration'] = '기간';
$lang['leaves_export_thead_type'] = '유형';
$lang['leaves_export_thead_status'] = '상태';
