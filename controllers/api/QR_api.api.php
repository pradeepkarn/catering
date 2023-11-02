<?php

class QR_api
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    function scan_data($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $data  = json_decode(file_get_contents("php://input"),true);
        $rules = [
            'token' => 'required|string',
        ];
        $req->rescan = $req->rescan??0;
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $req = obj($data);
        $req->qrdata = obj($req->qrdata);
        if (!isset($req->qrdata->id) || !isset($req->qrdata->email) || !isset($req->qrdata->nid) || !isset($req->qrdata->name)) {
            msg_set('Invalid qr code');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $user = (new Users_api)->get_user_by_token($req->token);
        if ($user) {
            $saccned_user_id = $req->qrdata->id;
            $user = obj($user);
            $this->db->tableName = 'qr_scan_data';
            $arr['user_id'] = $saccned_user_id;
            $arr['scan_date'] = date('Y-m-d');
            $arr['is_active'] = 1;
            $already_today = $this->db->findOne($arr);
            $arr['scanned_by'] = $user->id;
            $arr['created_at'] = date('Y-m-d H:i:s');
            $arr['scan_time'] = date('H:i:s');
            $arr['scan_data'] = json_encode($req->qrdata);
            $this->db->insertData = $arr;
            try {
                if ($already_today) {
                    if ($req->rescan!='1') {
                        msg_set('Please pass rescan permission to scan again');
                        $api['success'] = false;
                        $api['data'] = null;
                        $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                        echo json_encode($api);
                        exit;
                    }
                }
                $qrid = $this->db->create();
                if ($qrid) {
                    msg_set('Scan success, saved in database');
                    $api['success'] = true;
                    $api['data'] = [];
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                } else {
                    msg_set('Not not saved');
                    $api['success'] = true;
                    $api['data'] = [];
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                }
                exit;
            } catch (PDOException $e) {
                msg_set('Not saved');
                $api['success'] = false;
                $api['data'] = [];
                $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                echo json_encode($api);
                exit;
            }
        } else {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
    }
}
