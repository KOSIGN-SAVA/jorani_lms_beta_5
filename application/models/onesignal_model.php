<?php

/**
 * Created by PhpStorm.
 * User: VansaPha
 * Date: 9/5/2017
 * Time: 9:30 AM
 */
if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class OneSignal_model extends CI_Model {

    public function registerDevice($employeeId, $playerId){
        date_default_timezone_set('Asia/Bangkok');
        $data = array(
            "employee" => $employeeId,
            "player_id" => $playerId,
            'create_date' => date('Y-m-d H:i:s')
        );
        $this->db->insert('onesignal_playerid', $data);
        $newId = $this->db->insert_id();
        return $newId;
    }

    public function checkDuplicationDeviceId($deviceId) {
        $isDuplication = false;
        $this->db->where('player_id', $deviceId);
        $numberOfResults = $this->db->get('onesignal_playerid')->num_rows();
        if ($numberOfResults > 0) {
            $isDuplication = true;
        }
        return $isDuplication;
    }

    public function updateDeviceId($id, $deviceId) {
        date_default_timezone_set('Asia/Bangkok');
        $data = array(
            "employee" => $id,
            'create_date' => date('Y-m-d H:i:s')
        );
        $this->db->where('player_id', $deviceId);
        return $this->db->update('onesignal_playerid', $data);
    }

    public function removeDeviceId($id, $deviceId) {
        $this->db->delete('onesignal_playerid', array("employee" => $id, "player_id" => $deviceId));
        return $id;
    }

    public function getChildrenOfEachManager($id) {
        $this->db->select('onesignal_playerid.player_id');
        $this->db->from('users');
        $this->db->join('onesignal_playerid', 'users.id = onesignal_playerid.employee');
        $this->db->where('users.manager', $id);
        $raw = $this->db->get()->result_array();
        return $raw;
    }

    public function pushNotiBackToChild($leaveId) {
        $this->db->select('onesignal_playerid.player_id');
        $this->db->from('leaves');
        $this->db->join('users', 'leaves.employee = users.id');
        $this->db->join('onesignal_playerid', 'users.id = onesignal_playerid.employee');
        $this->db->where('leaves.id', $leaveId);
        return $this->db->get()->result_array();
    }

    public function pushNotiExtraBackToChild($extraId) {
        $this->db->select('onesignal_playerid.player_id');
        $this->db->from('overtime');
        $this->db->join('users', 'overtime.employee = users.id');
        $this->db->join('onesignal_playerid', 'users.id = onesignal_playerid.employee');
        $this->db->where('overtime.id', $extraId);
        return $this->db->get()->result_array();
    }

    public function isManagerCondition($id) {
        $isManager = false;
        $this->db->join('leaves', 'users.id = leaves.employee');
        $this->db->where('users.id = users.manager');
        $this->db->where('leaves.id', $id);
        $num_row = $this->db->get('users')->num_rows();
        if ($num_row > 0) {
            $isManager = true;
        }
        return $isManager;
    }

    public function isManagerConditionExtra($id) {
        $isManager = false;
        $this->db->join('overtime', 'users.id = overtime.employee');
        $this->db->where('users.id = users.manager');
        $this->db->where('overtime.id', $id);
        $num_row = $this->db->get('users')->num_rows();
        if ($num_row > 0) {
            $isManager = true;
        }
        return $isManager;
    }

    function pushNotiFromWeb(){
        $url="https://onesignal.com/api/v1/notifications";
        $datas = array(
            "app_id" => "0ac0b79a-048b-4358-8c7b-e99f7f512cb8",
            "contents" => array("en" => "Hello from CI"),
            "include_player_ids" => ["a006b281-ecca-4138-90c4-0ea2f0282880"]
        );
        $data_string = json_encode($datas);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        $result = curl_exec($ch);
        curl_close($ch);
        // end seeing POST
        $obj = json_decode($result);

        return $obj;
    }

}