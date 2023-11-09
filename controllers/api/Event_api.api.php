<?php

class Event_api
{
    public $db;
    function __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }
    function list($req = null)
    {
        $req = obj($req);
        header('Content-Type: application/json');
        $data  = json_decode(file_get_contents("php://input"), true);
        $rules = [
            // 'token' => 'required|string'
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
        // $user = (new Users_api)->get_user_by_token($req->token);

        $events = $this->get_all_events();
        if ($events) {
            msg_set('Events found successfully');
            $api['success'] = true;
            $api['data'] = $events;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
            exit;
        } else {
            msg_set('Event not found or it might be closed');
            $api['success'] = false;
            $api['data'] =  null;
            $api['msg'] = msg_ssn(return: true, lnbrk: ", ");
            echo json_encode($api);
        }
    }
    function get_event_by_id($id)
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'event';
        $arr['id'] = $id;
        $event = $this->db->findOne($arr);
        if ($event) {
            $event = obj($event);
            return (object) array(
                'id' => $event->id,
                'title' => $event->title,
                'managers' => json_decode($event->managers ?? '[]'),
                'employees' => json_decode($event->employees ?? '[]'),
            );
        }
        return null;
    }
    function get_all_events()
    {
        $this->db->tableName = 'content';
        $arr['is_active'] = 1;
        $arr['content_group'] = 'event';
        $events = $this->db->filter($arr);
        if ($events) {
            $evenst = null;
            foreach ($events as $key => $event) {
                $event = obj($event);
                $evenst[] = array(
                    'id' => $event->id,
                    'title' => $event->title,
                    'managers' => json_decode($event->managers ?? '[]'),
                    'employees' => json_decode($event->employees ?? '[]'),
                );
            }
            return  $evenst;
        }
        return null;
    }
}