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
        $data  = json_decode(file_get_contents("php://input"), true);
        $rules = [
            'token' => 'required|string',
            'event_id' => 'required|numeric',
        ];
        $req->rescan = $req->rescan ?? 0;
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
            $arr['event_id'] = $req->event_id??null;
            $arr['scan_data'] = json_encode($req->qrdata);
            $this->db->insertData = $arr;
            try {
                if ($already_today) {
                    if ($req->rescan != '1') {
                        msg_set('Please pass rescan permission to scan again');
                        $api['success'] = false;
                        $api['data'] = [
                            'already_scanned'=> 1
                        ];
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
                    $api['success'] = false;
                    $api['data'] = null;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                }
                exit;
            } catch (PDOException $e) {
                msg_set('Not saved');
                $api['success'] = false;
                $api['data'] = null;
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
    function get_scanned_data($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $data  = json_decode(file_get_contents("php://input"), true);
        $rules = [
            'token' => 'required|string',
            'event_id' => 'required|numeric',
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $req = obj($data);
        $user = (new Users_api)->get_user_by_token($req->token);
        if (!isset($req->event_id)) {
            msg_set('Event ID is required');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        $event = $this->get_event_by_id($req->event_id);
        if (!$event) {
            msg_set('Event not found or it might be closed');
            $api['success'] = false;
            $api['data'] = null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        }
        if ($user) {
            try {
                $user = obj($user);
                if (!in_array($user->id, $event->managers)) {
                    msg_set('You are not manager in this event');
                    $api['success'] = false;
                    $api['data'] = null;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                    exit;
                }
                $this->db->tableName = 'qr_scan_data';
                $arr['is_active'] = 1;
                $arr['event_id'] = $event->id;
                $reports = $this->db->filter($arr);
                if ($reports) {
                    $dta = [];
                    foreach ($reports as $key => $rprts) {
                        $rprts['scan_data'] = json_decode($rprts['scan_data']);
                        $dta[] = $rprts;
                    }
                    msg_set('Reports fetched successfully');
                    $api['success'] = true;
                    $api['data'] =  $dta;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                } else {
                    msg_set('No report found');
                    $api['success'] = false;
                    $api['data'] = null;
                    $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
                    echo json_encode($api);
                }
                exit;
            } catch (PDOException $e) {
                msg_set('Not found');
                $api['success'] = false;
                $api['data'] = null;
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
    function get_event_by_id($id) {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'event';
        $arr['id'] = $id;
        $event = $this->db->findOne($arr);
        if ($event) {
            $event = obj($event);
            return (object) array(
                'id'=>$event->id,
                'title'=>$event->title,
                'managers'=>json_decode($event->managers??'[]'),
                'employees'=>json_decode($event->employees??'[]'),
            );
        }
        return null;
    }
}
