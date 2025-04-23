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
   $snapshot_type="weekly";
                
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
   $sql="select * from kv_dates where the_date between '" . $l_date . "' and '" . $h_date . "' order by the_date";
   $d=$X->sql($sql);
   $wc=0;
   foreach($d as $g) {
    $wc++;
    if ($wc==1) $low_date = $g['the_date'];
    if ($wc==7) {
           $high_date = $g['the_date'];
           $wc=0;
           $list=array();
           $list['low_date']=$low_date;
           $list['high_date']=$high_date;
           array_push($cycles,$list);
    }
}

foreach($cycles as $c) {
      $low_date=$c['low_date'];
      $high_date=$c['high_date'];

      $sql="select * from kv_dates where the_date between '" . $low_date . "' and '" . $high_date . "' order by the_date";
      $date_list=$X->sql($sql);
      $day_count=sizeof($date_list);

      $sql="select distinct shareholder_id from ksa_daily_shares where security_id = " . $security_id;
      $sql.=" and share_date = '" . $low_date . "'";
      $shareholders=$X->sql($sql);
      foreach($shareholders as $s) {

           $post=array();
           $post['table_name']="ksa_shareholder_snapshot";

           $post['security_id']=$security_id;
           $post['shareholder_id']=$s['shareholder_id'];
           $post['company_id']=$company_id;
           $post['snapshot_type']=$snapshot_type;
           $post['low_date']=$low_date;
           $post['high_date']=$high_date;

           $sql="select sum(shares) as c from ksa_daily_shares where share_type = 'nobo'";
           $sql.=" and shareholder_id = " . $s['shareholder_id'];
           $sql.=" and security_id = " . $security_id . " and share_date = '" . $low_date . "'";
           $rs=$X->sql($sql);

           $post['open_shares_nobo']=$rs[0]['c'];
           $sql="select sum(shares) as c from ksa_daily_shares where share_type = 'nobo'";
           $sql.=" and shareholder_id = " . $s['shareholder_id'];
           $sql.=" and security_id = " . $security_id . " and share_date = '" . $high_date . "'";
           $rs=$X->sql($sql);
           $post['ending_shares_nobo']=$rs[0]['c'];

           $sql="select sum(shares) as c from ksa_daily_shares where share_type = 'ta'";
           $sql.=" and shareholder_id = " . $s['shareholder_id'];
           $sql.=" and security_id = " . $security_id . " and share_date = '" . $low_date . "'";
           $rs=$X->sql($sql);
           $post['open_shares_ta']=$rs[0]['c'];

           $sql="select sum(shares) as c from ksa_daily_shares where share_type = 'ta'";
           $sql.=" and shareholder_id = " . $s['shareholder_id'];
           $sql.=" and security_id = " . $security_id . " and share_date = '" . $high_date . "'";
           $sql.=" and security_id = " . $security_id . " and share_date = '" . $high_date . "'";
           $rs=$X->sql($sql);
           $post['ending_shares_ta']=$rs[0]['c'];

           $o=getSharePrice($security_id, $low_date, $high_date);

           $post['transaction_value']=0;
           $post['open_price']=$o['open_price'];
           $post['ending_price']=$o['close_price'];
           $post['low_price']=$o['low_price'];
           $post['high_price']=$o['high_price'];
           $post['open_value_nobo']=$post['open_price']*$post['open_shares_nobo'];
           $post['ending_value_nobo']=$post['ending_price']*$post['ending_shares_nobo'];
           $post['open_value_ta']=$post['open_price']*$post['open_shares_ta'];
           $post['ending_market_ta']=$post['ending_price']*$post['ending_shares_ta'];
           $post['total_profit_loss']=0;
           $sql="select id from ksa_shareholder_snapshot where security_id = " . $security_id;
           $sql.=" and shareholder_id = " . $s['shareholder_id'];
           $sql.=" and low_date = '" . $low_date . "' and snapshot_type = '" . $snapshot_type . "'";
           $rrr=$X->sql($sql);
           if (sizeof($rrr)>0) $post['id']=$rrr[0]['id'];
           print_r($post);
           $X->post($post);
}

}
?>
