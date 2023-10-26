<?php

class QR_ctrl
{
    public $db;
    function  __construct()
    {
        $this->db = (new DB_ctrl)->db;
    }

    function generate($req = null)
    {
        $req = obj($req);
        // Do not return anything to the browser
        if (!isset($req->id)) {
            exit;
        }
        $this->db->tableName = "pk_user";
        $user = $this->db->pk($req->id);
        if ($user) {
            $user = obj($user);
            // ob_start("callback");
            // Process the input string
            $codeText = "ID: $user->id \n";
            $codeText .= "NID: $user->nid_no \n";
            $codeText .= "Email: $user->email \n";
            $codeText .= "Name: $user->first_name $user->last_name\n";
            // end of processing
            $output = MEDIA_ROOT."images/qrcodes/".$user->email.".png";
            // $debugLog = ob_get_contents();
            // ob_end_clean();
            // outputs QR code as a PNG data
            QRcode::png(text: $codeText, outfile:$output, size: 5);
            QRcode::png(text: $codeText, size: 5);
        }
        exit;
    }
}
