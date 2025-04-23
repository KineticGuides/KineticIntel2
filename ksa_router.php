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
             
    function getPageHeader($data) {
       $output=array();
       $company=array();
       $company['company_name']="";
       $company['symbol']="";
       $output['company']=$company;
       return $output;
       
    }
   function getLoginForm($data) {
        $output=array();
        $formData=array();
        $formData['username']="";
        $formData['password']="";
        $output['formData']=$formData;
        return $output;
	}

        function postLogin($data) {
		$sql="select * from ksa_user where username = '" . $data['formData']['username'];
	        $sql.="' and password = '" . $data['formData']['password'] . "'";
        $rs=$this->X->sql($sql);
        if (sizeof($rs)==0) {
            $output=array();
            $output['error']="1";
            $output['message']="Invalid Username/Password Combination";
            $output['user']=array();
        } else {
            $output=array();
            $output['error']="0";
            $output['message']="Success";
            $output['user']=$rs[0];
        }
        return $output;
	}

    function getUser($data) {
     //   $data['uid']=1;
        $sql="select * from ksa_user where id = " . $data['uid'];
        $rs=$this->X->sql($sql);         
	if (sizeof($rs)>0) {
		return $rs[0];
        } else {
	   $output=array();
	   $output['current_company']="";
	   return $output;
	}
    }    

    function getCompany($id) {
	if ($id=="") $id = "0";
        $sql="select * from ksa_company where id = " . $id;
        $rs=$this->X->sql($sql); 
	if (sizeof($rs)>0) {
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
	} else {
	     $output['company_name']="";
	     $output['symbol']="";
	}
        return $output;
    }   

    function getCompanyList($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);
        $sql="select * from ksa_company where id in (select company_id from ksa_user_company where user_id = " . $data['uid'] . ") order by company_name";
        $rs=$this->X->sql($sql);
        $output['list']=$rs;
        return $output;
    }

    function getCompanyShareholderList($data) {
        $output=array();
        //$data['uid']="1";
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $output['organization']="Kinetic Seas";
        $output['role']="AD";
        $output['session']="0a328-b7ac1-893f1-29e44";

	$sql="select max(high_date) as share_date from ksa_shareholder_snapshot where snapshot_type = 'weekly' and ";
	$sql.=" company_id = " . $current_company;
	$rs=$this->X->sql($sql);
	$share_date=$rs[0]['share_date'];
        
	$sql="select shareholder_id, format(ending_shares_nobo,0) as ending_shares_nobo, format(ending_shares_ta,0) as ending_shares_ta ";
        $sql.="	from ksa_shareholder_snapshot where company_id = " . $current_company;
	$sql.=" and snapshot_type = 'weekly' and high_date = '" . $share_date . "' order by ending_shares_nobo desc";
        $rs=$this->X->sql($sql);         
	$list=array();
	foreach($rs as $r) {
	    $sql="select id, shareholder_name, shareholder_name2, mailing_address_1, mailing_address_2 ";
	    $sql.=" from ksa_shareholder where id = " . $r['shareholder_id'];
            $m=$this->X->sql($sql);
	    $l=array();
	    $l['shareholder_id']=$m[0]['id'];
	    $l['shareholder_name']=$m[0]['shareholder_name'];
	    $l['shareholder_name2']=$m[0]['shareholder_name2'];
	    $l['mailing_address_1']=$m[0]['mailing_address_1'];
	    $l['mailing_address_2']=$m[0]['mailing_address_2'];
	    $l['nobo']=$r['ending_shares_nobo'];
	    $l['ta']=$r['ending_shares_ta'];
            $sql="select date_format(first_date,'%m/%d/%y') as first_date from ksa_company_shareholder where shareholder_id = " . $r['shareholder_id'];
	    $sql.=" and company_id = " . $current_company;
            $m=$this->X->sql($sql);
	    $l['first_date']=$m[0]['first_date'];
	    $sql="select date_format(share_date,'%m/%d/%y') as share_date, activity_type, format(net_shares,0) as net_shares ";
	    $sql.="  from ksa_shareholder_activity where company_id = " . $current_company;
	    $sql.= " and shareholder_id = " . $r['shareholder_id'] . " order by share_date desc";
            $m=$this->X->sql($sql);
	    if (sizeof($m)==0) {
                $l['last_date']=$l['first_date'];
		$l['activity_type']="NEW";
		$l['net_shares']=$l['nobo'];
	    } else {
                $l['last_date']=$m[0]['share_date'];
		$l['activity_type']=$m[0]['activity_type'];
		$l['net_shares']=$m[0]['net_shares'];
	    }

            array_push($list,$l);
	}
        $output['list']=$list;
        return $output;
    }

    function getShareholderGraph($data) {
	    $first_date = '2024-11-16';

        $output=array();
	$sql="select * from ksa_daily_shares where share_date >= '" . $first_date . "' and  shareholder_id = " . $data['id'] . " and company_id = " . $data['id2'] . " order by share_date";
        $rs=$this->X->sql($sql);         
	$labels=array();
	$free=array();
	$ta_res=array();
	$ta_free=array();
	$total=array();
	$o=0;
	foreach($rs as $r) {
	//	if ($r['share_date']=="") $r['share_date']="";
	//	if ($r['free_count']=="") $r['free_count']=0;
	//	if ($r['ta_free_count']=="") $r['ta_free_count']=0;
	//	if ($r['ta_restricted_count']=="") $r['ta_restricted_count']=0;
	//	if ($r['ta_total_count']=="") $r['ta_total_count']=0;
	        array_push($free, $r['shares']);
	        array_push($labels, $r['share_date']);
	}
        $output['labels']=$labels;
        $output['total']=$free;
        return $output;
    }

    function getParticipantGraph($data) {
	$first_date = '2024-11-16';

        $output=array();
        $sql="select * from ksa_company_participant where id = " . $data['id'];
        $rs=$this->X->sql($sql);
	$participant_id=$rs[0]['participant_id'];

	$sql="select * from ksa_participant where id = " . $participant_id;
        $rs=$this->X->sql($sql);

	$sql="select * from ksa_dtc_upload where participant_number = " . $rs[0]['participant_number'] . " order by d1";
	$z=$this->X->sql($sql);
	$output['activity']=$z;

        $output['formData']=$rs[0];
	$sql="select * from ksa_daily_participant where share_date >= '" . $first_date;
        $sql.="' and  participant_id = " . $participant_id . " order by share_date";
        $rs=$this->X->sql($sql);         

	$labels=array();
	$free=array();
	$ta_res=array();
	$ta_free=array();
	$total=array();
	$last_total_share_count=0;
	$last_share_count=0;
	$o=0;
	foreach($rs as $r) {
	//	if ($r['share_date']=="") $r['share_date']="";
	//	if ($r['free_count']=="") $r['free_count']=0;
	//	if ($r['ta_free_count']=="") $r['ta_free_count']=0;
	//	if ($r['ta_restricted_count']=="") $r['ta_restricted_count']=0;
	//	if ($r['ta_total_count']=="") $r['ta_total_count']=0;
	   if ($r['total_share_count']>0) $last_total_share_count=$r['total_share_count'];
	   if ($r['share_count']>0) $last_share_count=$r['share_count'];
	   $sql="select 'x' as x from ksa_fridays where friday = '" . $r['share_date'] . "'";
	   $t=$this->X->sql($sql);
	   if (sizeof($t)) {
		   if ($r['total_share_count']!=0) {
			   $tsc=$r['total_share_count'];
		//	   array_push($total, $r['total_share_count']);
	           } else {
			   $tsc=$last_total_share_count;
		//	   array_push($total, $last_total_share_count);
                   }

		   if ($r['share_count']!=0) {
			   $sc=$r['share_count'];
	       //          array_push($free, $r['share_count']);
		   } else {
			   $sc=$last_share_count;
	       //          array_push($free, $last_share_count);
		   }
		   if ($tsc<$sc) $tsc=$sc;
		   array_push($total, $tsc);
		   array_push($free, $sc);
	      array_push($labels, $r['share_date']);
	   }
	}
        $output['labels']=$labels;
        $output['nobo']=$free;
        $output['total']=$total;
        return $output;
    }

    function getCompanyParticipantList($data) {
        $output=array();
        //$data['uid']="1";
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

	$sql="select a.id as id, ";
        $sql.=" a.participant_name as participant_name, ";
	$sql.=" b.first_date as first_date, b.last_date as last_date ";
	$sql.=" from ksa_participant a, ksa_company_participant b where b.participant_id = a.id ";
        $sql.=" and b.company_id = " . $current_company . " order by a.participant_name ";	

	$sql="select id, ";
	$sql.=" participant_name, ";
	$sql.=" DATE_FORMAT(last_date_nobo,'%m/%d/%y') as last_date_nobo, ";
	$sql.=" DATE_FORMAT(last_date_dtc,'%m/%d/%y') as last_date_dtc, ";
	$sql.=" FORMAT(total_share_count,0) as total_share_count_formatted, total_share_count, ";
        $sql.=" FORMAT(nobo_share_count,0) as nobo_share_count_formatted, nobo_share_count, ";
        $sql.=" FORMAT(total_share_count-nobo_share_count,0) as obo_share_count_formatted, ";
        $sql.=" FORMAT(nobo_holder_count,0) as nobo_holder_count, ";
        $sql.=" FORMAT(dtc_share_count,0) as dtc_share_count, ";
        $sql.=" FORMAT(dtc_share_change,0) as dtc_share_change ";
	$sql.=" from ksa_company_participant where company_id = " . $current_company;
        $sql.=" and participant_name <> '0' order by total_share_count desc";
//	echo $sql;
        $rs=$this->X->sql($sql);         
	$list=array();
	foreach($rs as $r) {

	    array_push($list,$r);
	    
	}
        $output['list']=$list;
        return $output;
    }

    function getHomePage($data) {
        $output=array();
        //$data['uid']="1";
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $output['all']=array();
        $output['ta']=array();
        $output['nobo']=array();

        $output['organization']="Kinetic Seas";
        $output['role']="AD";
        $output['session']="0a328-b7ac1-893f1-29e44";
        //$output['uid']="1";
        return $output;
   }

   function getShareholderDashboard($data) {
    $output=array();
    //$data['uid']="1";
    $output['user']=$this->getUser($data);
    $current_company=$output['user']['current_company'];
    $sql="select * from ksa_shareholder where id = " . $data['id'];
    $rs=$this->X->sql($sql);
    $output['formData']=$rs[0];
    $sql="select * from ksa_shareholder_activity where shareholder_id = " . $data['id'] . " order by share_date";
    $rs=$this->X->sql($sql);
    $output['activity']=$rs;

    $output['company']=$this->getCompany($current_company);
    $output['organization']="Kinetic Seas";
    $output['role']="AD";
    $output['session']="0a328-b7ac1-893f1-29e44";
    //$output['uid']="1";
    $sql="select max(high_date) as last_date from ksa_shareholder_snapshot where ";
    $sql.=" company_id = " . $current_company;
    $sql.=" and snapshot_type = 'weekly' ";
    $sql.=" and shareholder_id = " . $data['id'];
    $rs=$this->X->sql($sql);
    $last_date=$rs[0]['last_date'];
    $sql="select * from ksa_shareholder_snapshot where ";
    $sql.=" company_id = " . $current_company;
    $sql.=" and high_date = '" . $last_date . "' ";
    $sql.=" and snapshot_type = 'weekly'"; 
    $sql.=" and shareholder_id = " . $data['id'];
    $rs=$this->X->sql($sql);
    $output['snapshot']=$rs[0];
    
    return $output;
}

   function getParticipantDashboard($data) {
    $output=array();
    //$data['uid']="1";
    $output['user']=$this->getUser($data);
    $current_company=$output['user']['current_company'];
    $output['company']=$this->getCompany($current_company);

    $sql="select * from ksa_company_participant where id = " . $data['id'];
    $r=$this->X->sql($sql);
    $output['participant']=$r[0];

	$sql="select * from ksa_participant where id = " . $r[0]['participant_id'];
        $rs=$this->X->sql($sql);

	$sql="select * from ksa_dtc_upload where participant_number = " . $rs[0]['participant_number'] . " order by d1";
	$z=$this->X->sql($sql);
	$output['activity']=$z;
    $formData=$r[0];
    $output['formData']=$r[0];
    $output['organization']="Kinetic Seas";
    $output['role']="AD";
    $output['session']="0a328-b7ac1-893f1-29e44";
    $current=array();
    $activity=array();
    $notes=array();
    //$output['uid']="1";
    return $output;
}

   function swtichCompanies($data) {
    $output=array();
    //$data['uid']="1";
    $output['user']=$this->getUser($data);

    $sql="update ksa_user set current_company = " . $data['formData']['id'];
    $this->X->execute($sql);
    $output=array();    
    return $output;
    }

   function getStaticPostions($data) {
	$output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];
        $output['company']=$this->getCompany($current_company);
        $formData=array();
        $formData['company_id']=2;
        $formData['security_id']=4;
        $output['formData']=$formData;
        return $output;
    }
     
   function getActivePostions($data) {
	$output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];
        $output['company']=$this->getCompany($current_company);
        $formData=array();
        $formData['company_id']=2;
        $formData['security_id']=4;
        $output['formData']=$formData;
        return $output;
    }

   function getBuyers($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];
        $output['company']=$this->getCompany($current_company);
	$formData=array();
	$formData['company_id']=2;
	$formData['security_id']=4;
	$output['formData']=$formData;
        return $output;
    }
     
   function getBuySellGraph($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];
        $output['company']=$this->getCompany($current_company);

	$current_company=4;
	$sql="select date_format(record_date,'%m/%d/%y') as r_date, buyers+gained as buyers, sellers+lost as sellers ";
        $sql.="	from ksa_nobo_upload_summary where security_id = " . $current_company . " order by record_date";
	$z=$this->X->sql($sql);
	$count=sizeof($z);
	$i=0;

	$dates=array();
	$labels=array();
	$buyers=array();
	$sellers=array();
        foreach($z as $r) {
	   $i++;
	   if ($i>$count-20) {
		   array_push($labels,$r['r_date']); 
		   array_push($buyers,$r['buyers']); 
		   array_push($sellers,$r['sellers']); 
		   $i++;
	   }
	}
           $output['buyers']=$buyers;
           $output['sellers']=$sellers;
           $output['labels']=$labels;
           return $output;
     }
     
   function getStaticGraph($data) {

        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];
        $output['company']=$this->getCompany($current_company);

	$current_company=4;
	$sql="select date_format(record_date,'%m/%d/%y') as r_date, static, number_of_nobos ";
        $sql.="	from ksa_nobo_upload_summary where security_id = " . $current_company . " order by record_date";
	$z=$this->X->sql($sql);
	$count=sizeof($z);
	$i=0;

	$dates=array();
	$labels=array();
	$static=array();
	$shareholders=array();
        foreach($z as $r) {
	   $i++;
	   if ($i>$count-20) {
		   array_push($labels,$r['r_date']); 
		   array_push($static,$r['static']); 
		   array_push($shareholders,$r['number_of_nobos']); 
		   $i++;
	   }
	}
           $output['static']=$static;
           $output['shareholders']=$shareholders;
           $output['labels']=$labels;
           return $output;
     }
     
   function getActiveGraph($data) {

        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];
        $output['company']=$this->getCompany($current_company);

	$current_company=4;
	$sql="select date_format(record_date,'%m/%d/%y') as r_date, number_of_nobos-static as static, number_of_nobos ";
        $sql.="	from ksa_nobo_upload_summary where security_id = " . $current_company . " order by record_date";
	$z=$this->X->sql($sql);
	$count=sizeof($z);
	$i=0;

	$dates=array();
	$labels=array();
	$static=array();
	$shareholders=array();
        foreach($z as $r) {
	   $i++;
	   if ($i>$count-20) {
		   array_push($labels,$r['r_date']); 
		   array_push($static,$r['static']); 
		   array_push($shareholders,$r['number_of_nobos']); 
		   $i++;
	   }
	}
           $output['static']=$static;
           $output['shareholders']=$shareholders;
           $output['labels']=$labels;
           return $output;
     }
     
   function getSellers($data) {
	$output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];
        $output['company']=$this->getCompany($current_company);
        $formData=array();
        $formData['company_id']=2;
        $formData['security_id']=4;
        $output['formData']=$formData;
        return $output;
    }
     
     
   function getNewPositions($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select a.id as id, ";
        $sql.=" a.participant_name as participant_name, ";
        $sql.=" b.first_date as first_date, b.last_date as last_date ";
        $sql.=" from ksa_participant a, ksa_company_participant b where b.participant_id = a.id ";
        $sql.=" and b.company_id = " . $current_company . " order by a.participant_name ";

        $sql="select id, ";
        $sql.=" participant_name, ";
        $sql.=" DATE_FORMAT(last_date_nobo,'%m/%d/%y') as last_date_nobo, ";
        $sql.=" DATE_FORMAT(last_date_dtc,'%m/%d/%y') as last_date_dtc, ";
        $sql.=" FORMAT(total_share_count,0) as total_share_count_formatted, total_share_count, ";
        $sql.=" FORMAT(nobo_share_count,0) as nobo_share_count_formatted, nobo_share_count, ";
        $sql.=" FORMAT(total_share_count-nobo_share_count,0) as obo_share_count_formatted, ";
        $sql.=" FORMAT(nobo_holder_count,0) as nobo_holder_count, ";
        $sql.=" FORMAT(dtc_share_count,0) as dtc_share_count, ";
        $sql.=" FORMAT(dtc_share_change,0) as dtc_share_change ";
        $sql.=" from ksa_company_participant where company_id = " . $current_company;
        $sql.=" and participant_name <> '0' order by total_share_count desc";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getLostPositions($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select a.id as id, ";
        $sql.=" a.participant_name as participant_name, ";
        $sql.=" b.first_date as first_date, b.last_date as last_date ";
        $sql.=" from ksa_participant a, ksa_company_participant b where b.participant_id = a.id ";
        $sql.=" and b.company_id = " . $current_company . " order by a.participant_name ";

        $sql="select id, ";
        $sql.=" participant_name, ";
        $sql.=" DATE_FORMAT(last_date_nobo,'%m/%d/%y') as last_date_nobo, ";
        $sql.=" DATE_FORMAT(last_date_dtc,'%m/%d/%y') as last_date_dtc, ";
        $sql.=" FORMAT(total_share_count,0) as total_share_count_formatted, total_share_count, ";
        $sql.=" FORMAT(nobo_share_count,0) as nobo_share_count_formatted, nobo_share_count, ";
        $sql.=" FORMAT(total_share_count-nobo_share_count,0) as obo_share_count_formatted, ";
        $sql.=" FORMAT(nobo_holder_count,0) as nobo_holder_count, ";
        $sql.=" FORMAT(dtc_share_count,0) as dtc_share_count, ";
        $sql.=" FORMAT(dtc_share_change,0) as dtc_share_change ";
        $sql.=" from ksa_company_participant where company_id = " . $current_company;
        $sql.=" and participant_name <> '0' order by total_share_count desc";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getTransferAgents($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select max(record_date) as d from ksa_ta_upload_shares2";
        $rs=$this->X->sql($sql);
	$rd=$rs[0]['d'];

        $sql="select line_1_name_add, line_2_name_add, line_3_name_add, total_shares, format(total_shares,2) as total_shares_f from ksa_ta_upload_shares2 where record_date = '" . $rd . "' order by total_shares desc";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getActiveParticipants($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select max(d5) as d from ksa_dtc_upload";
        $rs=$this->X->sql($sql);
        $last_date=$rs[0]['d'];

        $sql="select * from ";
        $sql.=" ksa_dtc_upload where d5 = '" . $last_date . "'";
        $sql.=" order by participant_id";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getActiveTA($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select max(d5) as d from ksa_dtc_upload";
        $rs=$this->X->sql($sql);
        $last_date=$rs[0]['d'];

        $sql="select * from ";
        $sql.=" ksa_dtc_upload where d5 = '" . $last_date . "'";
        $sql.=" order by participant_id";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getParticipantIncreases($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select a.id as id, ";
        $sql.=" a.participant_name as participant_name, ";
        $sql.=" b.first_date as first_date, b.last_date as last_date ";
        $sql.=" from ksa_participant a, ksa_company_participant b where b.participant_id = a.id ";
        $sql.=" and b.company_id = " . $current_company . " order by a.participant_name ";

        $sql="select id, ";
        $sql.=" participant_name, ";
        $sql.=" DATE_FORMAT(last_date_nobo,'%m/%d/%y') as last_date_nobo, ";
        $sql.=" DATE_FORMAT(last_date_dtc,'%m/%d/%y') as last_date_dtc, ";
        $sql.=" FORMAT(total_share_count,0) as total_share_count_formatted, total_share_count, ";
        $sql.=" FORMAT(nobo_share_count,0) as nobo_share_count_formatted, nobo_share_count, ";
        $sql.=" FORMAT(total_share_count-nobo_share_count,0) as obo_share_count_formatted, ";
        $sql.=" FORMAT(nobo_holder_count,0) as nobo_holder_count, ";
        $sql.=" FORMAT(dtc_share_count,0) as dtc_share_count, ";
        $sql.=" FORMAT(dtc_share_change,0) as dtc_share_change ";
        $sql.=" from ksa_company_participant where company_id = " . $current_company;
        $sql.=" and participant_name <> '0' order by total_share_count desc";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getParticipantDecreases($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select a.id as id, ";
        $sql.=" a.participant_name as participant_name, ";
        $sql.=" b.first_date as first_date, b.last_date as last_date ";
        $sql.=" from ksa_participant a, ksa_company_participant b where b.participant_id = a.id ";
        $sql.=" and b.company_id = " . $current_company . " order by a.participant_name ";

        $sql="select id, ";
        $sql.=" participant_name, ";
        $sql.=" DATE_FORMAT(last_date_nobo,'%m/%d/%y') as last_date_nobo, ";
        $sql.=" DATE_FORMAT(last_date_dtc,'%m/%d/%y') as last_date_dtc, ";
        $sql.=" FORMAT(total_share_count,0) as total_share_count_formatted, total_share_count, ";
        $sql.=" FORMAT(nobo_share_count,0) as nobo_share_count_formatted, nobo_share_count, ";
        $sql.=" FORMAT(total_share_count-nobo_share_count,0) as obo_share_count_formatted, ";
        $sql.=" FORMAT(nobo_holder_count,0) as nobo_holder_count, ";
        $sql.=" FORMAT(dtc_share_count,0) as dtc_share_count, ";
        $sql.=" FORMAT(dtc_share_change,0) as dtc_share_change ";
        $sql.=" from ksa_company_participant where company_id = " . $current_company;
        $sql.=" and participant_name <> '0' order by total_share_count desc";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getNewParticipants($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select a.id as id, ";
        $sql.=" a.participant_name as participant_name, ";
        $sql.=" b.first_date as first_date, b.last_date as last_date ";
        $sql.=" from ksa_participant a, ksa_company_participant b where b.participant_id = a.id ";
        $sql.=" and b.company_id = " . $current_company . " order by a.participant_name ";

        $sql="select id, ";
        $sql.=" participant_name, ";
        $sql.=" DATE_FORMAT(last_date_nobo,'%m/%d/%y') as last_date_nobo, ";
        $sql.=" DATE_FORMAT(last_date_dtc,'%m/%d/%y') as last_date_dtc, ";
        $sql.=" FORMAT(total_share_count,0) as total_share_count_formatted, total_share_count, ";
        $sql.=" FORMAT(nobo_share_count,0) as nobo_share_count_formatted, nobo_share_count, ";
        $sql.=" FORMAT(total_share_count-nobo_share_count,0) as obo_share_count_formatted, ";
        $sql.=" FORMAT(nobo_holder_count,0) as nobo_holder_count, ";
        $sql.=" FORMAT(dtc_share_count,0) as dtc_share_count, ";
        $sql.=" FORMAT(dtc_share_change,0) as dtc_share_change ";
        $sql.=" from ksa_company_participant where company_id = " . $current_company;
        $sql.=" and participant_name <> '0' order by total_share_count desc";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
   function getLostParticipants($data) {
        $output=array();
        $output['user']=$this->getUser($data);
        $current_company=$output['user']['current_company'];

        $output['company']=$this->getCompany($current_company);

        $sql="select a.id as id, ";
        $sql.=" a.participant_name as participant_name, ";
        $sql.=" b.first_date as first_date, b.last_date as last_date ";
        $sql.=" from ksa_participant a, ksa_company_participant b where b.participant_id = a.id ";
        $sql.=" and b.company_id = " . $current_company . " order by a.participant_name ";

        $sql="select id, ";
        $sql.=" participant_name, ";
        $sql.=" DATE_FORMAT(last_date_nobo,'%m/%d/%y') as last_date_nobo, ";
        $sql.=" DATE_FORMAT(last_date_dtc,'%m/%d/%y') as last_date_dtc, ";
        $sql.=" FORMAT(total_share_count,0) as total_share_count_formatted, total_share_count, ";
        $sql.=" FORMAT(nobo_share_count,0) as nobo_share_count_formatted, nobo_share_count, ";
        $sql.=" FORMAT(total_share_count-nobo_share_count,0) as obo_share_count_formatted, ";
        $sql.=" FORMAT(nobo_holder_count,0) as nobo_holder_count, ";
        $sql.=" FORMAT(dtc_share_count,0) as dtc_share_count, ";
        $sql.=" FORMAT(dtc_share_change,0) as dtc_share_change ";
        $sql.=" from ksa_company_participant where company_id = " . $current_company;
        $sql.=" and participant_name <> '0' order by total_share_count desc";
        $rs=$this->X->sql($sql);
        $list=array();
        foreach($rs as $r) {
            array_push($list,$r);
        }
        $output['list']=$list;
        return $output;
    }
     
    }        
             
    $A=new KSA();
    $output=array();
             
    $data = file_get_contents("php://input");
    $data = json_decode($data, TRUE);
    //$data['uid']="1";
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
       case 'get-shareholder-graph':
                 $output=$A->getShareholderGraph($data);
                 break;
       case 'get-participant-graph':
                 $output=$A->getParticipantGraph($data);
                 break;
       case 'get-buysell-graph':
                 $output=$A->getBuySellGraph($data);
                 break;
       case 'get-static-graph':
                 $output=$A->getStaticGraph($data);
                 break;
       case 'get-active-graph':
                 $output=$A->getActiveGraph($data);
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
        case 'participant-dashboard':
                        $output=$A->getParticipantDashboard($data);
                        break;
        case 'switch-companies':
                $output=$A->swtichCompanies($data);
                break;
        case 'login':
               $output=$A->getLoginForm($data);
               break;
        case 'post-login':
               $output=$A->postLogin($data);
               break;
//	case 'get-page-header':
//		$output=$A->getPageHeader($data);
//		break;
	case 'static-positions':
                $output=$A->getStaticPostions($data);
		break;
	case 'active-positions':
                $output=$A->getActivePostions($data);
		break;
	case 'buyers':
                $output=$A->getBuyers($data);
		break;
	case 'sellers':
                $output=$A->getSellers($data);
		break;
	case 'new-positions':
                $output=$A->getNewPositions($data);
		break;
	case 'lost-positions':
                $output=$A->getLostPositions($data);
		break;
	case 'transfer-agent':
                $output=$A->getTransferAgents($data);
		break;
	case 'active-participants':
                $output=$A->getActiveParticipants($data);
		break;
	case 'participant-increases':
                $output=$A->getParticipantIncreases($data);
		break;
	case 'participant-decreases':
                $output=$A->getParticipantDecreases($data);
		break;
	case 'new-participants':
                $output=$A->getNewParticipants($data);
		break;
	case 'lost-participants':
                $output=$A->getLostParticipants($data);
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