<?php 
   require_once('class.PSDB.php');

   class KSAX {
    public $X;
    public $json;
    public $arr;
    function __construct() {
        $this->X=new PSDB();
    }    

    function match_shareholder_basic($name) {
        $ouptut=array();
        $sql="select id from ksa_shareholder where shareholder_name = ?";
        $rs=$this->X->sql1($sql,$name);
        if (sizeof($rs)==0) {
            $post=array();
            $post['action']="insert";
            $post['table_name']="ksa_shareholder";
            $post['shareholder_name']=$name;
            $id=$this->X->post($post);
            $output['id']=$id;
            $output['new']='Y';
        } else {
            $id=$rs[0]['id'];
            $output['id']=$id;
            $output['new']='N';
        }
        return $output;
    }

    function summarize_positions($record_date, $security_id) {
        $sql="select distinct shareholder_id from ksa_shareholder_position where security_id = " . $security_id . " and record_date = '" . $record_date . "'";
        $rs=$this->X->sql($sql);
        foreach($rs as $r) {
            $sql="select count(*) as c from ksa_shareholder_position where security_id = " . $security_id . " and record_date = '" . $record_date . "' and ";
            $sql.="shareholder_id = " . $
        }
    }

}