<?php
/**
 * Translation file
 * @copyright  Copyright (c) 2014-2016 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */

$lang['requests_index_title'] = '나에게 제출된 휴가 요청';
$lang['requests_index_description'] = '이 화면은 당신에게 제출된 휴가 요청을 나열합니다. 당신이 매니저가 아닌 경우, 이 목록은 항상 비어 있습니다.';
$lang['requests_index_thead_tip_view'] = '조회';
$lang['requests_index_thead_tip_accept'] = '승인';
$lang['requests_index_thead_tip_reject'] = '반려';
$lang['requests_index_thead_tip_history'] = '이력 조회';
$lang['requests_index_thead_id'] = 'ID';
$lang['requests_index_thead_fullname'] = '성명';
$lang['requests_index_thead_startdate'] = '시작일';
$lang['requests_index_thead_enddate'] = '종료일';
$lang['requests_index_thead_duration'] = '기간';
$lang['requests_index_thead_type'] = '유형';
$lang['requests_index_thead_status'] = '상태';

$lang['requests_collaborators_title'] = '내 부하직원 목록';
$lang['requests_collaborators_description'] = '이 화면은 직속 부하직원을 나열합니다. 당신이 매니저가 아닌 경우,이 목록은 항상 비어 있습니다.';
$lang['requests_collaborators_thead_id'] = 'ID';
$lang['requests_collaborators_thead_link_balance'] = '휴가 현황';
$lang['requests_collaborators_thead_link_presence'] = '현재 보고서';
$lang['requests_collaborators_thead_link_year'] = '연간 달력';
$lang['requests_collaborators_thead_link_create_leave'] = '부하직원 대신 휴가 요청 생성';
$lang['requests_collaborators_thead_link_create_overtime'] = '부하직원 대신 초과근무 요청 생성';
$lang['requests_collaborators_thead_firstname'] = '이름';
$lang['requests_collaborators_thead_lastname'] = '성';
$lang['requests_collaborators_thead_email'] = '이메일';
$lang['requests_collaborators_thead_identifier'] = '식별자';

$lang['requests_summary_title'] = '사용자 휴가 현황 #';
$lang['requests_summary_thead_type'] = '휴가 유형';
$lang['requests_summary_thead_available'] = '사용가능한';
$lang['requests_summary_thead_taken'] = '사용한';
$lang['requests_summary_thead_entitled'] = '부여된';
$lang['requests_summary_thead_description'] = '설명';
$lang['requests_summary_flash_msg_error'] = '이 직원은 계약이 없습니다.';
$lang['requests_summary_flash_msg_forbidden'] = '당신은 이 직원의 매니저가 아닙니다.';
$lang['requests_summary_button_list'] = '부하직원 목록';

$lang['requests_index_button_export'] = '이 목록 내보내기';
$lang['requests_index_button_show_all'] = '모든 요청';
$lang['requests_index_button_show_pending'] = '대기중인 요청 ';

$lang['requests_accept_flash_msg_error'] = '이 직원의 결재라인이 아닙니다. 이 휴가 요청을 승인할 수 없습니다.';
$lang['requests_accept_flash_msg_success'] = '휴가 요청이 성공적으로 승인되었습니다.';

$lang['requests_reject_flash_msg_error'] = '이 직원의 결재라인이 아닙니다. 이 휴가 요청을 반려할 수 없습니다.';
$lang['requests_reject_flash_msg_success'] = '휴가 요청이 성공적으로 반려되었습니다.';

$lang['requests_export_title'] = '휴가 요청 목록';
$lang['requests_export_thead_id'] = 'ID';
$lang['requests_export_thead_fullname'] = '성명';
$lang['requests_export_thead_startdate'] = '시작일';
$lang['requests_export_thead_startdate_type'] = '오전/오후';
$lang['requests_export_thead_enddate'] = '종료일';
$lang['requests_export_thead_enddate_type'] = '오전/오후';
$lang['requests_export_thead_duration'] = '기간';
$lang['requests_export_thead_type'] = '유형';
$lang['requests_export_thead_cause'] = '사유';
$lang['requests_export_thead_status'] = '상태';

$lang['requests_delegations_title'] = '위임 목록';
$lang['requests_delegations_description'] = '당신을 대신해서 휴가를 승인하거나 반려할수 있는 직원의 목록입니다.';
$lang['requests_delegations_thead_employee'] = '직원';
$lang['requests_delegations_thead_tip_delete'] = '철회';
$lang['requests_delegations_button_add'] = '등록';
$lang['requests_delegations_popup_delegate_title'] = '위임 등록';
$lang['requests_delegations_popup_delegate_button_ok'] = '확인';
$lang['requests_delegations_popup_delegate_button_cancel'] = '취소';
$lang['requests_delegations_confirm_delete_message'] = '정말 이 위임을 철회하시겠습니까?';
$lang['requests_delegations_confirm_delete_cancel'] = '취소';
$lang['requests_delegations_confirm_delete_yes'] = '예';

$lang['requests_balance_title'] = '부하직원 휴가 현황';
$lang['requests_balance_description'] = '내 부하직원의 휴가 현황. 당신이 매니저가 아닌 경우,이 목록은 항상 비어 있습니다.';
$lang['requests_balance_date_field'] = '보고서의 날짜';
