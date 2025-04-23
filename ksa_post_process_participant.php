<?php      
   //        
   // This process runs after nobo upload to update database/
   //        
   ini_set('display_errors',1);
   ini_set('display_startup_errors',1);
   header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
   header('Content-type: application/json');
   require 'vendor/autoload.php';
   require 'class.PSDB.php';
   require 'class.KSA.php';
              
   $X=new PSDB();
   $K=new KSAX();

   $report_date = $argv[1];
   $security_id = $argv[2];
   $company_id = 2;
             
   // Get Upload Summary Record
             
   function getSharePrice($security_id, $low_date, $high_date) {
        $X=new PSDB();
        $open=-1;
        $close=-1;
        $low=-1;
        $high=-1;
        $sql = "select * from ksa_market_price where security_id = " . $security_id . " and share_date ";
        $sql.= " between '". $low_date . "' and '" . $high_date . "' order by share_date";
        $rs=$X->sql($sql);
        foreach($rs as $r) {
          if ($low==-1) $low=$r['low_price'];
          if ($high=-1) $low=$r['high_price'];
          if ($open==-1) $open=$r['open_price'];
          $close=$r['close_price'];
          if ($r['low_price']<$low) $low=$r['low_price'];
          if ($r['high_price']>$high) $high=$r['high_price'];
        }       
        $output=array();
        $output['low_price']=$low;
        $output['open_price']=$open;
        $output['close_price']=$close;
        $output['high_price']=$high;
        return $output;
   }         
             
   $sql="select company_id from ksa_company_security where id = " . $security_id;
   $rs=$X->sql($sql);
   $company_id = $rs[0]['company_id'];
           
   $sql="select * from ksa_nobo_upload_summary where record_date = '" . $report_date . "' and security_id = " . $security_id;
   $rs=$X->sql($sql);
   if (sizeof($rs)==0) { die("Invalid Record Date"); }

   $new="N";
   $summary=$rs[0];

   // Get Previous Upload Summary Record

   $sql="select * from ksa_nobo_upload_summary where record_date < '" . $report_date . "' and security_id = " . $security_id;
   $sql .= " order by record_date desc";
   $rs=$X->sql($sql);
   if (sizeof($rs)==0) {
       $new='Y';
       $last_summary=array();
       $last_date="";
   } else {
      $last_summary=$rs[0];
      $last_date=$rs[0]['record_date'];
   }

   // Loop through all of the records in the current upload. 

   $sql="select * from ksa_nobo_upload_client where record_date = '" . $report_date;
   $sql .= "' and security_id = " . $security_id;
   $rs=$X->sql($sql);

   foreach($rs as $record) {
         $new='N';
         // Match Participant.
         $participant_id=$record['participant_id'];
         $sql="select * from ksa_participant where id = " . $record['participant_id'];
         $rr=$X->sql($sql);
         $sql="select id from ksa_daily_participant where security_id = " . $security_id . " and ";
         $sql.= " share_date = '" . $report_date . "' and participant_id = " . $participant_id;
         $tt=$X->sql($sql);
         $post=array();
         $post['table_name']="ksa_daily_participant";
         if (sizeof($tt)>0) $post['id']=$tt[0]['id'];
         $post['participant_id']=$participant_id;
         $post['company_id']=$company_id;
         $post['security_id']=$security_id;
         $post['share_date']=$report_date;
         $post['nobo_count']=$record['number_nobos'];
         $post['share_count']=$record['shares'];
         $current_shares=$post['share_count'];
         $current_nobos=$post['nobo_count'];

         $post['security_id']=$security_id;
         $post['company_id']=$company_id;
         print_r($post);
         $current_id=$X->post($post);

         $sql="select count(*) as c from ksa_daily_participant where share_date < '";
         $sql.=  $report_date . "' and participant_id = " . $participant_id . " and security_id = " . $security_id . " and share_count > 0";
         $yyy=$X->sql($sql);
         $process_date = $report_date;
         if ($yyy[0]['c']>0) {
                  //-- Find Last Record.
                  $sql="select max(share_date) as rd from ksa_daily_participant where  ";
                  $sql.=" share_date < '" . $report_date . "' and participant_id = " . $participant_id . " and security_id = " . $security_id . " and share_count > 0";
                  $yyy=$X->sql($sql);
                  if (sizeof($yyy)>0) {
                    $process_date=$yyy[0]['rd'];
                    //-- Get Last Record.
                    $sql="select nobo_count, share_count from ksa_daily_participant where ";
                    $sql.=" share_date = '" . $process_date . "' and participant_id = " . $participant_id . " and security_id = " . $security_id;
  echo $sql;
                    $yyy=$X->sql($sql);
                    $shares=$yyy[0]['share_count'];
                    $nobos=$yyy[0]['nobo_count'];
                    $post=array();
                    $post['table_name']="ksa_daily_participant";
                    $post['id']=$current_id;
                    $post['share_change']=$record['shares']-$current_shares;
                    $post['nobo_change']=$record['number_nobos']-$current_nobos;
                    print_r($post);
                    $X->post($post);
                    //--
                    }
           }
                    //-- Get date list.
                    $sql="select id, the_date from kv_dates where the_date > '" . $process_date . "' and the_date < '" . $report_date . "' order by id";
                    $jjj=$X->sql($sql);
                    foreach($jjj as $j) {
                        $sql="select * from ksa_daily_participant where participant_id = " . $participant_id;
                        $sql .= " and security_id = " . $security_id . " and share_date = '" . $j['the_date'] . "'";
                        $rs=$X->sql($sql);
                        $post=array();
                        $post['table_name']="ksa_daily_participant";
                        if (sizeof($rs)>0) $post['id']=$rs[0]['id'];
                        $post['participant_id']=$participant_id;
                        $post['security_id']=$security_id;
                        $post['company_id']=$company_id;
                        $post['share_count']=$shares;
                        $post['nobo_count']=$nobos;
                        $post['share_date']=$j['the_date'];
                        print_r($post);
                        $X->post($post);
                    }
           }
  die();
  ?>
  