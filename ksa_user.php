<?php  
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
require_once('class.PSDB.php');
   
class KSA {
    public $X;
    public $json;
    public $arr;
    function __construct() {
         $this->X=new PSDB();
    }    
         
    function getMenu($data) {
         $sql="select * from ksa_user where id = " . $data['uid'];
         $rs=$this->X->sql($sql);         
         return $rs[0];
    }    
}        
         
$A=new KSA();
$output=array();
         
$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
$data['uid']="1";
//if (!isset($data['q'])) $data['q']="/";
//$aa=explode("/",$data['q']);
//if (isset($aa[1])) {
//     $data['q']=$aa[1];
//     if (isset($aa[2])) $data['id']=$aa[2];
//     if (isset($aa[3])) $data['id2']=$aa[3];
//         if (isset($aa[4])) $data['id3']=$aa[4];
//}        
         
$output=$A->getMenu($data);
$o=array();
$o=str_replace('null','""',json_encode($output, JSON_HEX_TAG |
        JSON_HEX_APOS |
        JSON_HEX_QUOT |
        JSON_HEX_AMP |
        JSON_UNESCAPED_UNICODE));
         
echo $o; 
         
?> 