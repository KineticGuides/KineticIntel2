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

   $security_id = 4;
   $company_id = 2;

   //-- Get Last Date -- DTC
   $sql="select id, participant_id from ksa_company_participant where security_id = " . $security_id . " and company_id = " . $company_id;
   $sql.=" and participant_id in (select participant_id from ksa_dtc_upload union select participant_id from ksa_daily_participant)" ;
   $participants=$X->sql($sql);
   foreach($participants as $p) {
       $post=array();
       $post['id']=$p['id'];
       $post['table_name']="ksa_company_participant";
       //-- DTC
           $sql="select count(*) as c from ksa_dtc_upload where security_id = " . $security_id;
           $sql.=" and participant_id = " . $p['participant_id'] . " and d5 <> 0";
           $rs=$X->sql($sql);
           if($rs[0]['c']>0) {
           $sql="select max(d5) as dtc_date from ksa_dtc_upload where security_id = " . $security_id;
           $sql.=" and participant_id = " . $p['participant_id'];
           $rs=$X->sql($sql);
           $dtc_date=$rs[0]['dtc_date'];
       $post['last_date_dtc']=$dtc_date;
           $sql="select * from ksa_dtc_upload where security_id = " . $security_id;
           $sql.=" and participant_id = " . $p['participant_id'] . " and d5 = '" . $dtc_date . "'" ;
           $rs=$X->sql($sql);
           $post['total_share_count']=$rs[0]['friday'];
           }
           $sql="select max(share_date) as record_date from ksa_daily_participant where company_id = " . $company_id;
           $sql.=" and participant_id = " . $p['participant_id'];
           $rs=$X->sql($sql);
           $record_date=$rs[0]['record_date'];
       $post['last_date_ta']=$record_date;
           $sql="select total_share_count, total_share_change from ksa_daily_participant where company_id = " . $company_id;
           $sql.=" and participant_id = " . $p['participant_id'] . " and share_date = '" . $record_date . "'" ;
           $rs=$X->sql($sql);
       $post['dtc_share_count']=$rs[0]['total_share_count'];
       $post['dtc_share_change']=$rs[0]['total_share_change'];
           $sql="select count(*) as c from ksa_daily_participant where company_id = " . $company_id;
           $sql.=" and participant_id = " . $p['participant_id'] . " and share_count <> 0";
           $rs=$X->sql($sql);
           $sql.=" and participant_id = " . $p['participant_id'] . " and share_count <> 0";
           $rs=$X->sql($sql);
           if ($rs[0]['c']>0) {
           $sql="select max(share_date) as record_date from ksa_daily_participant where company_id = " . $company_id;
           $sql.=" and participant_id = " . $p['participant_id'] . " and share_count <> 0";
           $rs=$X->sql($sql);
           $record_date=$rs[0]['record_date'];
       $post['last_date_nobo']=$record_date;
           $sql="select nobo_count, share_count, nobo_change, share_change from ksa_daily_participant where company_id = " . $company_id;
           $sql.=" and participant_id = " . $p['participant_id'] . " and share_date = '" . $record_date . "'" ;
           echo $sql;
           $rs=$X->sql($sql);
       $post['nobo_share_count']=$rs[0]['share_count'];
       $post['nobo_share_change']=$rs[0]['share_change'];
       $post['nobo_holder_count']=$rs[0]['nobo_count'];
       $post['nobo_holder_change']=$rs[0]['nobo_change'];
       $post['company_id']=$company_id;

       print_r($post);
       $X->post($post);
           }
   }
?>
