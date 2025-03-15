<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
require_once('class.KSDB.php');

if (isset($_COOKIE['uid'])) { $uid=$_COOKIE['uid']; } else { $uid=55009; }

class KMD {
    public $X;
    public $json;
    public $arr;
    function __construct() {
         $this->X=new KSDB();
    }

    function getLogin($data) {
         $output=array();
         $output['error-code']="0";
         $output['full_name']="Edward Honour";
         //$output['email']=$data['data']['email'];
         $output['organization']="Kinetic Seas";
         $output['role']="AD";
         //$output['password']=hash('sha256',$data['data']['password']);
         $output['uid']="1";
         return $output;
    }

    function getHeader($data) {
         $output=array();
         $output['error-code']="0";
         $output['full_name']="Edward Honour";
         $output['organization']="Kinetic Seas";
         $output['role']="AD";
         $output['crumb2']="Add Order";
         $output['crumb3']="";
         return $output;
    }

    function getUser($data) {
         $output=array();
         $output['full_name']="Edward Honour";
         $output['organization']="Kinetic Seas";
         $output['role']="AD";
         $output['session']="0a328-b7ac1-893f1-29e44";
         $output['uid']="1";
         return $output;
    }


    function getMenu($data) {


     //$sql="select role from cmm_user where id = " . $data['uid'];
     //$rs=$this->X->sql($sql);
     //$menu_id = $rs[0]['role'];
     $menu_id = 1;
  $sql="select id, title, menu_type, link, params, active, icon from cmm_menu_item where menu_id = " . $menu_id . " and parent_id = 0 order by item_order, id";
  $rs=$this->X->sql($sql);

  $item=array();
  foreach($rs as $r) {
         if ($r['menu_type']=="menu") {
             $item2=array();
             $sql="select title, menu_type, link, params, active, icon from cmm_menu_item where menu_id = " . $menu_id . " and parent_id = " . $r['id'] . " order by item_order, id";
             $rs2=$this->X->sql($sql);
             foreach($rs2 as $r2) {
                array_push($item2,$r2);
             }
             $r['submenu']=$item2;
         } else {
             $r['submenu']=array();
             $r['menu-type']="item";
         }
         array_push($item,$r);
  }

  return $item;

}

}

$A=new KMD();
$output=array();

$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
if (!isset($data['q'])) $data['q']="/";
$aa=explode("/",$data['q']);
if (isset($aa[1])) {
$data['q']=$aa[1];
if (isset($aa[2])) $data['id']=$aa[2];
if (isset($aa[3])) $data['id2']=$aa[3];
  if (isset($aa[4])) $data['id3']=$aa[4];
}

$output=$A->getMenu($data);
$o=array();
$o=str_replace('null','""',json_encode($output, JSON_HEX_TAG |
 JSON_HEX_APOS |
 JSON_HEX_QUOT |
 JSON_HEX_AMP |
 JSON_UNESCAPED_UNICODE));

echo $o;

?>
