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
   $security_id = 4; // $argv[2];
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
//   $company_id = $rs[0]['company_id'];

   $sql="select * from ksa_ta_upload_summary where record_date = '" . $report_date . "' and company_id = " . $company_id;
   $rs=$X->sql($sql);
   if (sizeof($rs)==0) {
           echo $sql;
           die("Invalid Record Date");

        }

        $new="N";
        $summary=$rs[0];
     
        // Get Previous Upload Summary Record
     
        $sql="select * from ksa_ta_upload_summary where record_date < '" . $report_date . "' and company_id = " . $company_id;
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
     
        $sql="select * from ksa_ta_upload_shares2 where record_date = '" . $report_date . "' and security_id = " . $security_id;
        $rs=$X->sql($sql);
        $iii=0;
        $count=sizeof($rs);
        foreach($rs as $record) {
                $iii++;
                echo $iii . " of " . $count . "\r\n";
     
            $sql="select * from ksa_shareholder where shareholder_name = '" . str_replace("'","''",$record['line_1_name_add']) . "'";
            $ns=$X->sql($sql);
            if (sizeof($ns)==0) {
                $post=array();
     
                // 3b. Insert if doesn't exists.
     
                $post['table_name']="ksa_shareholder";
                $post['shareholder_name']=$record['line_1_name_add'];
                if (preg_match('/\d/', $record['line_2_name_add'])) {
                         $post['mailing_address_1']=$record['line_2_name_add'];
                         $post['mailing_address_2']=$record['line_3_name_add'];
                         $post['mailing_address_3']=$record['line_4_name_add'];
                         $post['mailing_address_4']=$record['line_5_name_add'];
                } else {
                         $post['shareholder_name_2']=$record['line_2_name_add'];
                         $post['mailing_address_1']=$record['line_3_name_add'];
                         $post['mailing_address_2']=$record['line_4_name_add'];
                         $post['mailing_address_3']=$record['line_5_name_add'];
                }
                $shareholder_id=$X->post($post);
            } else {
                $shareholder_id=$ns[0]['id'];
            }
     
            // Set shareholder id
     
            $sql="update ksa_ta_upload_shares2 set shareholder_id = " . $shareholder_id . " where id = " . $record['id'];
            $X->execute($sql);

            // Insert Company Shareholder if does not exist.
     
            $sql="select * from ksa_company_shareholder where shareholder_id = " . $shareholder_id . " and security_id = " . $security_id;
            $rs=$X->sql($sql);
               $post=array();
                if (sizeof($rs)>0) {
                   $post['id']=$rs[0]['id'];
                } else {
     
                   // FIRST PURCHASE: Insert Activity
     
                   $post['first_date']=$report_date;
                   $p=array();
                   $p['table_name']="ksa_shareholder_activity";
                   $sql="select id from ksa_shareholder_activity where shareholder_id = " . $shareholder_id;
                   $sql.=" and security_id = " . $security_id;
                   $sql.=" and activity_type = 'NEW' ";
                   $sql.=" and share_date = '" . $report_date . "'";
                   $rr=$X->sql($sql);
                   if (sizeof($rr)>0) $post['id']=$rr[0]['id'];
                   $p['table_name']="ksa_shareholder_activity";
     
                   $p['share_date']=$report_date;
                   $p['shareholder_id']=$shareholder_id;
                   $p['security_id']=$security_id;
                   $p['company_id']=$company_id;
                   $p['beginning_shares']=0;
                   $p['beginning_shares_ta']=0;
                   $p['ending_shares_ta']=$record['total_shares'];
                   $p['activity_type']="NEW";
                   $p['net_shares']=$record['total_shares'];
                   $X->post($p);
     
                   // INSERT COMPANY SHAREHOLDER
     
                   $post['table_name']="ksa_company_shareholder";
                   $post['shareholder_id']=$shareholder_id;
                   $post['security_id']=$security_id;
                   $post['company_id']=$company_id;
                   $X->post($post);
                }
     
                //============================================================================
                // INSERT ksa_daily_shares
                //============================================================================
     
                $post=array();
                $post['table_name']="ksa_daily_shares";
     
                //-- Is there are previous record.
                //
                $sql="select count(*) as c from ksa_daily_shares where shareholder_id = " . $shareholder_id;
                $sql .= " and security_id = " . $security_id . " and share_date < '" . $report_date . "' and ta_total_shares > 0";
                echo $sql;
                $rz=$X->sql($sql);
                if ($rz[0]['c']==0) {
                    $last_shares=0;
                } else {
                    //-- Yes, Find record.
                    $sql="select max(share_date) as c from ksa_daily_shares where shareholder_id = " . $shareholder_id;
                        $sql .= " and security_id = " . $security_id . " and share_date < '" . $report_date . "' and ta_total_shares > 0";
                        echo $sql;
                        $rz=$X->sql($sql);
                        //-- Yes, get record.
                        $sql="select ta_total_shares from ksa_daily_shares where shareholder_id = " . $shareholder_id;
                        $sql .= " and security_id = " . $security_id . " and share_date = '" . $rz[0]['c'] . "' and ta_total_shares > 0";
                        echo $sql;
                        $rz=$X->sql($sql);
                        $last_shares=$rz[0]['ta_total_shares'];
                }
     
                //-- Check for Dup.
                $sql="select * from ksa_daily_shares where shareholder_id = " . $shareholder_id;
                $sql .= " and security_id = " . $security_id . " and share_date = '" . $report_date . "'";
                $rz=$X->sql($sql);
     
                if (sizeof($rz)>0) $post['id']=$rz[0]['id'];
                $post['shareholder_id']=$shareholder_id;
                $post['security_id']=$security_id;
                $post['company_id']=$company_id;
                $post['ta_total_shares']=$record['total_shares'];
                $post['ta_last_shares']=$last_shares;
                $o=getSharePrice($security_id,$report_date,$report_date);
                $post['share_price']=0;
                $post['share_price_open']=$o['open_price'];
                $post['share_price_close']=$o['close_price'];
                $post['share_price_low']=$o['low_price'];
                $post['share_price_high']=$o['high_price'];
     
     //         $post['share_value_open']=$record['shares']*$o['open_price'];
     //         $post['share_value_close']=$record['shares']*$o['close_price'];
     //         $post['share_value_low']=$record['shares']*$o['low_price'];
     //         $post['share_value_high']=$record['shares']*$o['high_price'];
     
                $post['share_date']=$report_date;
                print_r($post);
                $X->post($post);
     
                if ($new=='N') {
                    //
                    // Insert missing daily_shares records going back to first_date
                    //
                    $sql="select * from ksa_company_shareholder where shareholder_id = " . $shareholder_id . " and security_id = " . $security_id;
                    $rs=$X->sql($sql);
                    $first_date=$rs[0]['first_date'];
                    //-- Are there prior records.
                    $sql="select count(*) as c from ksa_daily_shares where share_date < '" . $report_date;
                    $sql.= "' and shareholder_id = " . $shareholder_id . " and security_id = " . $security_id . " and ta_total_shares > 0" ;
                    $yyy=$X->sql($sql);
                    if ($yyy[0]['c']>0) {
                       //-- Get the previous record.
                       $sql="select max(share_date) as rd from ksa_daily_shares where share_type='nobo' and ";
                       $sql.=" share_date < '" . $report_date . "' and shareholder_id = " . $shareholder_id;
                       $sql.=" and security_id = " . $security_id . " and ta_total_shares > 0";
                       $yyy=$X->sql($sql);
                       $process_date=$yyy[0]['rd'];
                       //-- Get the starting value.
                       $sql="select ta_total_shares, share_price from ksa_daily_shares where share_type='nobo' and ";
                       $sql.=" share_date = '" . $process_date . "' and shareholder_id = " . $shareholder_id;
                       $sql.= " and security_id = " . $security_id . " and ta_total_shares > 0";
                       $yyy=$X->sql($sql);
                       $shares=$yyy[0]['ta_total_shares'];
     
                      $post['first_date']=$report_date;
                      //--
                      //-- If share count changed add activity record.
                      //
                      $p=array();
                      $p['table_name']="ksa_shareholder_activity";
                      $p['share_date']=$report_date;
                      $p['shareholder_id']=$shareholder_id;
                      $p['security_id']=$security_id;
                      $p['company_id']=$company_id;
                      $p['beginning_shares_ta']=$shares;
                      $p['ending_shares_ta']=$record['total_shares'];
                      $net_shares=$record['total_shares']-$shares;
                      $p['activity_type']="NOTHING";
                      if ($net_shares>0) $p['activity_type']="TA-PURCHASED";
                      if ($net_shares<0) $p['activity_type']="TA-SOLD";
                      $p['net_shares_ta']=$net_shares;
                      echo $report_date . "\r\n";
                      if ($net_shares!=0) {
                         $sql="select id from ksa_shareholder_activity where shareholder_id = " . $shareholder_id;
                         $sql.=" and security_id = " . $security_id;
                         $sql.=" and activity_type = '" . $p['activity_type'] . "' ";
                         $sql.=" and share_date = '" . $report_date . "'";
                         $rr=$X->sql($sql);
                         if (sizeof($rr)>0) $p['id']=$rr[0]['id'];
                         $X->post($p);
                      }
     
                      //--
                      //-- Get the list of date from prior date until today.
                      //--
                      $sql="select id, the_date from kv_dates where the_date > '" . $process_date . "' and the_date < '" . $report_date . "' order by id";
                      $jjj=$X->sql($sql);
                      foreach($jjj as $j) {
     
                          $sql="select * from ksa_daily_shares where share_type = 'nobo' and  shareholder_id = " . $shareholder_id;
                          $sql .= " and security_id = " . $security_id . " and share_date = '" . $j['the_date'] . "'";
                          $rs=$X->sql($sql);
                          $post=array();
                          $post['table_name']="ksa_daily_shares";
     
                          if (sizeof($rs)>0) $post['id']=$rs[0]['id'];
     
                          $post['shareholder_id']=$shareholder_id;
                          $post['security_id']=$security_id;
                          $post['company_id']=$company_id;
                          $post['ta_total_shares']=$shares;
                          $post['share_price']=0;
                          $post['share_date']=$j['the_date'];
                          echo "." . $post['ta_total_shares'] . ".";
                          $X->post($post);
     
                      } //-- foreach(kv_date);
               }
            }
        }
     //   if ($new=='N') {
                //
                // Check for Leaving Shareholders
                // 
               /* 
                $sql="select shareholder_id, shares from ksa_daily_shares where share_date = '" . $process_date . "' and security_id = " . $security_id;
                $sql.=" and shareholder_id not in (select shareholder_id from ksa_daily_shares where share_date = '" . $report_date . "' and security_id = " . $security_id . ")";
                $rs=$X->sql($sql);
                foreach($rs as $record) { 
                   $p=array();
                   $p['table_name']="ksa_shareholder_activity";
                   $p['share_date']=$report_date;
                   $p['shareholder_id']=$shareholder_id;
                   $p['security_id']=$security_id;
                   $p['company_id']=$company_id;
                   $p['beginning_shares']=$record['shares'];
                   $p['ending_shares']=0;
                   $net_shares=0-$record['shares'];
                   $p['activity_type']="LOST";
                   $p['net_shares']=$net_shares;
                   $sql="select id from ksa_shareholder_activity where shareholder_id = " . $shareholder_id;
                   $sql.=" and security_id = " . $security_id;
                   $sql.=" and activity_type = 'LOST' ";
                   $sql.=" and share_date = '" . $report_date . "'";
                   $rr=$X->sql($sql);
                   if (sizeof($rr)>0) $p['id']=$rr[0]['id'];
                   $X->post($p);
                }
                */
       // }
     die();
     ?>
                         