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
   
   $security_id = $argv[1];
   $l_date = $argv[2];
   $h_date = $argv[3];
   $snapshot_type="ksa";
   
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

   $cycles=array();
   $sql="select distinct record_date as r from ksa_nobo_upload_summary where record_date between '" . $l_date . "' and '" . $h_date . "' order by record_date";
   $d=$X->sql($sql);
   $wc=0;
