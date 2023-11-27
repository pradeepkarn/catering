<?php
class Events_ctrl
{
    public $db;
    public $conn;
    function  __construct()
    {
        $this->db = (new DB_ctrl)->db;
        $this->conn = $this->db->conn;
    }
    // Cretae page
    public function create($req = null)
    {
        $context = (object) array(
            'page' => 'events/create.php',
            'data' => (object) array(
                'req' => obj($req),
                'cat_list' => $this->cat_list(limit: 100),
                'employee_list' => $this->user_list('employee'),
                'manager_list' => $this->user_list('manager')
            )
        );
        $this->render_main($context);
    }
    // List page
    public function list($req = null)
    {
        $req = obj($req);

        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_page = $this->event_list(ord: "DESC", limit: 10000, active: 1);
        $tp = count($total_page);
        if ($tp %  $data_limit == 0) {
            $tp = $tp / $data_limit;
        } else {
            $tp = floor($tp / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $event_list = $this->event_search_list($keyword = $req->search, $ord = "DESC", $limit = $page_limit, $active = 1);
        } else {
            $event_list = $this->event_list(ord: "DESC", limit: $page_limit, active: 1);
        }
        $context = (object) array(
            'page' => 'events/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'event_list' => $event_list,
                'total_page' => $tp,
                'current_page' => $cp,
                'is_active' => true
            )
        );
        $this->render_main($context);
    }
    // Trashed post list
    public function trash_list($req = null)
    {
        $req = obj($req);

        $current_page = 0;
        $data_limit = DB_ROW_LIMIT;
        $page_limit = "0,$data_limit";
        $cp = 0;
        if (isset($req->page) && intval($req->page)) {
            $cp = $req->page;
            $current_page = (abs($req->page) - 1) * $data_limit;
            $page_limit = "$current_page,$data_limit";
        }
        $total_page = $this->event_list(ord: "DESC", limit: 10000, active: 0);
        $tp = count($total_page);
        if ($tp %  $data_limit == 0) {
            $tp = $tp / $data_limit;
        } else {
            $tp = floor($tp / $data_limit) + 1;
        }
        if (isset($req->search)) {
            $event_list = $this->event_search_list($keyword = $req->search, $ord = "DESC", $limit = $page_limit, $active = 0);
        } else {
            $event_list = $this->event_list(ord: "DESC", limit: $page_limit, active: 0);
        }
        $context = (object) array(
            'page' => 'events/list.php',
            'data' => (object) array(
                'req' => obj($req),
                'event_list' => $event_list,
                'total_page' => $tp,
                'current_page' => $cp,
                'is_active' => false
            )
        );
        $this->render_main($context);
    }
    // Edit page
    public function edit($req = null)
    {
        $req = obj($req);
        $rvdb = new Dbobjects;
        $rvdb->tableName = "review";
        $arrv = null;
        $arrv['item_id'] = $req->id;
        $arrv['item_group'] = 'event';
        $arrv['status'] = "published";
        $reviewdata = $rvdb->filter($arrv);
        $context = (object) array(
            'page' => 'events/edit.php',
            'data' => (object) array(
                'req' => obj($req),
                'event_detail' => $this->event_detail($req->id),
                'cat_list' => $this->cat_list(limit: 1000),
                'reviewdata' => $reviewdata,
                'employee_list' => $this->user_list('employee'),
                'manager_list' => $this->user_list('manager')
            )
        );
        $this->render_main($context);
    }
    // Save by ajax call
    public function save($req = null)
    {
        $request = null;
        $data = null;
        $data = $_POST;
        $data['banner'] = $_FILES['banner'];

        $rules = [
            'title' => 'required|string',
            'slug' => 'required|string',
            'content' => 'required|string',
            'banner' => 'required|file',
            'event_date' => 'required|date',
            'event_time' => 'required|time',
            // 'price' => 'required|numeric',
            // 'days' => 'required|numeric',
            'city' => 'required|string',
            // 'min_age' => 'required|numeric',
            // 'max_people' => 'required|numeric',
            // 'pickup' => 'required|string',
            'address' => 'required|string',
        ];

        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = obj($data);
        $json_arr = array();
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        if (isset($request->title)) {

            $arr = null;
            $arr['json_obj'] = json_encode($json_arr);
            $arr['content_group'] = "event";
            $arr['title'] = $request->title;
            $arr['slug'] = generate_slug(trim($request->slug));

            $arr['city'] = $request->city;
            $arr['content'] = $request->content;

            $arr['created_at'] = date('Y-m-d H:i:s');
            $arr['created_by'] = USER['id'];

            $arr['event_date'] = $request->event_date;
            $arr['event_time'] = $request->event_time;
            $arr['address'] = $request->address;
            $arr['lat'] = $request->lat ?? null;
            $arr['lon'] = $request->lon ?? null;
            $arr['managers'] = json_encode($request->managers ?? []);
            $arr['employees'] = json_encode($request->employees ?? []);
            $moreimg = [];
            if (isset($_FILES['moreimgs'])) {
                $fl = $_FILES['moreimgs'];
                for ($i = 0; $i < count($fl['name']); $i++) {
                    if ($fl['name'][$i] != '' && $fl['error'][$i] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($fl['name'][$i], PATHINFO_EXTENSION);
                        $imgstr = getUrlSafeString($fl['name'][$i]);
                        $moreimgname = str_replace(" ", "_", $imgstr) . uniqid("_moreimg_") . "." . $ext;
                        $dir = MEDIA_ROOT . "images/pages/" . $moreimgname;
                        $upload = move_uploaded_file($fl['tmp_name'][$i], $dir);
                        if ($upload) {
                            $moreimg[] = $moreimgname;
                        }
                    }
                }
                $arr['imgs'] = json_encode($moreimg);
            }
            $postid = (new Model('content'))->store($arr);
            if (intval($postid)) {
                $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                $imgstr = getUrlSafeString($request->title);
                $imgname = str_replace(" ", "_", $imgstr) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/pages/" . $imgname;
                $upload = move_uploaded_file($request->banner['tmp_name'], $dir);
                if ($upload) {
                    (new Model('content'))->update($postid, array('banner' => $imgname));
                }
                echo js_alert('event created');
                echo go_to(route('eventList'));
            } else {
                echo js_alert('event not created');
                return false;
            }
        }
    }
    public function toggle_trending($req = null)
    {

        $request = json_decode(file_get_contents('php://input'));
        if (isset($request->content_id) && isset($request->action) && ($request->action == 'is_trending' || $request->action == 'is_featured')) {
            $id = $request->content_id;
            $tobj = new Model('content');
            $arr['id'] = $id;
            $arr[$request->action] = 1;
            $trending_post = $tobj->filter_index($arr);
            if (count($trending_post) > 0) {
                $tobj->update($id, [$request->action => 0]);
                $res['msg'] = 'success';
                $res['data'] = "Page removed from $request->action";
            } else {
                $tobj->update($id, [$request->action => 1]);
                $res['msg'] = 'success';
                $res['data'] = "event marked as $request->action";
            }
            echo json_encode($res);
            exit;
        } else {
            $res['msg'] = 'Something went wrong';
            $res['data'] = null;
            echo json_encode($res);
            exit;
        }
    }
    // Save by ajax call
    public function update($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = null;
        $data = null;
        $data = $_POST;
        $data['id'] = $req->id;
        $data['banner'] = $_FILES['banner'];
        $rules = [
            'id' => 'required|integer',
            'title' => 'required|string',
            'slug' => 'required|string',
            'content' => 'required|string',
            'event_date' => 'required|date',
            'event_time' => 'required|time',
            'city' => 'required|string',
            'address' => 'required|string',
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $request = obj($data);
        $json_arr = array();
        if (isset($request->meta_tags)) {
            $json_arr['meta']['tags'] = $request->meta_tags;
        }
        if (isset($request->meta_description)) {
            $json_arr['meta']['description'] = $request->meta_description;
        }
        if (isset($request->title)) {
            $arr = null;
            $arr['json_obj'] = json_encode($json_arr);
            $arr['content_group'] = "event";
            $arr['title'] = $request->title;
            $arr['slug'] = generate_slug(trim($request->slug));

            $arr['city'] = $request->city;
            $arr['content'] = $request->content;

            $arr['created_at'] = date('Y-m-d H:i:s');

            $arr['event_date'] = $request->event_date;
            $arr['event_time'] = $request->event_time;
            $arr['address'] = $request->address;
            $arr['lat'] = $request->lat ?? null;
            $arr['lon'] = $request->lon ?? null;
            $arr['managers'] = json_encode($request->managers ?? []);
            $arr['employees'] = json_encode($request->employees ?? []);
            // $mngrjsn = json_decode($content->managers??'[]',true)??[];
            // $emplsjsn = json_decode($content->employees??'[]',true)??[];
            $imsgjsn = json_decode($content->imgs ?? '[]', true);
            $moreimg = [];
            if (isset($_FILES['moreimgs'])) {
                $fl = $_FILES['moreimgs'];
                for ($i = 0; $i < count($fl['name']); $i++) {
                    if ($fl['name'][$i] != '' && $fl['error'][$i] === UPLOAD_ERR_OK) {
                        $ext = pathinfo($fl['name'][$i], PATHINFO_EXTENSION);
                        $imgstr = getUrlSafeString($fl['name'][$i]);
                        $moreimgname = str_replace(" ", "_", $imgstr) . uniqid("_moreimg_") . "." . $ext;
                        $dir = MEDIA_ROOT . "images/pages/" . $moreimgname;
                        $upload = move_uploaded_file($fl['tmp_name'][$i], $dir);
                        if ($upload) {
                            $moreimg[] = $moreimgname;
                        }
                    }
                }
                $newimgs = array_merge($imsgjsn, $moreimg);
                $arr['imgs'] = json_encode($newimgs);
            }

            if ($request->banner['name'] != "" && $request->banner['error'] == 0) {
                $ext = pathinfo($request->banner['name'], PATHINFO_EXTENSION);
                $imgstr = getUrlSafeString($request->title);
                $imgname = str_replace(" ", "_", $imgstr) . uniqid("_") . "." . $ext;
                $dir = MEDIA_ROOT . "images/pages/" . $imgname;
                $upload = move_uploaded_file($request->banner['tmp_name'], $dir);
                if ($upload) {
                    $arr['banner'] = $imgname;
                    $old = obj($content);
                    if ($old) {
                        if ($old->banner != "") {
                            $olddir = MEDIA_ROOT . "images/pages/" . $old->banner;
                            if (file_exists($olddir)) {
                                unlink($olddir);
                            }
                        }
                    }
                }
            }
            try {
                (new Model('content'))->update($request->id, $arr);
                echo js_alert('event updated');
                echo go_to(route('eventEdit', ['id' => $request->id]));
                exit;
            } catch (PDOException $e) {
                echo js_alert('event not updated, check slug or content data');
                exit;
            }
        }
    }
    function delete_more_img($req = null)
    {
        header('Content-Type: application/json');
        $datavald = $_POST;
        $req = obj($_POST);
        $rules = [
            'content_id' => 'required|integer',
            'img_src' => 'required|string',
        ];
        $pass = validateData(data: $datavald, rules: $rules);
        $data = null;
        if (!$pass) {
            $data['msg'] = msg_ssn(return: true, lnbrk: "<br>");
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
        $db = new Dbobjects;
        $pdo = $db->conn;
        $pdo->beginTransaction();
        $db->tableName = "content";
        $content = $db->pk($req->content_id);
        if ($content) {
            $content = obj($content);
            $imgsjson = modifyJsonArray(jsonString: $content->imgs, valueToDelete: $req->img_src);
            if ($imgsjson !== false) {
                $imgpath = RPATH . "/media/images/pages/" . $req->img_src;
                if ($req->img_src != null && file_exists($imgpath)) {
                    unlink($imgpath);
                }
                try {
                    $db->insertData['imgs'] = $imgsjson;
                    $db->pk($req->content_id);
                    $db->update();
                    $pdo->commit();
                    msg_set("Image deleted");
                    $data['msg'] = msg_ssn(return: true, lnbrk: "\n");
                    $data['success'] = true;
                    $data['data'] = null;
                    echo json_encode($data);
                    exit;
                } catch (PDOException $th) {
                    $pdo->rollback();
                    msg_set("Image not deleted");
                    $data['msg'] = msg_ssn(return: true, lnbrk: "\n");
                    $data['success'] = false;
                    $data['data'] = null;
                    echo json_encode($data);
                    exit;
                }
            }
        } else {
            msg_set("content not found");
            $data['msg'] = msg_ssn(return: true, lnbrk: "\n");
            $data['success'] = false;
            $data['data'] = null;
            echo json_encode($data);
            exit;
        }
    }
    public function move_to_trash($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        try {
            (new Model('content'))->update($req->id, array('is_active' => 0));
            echo js_alert('Content moved to trash');
            echo go_to(route('eventList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Content not moved to trash');
            exit;
        }
    }
    public function restore($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        try {
            (new Model('content'))->update($req->id, array('is_active' => 1));
            echo js_alert('Content moved to active list');
            echo go_to(route('eventTrashList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Content not moved to active list');
            exit;
        }
    }
    public function delete_trash($req = null)
    {
        $req = obj($req);
        $content = obj(getData(table: 'content', id: $req->id));
        if ($content == false) {
            $_SESSION['msg'][] = "Object not found";
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        $data = null;
        $data['id'] = $req->id;
        $rules = [
            'id' => 'required|integer'
        ];
        $pass = validateData(data: $data, rules: $rules);
        if (!$pass) {
            echo js_alert(msg_ssn("msg", true));
            exit;
        }
        try {
            $content_exists = (new Model('content'))->exists(['id' => $req->id, 'is_active' => 0]);
            if ($content_exists) {
                if ((new Model('content'))->destroy($req->id)) {
                    echo js_alert('Content deleted permanatly');
                    echo go_to(route('eventTrashList'));
                    exit;
                }
            }
            echo js_alert('Content does not exixt');
            echo go_to(route('eventTrashList'));
            exit;
        } catch (PDOException $e) {
            echo js_alert('Content not deleted');
            exit;
        }
    }
    // emplyeelist
    function user_list($ug = 'employee')
    {
        return $this->db->show("select id, email, username, first_name, last_name from pk_user where is_active=1 and user_group='$ug';");
    }
    // render function
    public function render_main($context = null)
    {
        import("apps/admin/layouts/admin-main.php", $context);
    }
    // Post list
    public function event_list($ord = "DESC", $limit = 5, $active = 1, $sort_by = 'views')
    {
        $cntobj = new Model('content');
        if (!is_superuser()) {
            return $cntobj->filter_index(array('content_group' => 'event', 'is_active' => $active, 'created_by' => USER['id']), $ord, $limit, $change_order_by_col = $sort_by);
        }
        return $cntobj->filter_index(array('content_group' => 'event', 'is_active' => $active), $ord, $limit, $change_order_by_col = $sort_by);
    }
    public function event_search_list($keyword, $ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('content');
        $search_arr['id'] = $keyword;
        $search_arr['title'] = $keyword;
        // $search_arr['content'] = $keyword;
        $search_arr['author'] = $keyword;
        // $search_arr['created_at'] = $keyword;
        // $search_arr['updated_at'] = $keyword;
        if (!is_superuser()) {
            return $cntobj->search(
                assoc_arr: $search_arr,
                ord: $ord,
                limit: $limit,
                whr_arr: array('content_group' => 'event', 'is_active' => $active, 'created_by' => USER['id'])
            );
        }
        return $cntobj->search(
            assoc_arr: $search_arr,
            ord: $ord,
            limit: $limit,
            whr_arr: array('content_group' => 'event', 'is_active' => $active)
        );
    }
    public function event_detail($id)
    {
        $cntobj = new Model('content');
        return $cntobj->show($id);
    }
    // category list
    public function cat_list($ord = "DESC", $limit = 5, $active = 1)
    {
        $cntobj = new Model('content');
        return $cntobj->filter_index(array('content_group' => 'product_category', 'is_active' => $active), $ord, $limit);
    }
    function event_data($content_id)
    {
        $db = new Dbobjects;

        $sql = "SELECT id, title, banner, managers, employees FROM content WHERE id = $content_id";
        $event = $db->showOne($sql);
        $managers = [];
        $employees = [];

        if ($event) {
            $managerIds = json_decode($event['managers'] ?? '[]', true);
            $employeeIds = json_decode($event['employees'] ?? '[]', true);

            if (is_array($managerIds) && count($managerIds) > 0) {
                $managerIdsString = implode(",", $managerIds);
                $sql = "SELECT first_name, last_name, position, company, country, isd_code, mobile FROM pk_user WHERE id IN ($managerIdsString);";
                $managers = $db->show($sql);
            }

            if (is_array($employeeIds) && count($employeeIds) > 0) {
                $employeeIdsString = implode(",", $employeeIds);
                $sql = "SELECT first_name, last_name, position, company, country, isd_code, mobile FROM pk_user WHERE id IN ($employeeIdsString);";
                $employees = $db->show($sql);
            }

            $event['managers'] = $managers;
            $event['employees'] = $employees;

            return $event;
        } else {
            return null; // Handle case where no content is found for the given ID
        }
    }
    function generate_excel($content_id)
    {
        $data = $this->event_data($content_id);

        $returnarr = [];
        foreach ($data as $key => $ed) {
            $managers = [];
            $employees = [];

            if (isset($ed['managers']) && is_array($ed['managers'])) {
                foreach ($ed['managers'] as $key => $mngr) {
                    $mngr['position'] = getTextFromCode($mngr['position'], POSITIONS);
                    $managers[] = $mngr;
                }
            }

            if (isset($ed['employees']) && is_array($ed['employees'])) {
                foreach ($ed['employees'] as $key => $emps) {
                    $emps['position'] = getTextFromCode($emps['position'], POSITIONS);
                    $employees[] = $emps;
                }
            }

            $ed['managers'] = $managers;
            $ed['employees'] = $employees;
            $returnarr[] = $ed;
        }

        return $returnarr;
    }
}
