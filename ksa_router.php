<?php 
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
   require_once('class.PSDB.php');
      
   if (isset($_COOKIE['uid'])) { $uid=$_COOKIE['uid']; } else { $uid=55009; }
     
   class KSA {
    public $X;
    public $json;
    public $arr;
    function __construct() {
        $this->X=new PSDB();
    }    
             
    function getUser($data) {
        $data['uid']=1;
        $sql="select * from ksa_user where id = " . $data['uid'];
        $rs=$this->X->sql($sql);         
        return $rs[0];
    }    

    function getCompany($id) {
        $sql="select * from ksa_company where id = " . $id;
        $rs=$this->X->sql($sql); 
        $output=$rs[0];

        $sql="select authorized_shares, DATE_FORMAT(authorized_shares_dt, '%m/%d/%Y') AS authorized_shares_dt, ";
        $sql .= "dtc_shares, DATE_FORMAT(dtc_shares_dt, '%m/%d/%Y') AS dtc_shares_dt, ";
        $sql .= "float_shares, DATE_FORMAT(float_shares_dt, '%m/%d/%Y') AS authorized_shares_dt, ";
        $sql .= "market_cap, DATE_FORMAT(market_cap_dt, '%m/%d/%Y') AS market_cap_dt, ";
        $sql .= "outstanding_shares, ";
        $sql .= "DATE_FORMAT(outstanding_shares_dt, '%m/%d/%Y') AS outstanding_shares_dt, ";
        $sql .= "DATE_FORMAT(par_value_dt, '%m/%d/%Y') AS par_value_dt, ";
        $sql .= "participant_count, DATE_FORMAT(participant_count_dt, '%m/%d/%Y') AS participant_count_dt, ";
        $sql .= "restricted_shares, DATE_FORMAT(restricted_shares_dt, '%m/%d/%Y') AS restricted_shares_dt, ";
        $sql .= "transfer_agent_shares, DATE_FORMAT(transfer_agent_shares_dt, '%m/%d/%Y') AS transfer_agent_shares_dt, ";
        $sql .= "shareholder_count, DATE_FORMAT(shareholder_count_dt, '%m/%d/%Y') AS shareholder_count_dt, ";
        $sql .= "transfer_agent, website, ";
        $sql .= "unrestricted_shares, ";
        $sql .= "DATE_FORMAT(unrestricted_shares_dt, '%m/%d/%Y') AS unrestricted_shares_dt from ksa_company_data where id = " . $id;
        $rs=$this->X->sql($sql); 
        $output['data']=$rs[0];
        return $output;
    }   

    function getCompanyList($data) {
        $output=array();
        $data['uid']="1";
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $output['organization']="Kinetic Seas";
        $output['role']="AD";
        $output['session']="0a328-b7ac1-893f1-29e44";

        $sql="select * from ksa_company order by company_name";
        $rs=$this->X->sql($sql);         
        $output['list']=$rs;
        return $output;
    }

    function getCompanyShareholderList($data) {
        $output=array();
        $data['uid']="1";
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $output['organization']="Kinetic Seas";
        $output['role']="AD";
        $output['session']="0a328-b7ac1-893f1-29e44";

        $sql="select max(job_id) as jn from ksa_nobo_upload_shares where company_id = " . $current_company;
        $rs=$this->X->sql($sql);         
        $job_id=$rs[0]['jn'];

        $sql="select line_1_name_add, line_2_name_add, line_3_name_add, line_4_name_add, line_5_name_add, DATE_FORMAT(record_date, '%m/%d/%Y') AS last_dt, contact_id,  ";
        $sql.=" FORMAT(shares, 0) AS s, shares from ksa_nobo_upload_shares where job_id = " . $job_id . "  order by shares desc";

        $rs=$this->X->sql($sql);         
        $output['list']=$rs;
        return $output;
    }

    function getCompanyParticipantList($data) {
        $output=array();
        $data['uid']="1";
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $output['organization']="Kinetic Seas";
        $output['role']="AD";
        $output['session']="0a328-b7ac1-893f1-29e44";

        $sql="select client, number_nobos, FORMAT(shares, 0) AS shares from ksa_company_participant where company_id = " . $current_company . " order by shares desc";
        $rs=$this->X->sql($sql);         
        $output['list']=$rs;
        return $output;
    }

    function getHomePage($data) {
        $output=array();
        $data['uid']="1";
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $output['all']=array();
        $output['ta']=array();
        $output['nobo']=array();

        $output['organization']="Kinetic Seas";
        $output['role']="AD";
        $output['session']="0a328-b7ac1-893f1-29e44";
        $output['uid']="1";
        return $output;
   }

   function getShareholderDashboard($data) {
    $output=array();
    $data['uid']="1";
    $output['user']=$this->getUser($data);
    $current_company=$output['user']['current_company'];

    $output['company']=$this->getCompany($current_company);

    $formData=array();
    $formData['first_name']="Edward";
    $formData['last_name']="Honour";
    $formData['cik']="0002016018";

    $output['formData']=$formData;
    $output['organization']="Kinetic Seas";
    $output['role']="AD";
    $output['session']="0a328-b7ac1-893f1-29e44";
    $output['uid']="1";
    return $output;
}

   function swtichCompanies($data) {
    $output=array();
    $data['uid']="1";
    $output['user']=$this->getUser($data);

    $sql="update ksa_user set current_company = " . $data['formData']['id'];
    $this->X->execute($sql);
    $output=array();    
    return $output;
}

    }        
             
    $A=new KSA();
    $output=array();
             
    $data = file_get_contents("php://input");
    $data = json_decode($data, TRUE);
    $data['uid']="1";
    $output=array();

    $A=new KSA();

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

$output=array();
if ($data['q']=='process-ksa-login') {
    $o=$A->getLogin($data);
    die();
   } else {
        $o=array();
        $o['user']=array();
        $o['user']['force_logout']=0;
        $o['user']['force_off']=0;
   }

   switch ($data['q']) {
       case 'divisions':
                 $output=$A->getDivisions($data);
                 break;
       case 'departments':
                 $output=$A->getDepartments($data);
                 break;
        case 'companies':
                $output=$A->getCompanyList($data);
                break;
        case 'participants':
                $output=$A->getCompanyParticipantList($data);
                break;
        case 'shareholders':
                    $output=$A->getCompanyShareholderList($data);
                    break;
        case 'shareholder-dashboard':
                        $output=$A->getShareholderDashboard($data);
                        break;
        case 'switch-companies':
                $output=$A->swtichCompanies($data);
                break;
        default:
                 $output=$A->getHomePage($data);
                break;
        }
            
        //$output['header']=$A->getHeader($data);
        //$output['user']=$A->getUser($data);
        //$output['menu']=$A->getMenu($data);
        $o=array();
        $o=str_replace('null','""',json_encode($output, JSON_HEX_TAG |
                    JSON_HEX_APOS |
                    JSON_HEX_QUOT |
                    JSON_HEX_AMP |
                    JSON_UNESCAPED_UNICODE));
            
            echo $o;
            
?>