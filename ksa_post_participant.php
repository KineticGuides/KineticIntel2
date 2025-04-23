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
             
   $sql="select * from ksa_dtc_upload order by d1";
   $rs=$X->sql($sql);
   $last_r=$rs[0];
   $last_r['friday']=$last_r['monday'];
   foreach($rs as $r) {
           $participant_id = 0;
           $sql="select id from ksa_participant where participant_number = " . $r['participant_number'];
           echo $sql;
           $t=$X->sql($sql);
           if (sizeof($t)==0) {
                   $p=array();
                   $p['table_name']="ksa_participant";
                   $p['participant_number']=$r['participant_number'];
                   $p['participant_number']=$r['participant_number'];
                   $particpiant_id = $X->post($p);
           } else {
             $participant_id = $t[0]['id'];
           }

           if ($r['d1']!='') {
              $post=array();
              $post['table_name']="ksa_daily_participant";
              $sql="select id from ksa_daily_participant where participant_id = " . $participant_id;
              $sql.= " and security_id = " . $security_id . " and share_date = '" . $r['d1'] . "'";
              echo $sql;
              $rz=$X->sql($sql);
              if (sizeof($rz)>0) { $post['id']=$rs[0]['id']; }
              $post['share_date']=$r['d1'];
              $post['participant_id']=$participant_id;
              $post['company_id']=$company_id;
              $post['security_id']=$security_id;
              $post['total_share_count']=$r['monday'];
              $post['total_share_change']=$last_r['friday']-$r['monday'];
              print_r($post);
              $X->post($post);
           }

           if ($r['d2']!='') {
             $post=array();
             $post['table_name']="ksa_daily_participant";
             $sql="select id from ksa_daily_participant where participant_id = " . $participant_id;
             $sql .= " and security_id = " . $security_id . " and share_date = '" . $r['d2'] . "'";
             $rz=$X->sql($sql);
             if (sizeof($rz)>0) { $post['id']=$rs[0]['id']; }
             $post['share_date']=$r['d2'];
             $post['participant_id']=$participant_id;
             $post['company_id']=$company_id;
             $post['security_id']=$security_id;
              $post['total_share_count']=$r['tuesday'];
             $post['total_share_change']=$r['tuesday']-$r['monday'];
              print_r($post);
             $X->post($post);
           }


           if ($r['d3']!='') {
             $post=array();
             $post['table_name']="ksa_daily_participant";
             $sql="select id from ksa_daily_participant where participant_id = " . $participant_id;
             $sql .= " and security_id = " . $security_id . " and share_date = '" . $r['d3'] . "'";
             $rz=$X->sql($sql);
             if (sizeof($rz)>0) { $post['id']=$rs[0]['id']; }
             $post['share_date']=$r['d3'];
             $post['participant_id']=$participant_id;
             $post['company_id']=$company_id;
             $post['security_id']=$security_id;
             $post['total_share_count']=$r['wednesday'];
             $post['total_share_change']=$r['wednesday']-$r['tuesday'];
              print_r($post);
             $X->post($post);
            }

            if ($r['d4']!='') {
                $post=array();
                $post['table_name']="ksa_daily_participant";
                $sql="select id from ksa_daily_participant where participant_id = " . $participant_id;
                $sql .= " and security_id = " . $security_id . " and share_date = '" . $r['d4'] . "'";
                $rz=$X->sql($sql);
                if (sizeof($rz)>0) { $post['id']=$rs[0]['id']; }
                $post['share_date']=$r['d4'];
                $post['participant_id']=$participant_id;
                $post['company_id']=$company_id;
                $post['security_id']=$security_id;
                $post['total_share_count']=$r['thursday'];
                $post['total_share_change']=$r['thursday']-$r['wednesday'];
                 print_r($post);
                $X->post($post);
              }
   
              if ($r['d5']!='') {
                $post=array();
                $post['table_name']="ksa_daily_participant";
                $sql="select id from ksa_daily_participant where participant_id = " . $participant_id;
                $sql .= " and security_id = " . $security_id . " and share_date = '" . $r['d5'] . "'";
                $rz=$X->sql($sql);
                if (sizeof($rz)>0) { $post['id']=$rs[0]['id']; }
                $post['share_date']=$r['d5'];
                $post['participant_id']=$participant_id;
                $post['company_id']=$company_id;
                $post['security_id']=$security_id;
                $post['total_share_count']=$r['friday'];
                $post['total_share_change']=$r['friday']-$r['thursday'];
                print_r($post);
                $X->post($post);
              }
              $last_r=$r;
      }
   
   
   
   ?>
   