<?php
/**
 * Created by PhpStorm.
 * User: Vansa
 * Date: 9/11/2017
 * Time: 3:13 PM
 */
Class connection_model extends CI_Model {
    function getData(){
        $url="https://onesignal.com/api/v1/notifications";

        $datas = array(
            "app_id" => "0ac0b79a-048b-4358-8c7b-e99f7f512cb8",
            "contents" => array("en" => "Hello from CI"),
            "include_player_ids" => ["a006b281-ecca-4138-90c4-0ea2f0282880"]
        );

        echo json_encode($datas);
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