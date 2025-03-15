<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
   require_once('class.PSDB.php');
    $url = "https://data.sec.gov/submissions/CIK0001829311.json";
    $current_cik=1945619;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
    $jsonData = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    } 
    curl_close($ch);

   $jsonArray = json_decode($jsonData, true);
   $current_id = 0;
   $X=new PSDB();
    foreach($jsonArray as $name => $value) {
        if ($name=="cik") {
                $sql="select * from edgar_filing where cik = '" . $value . "'";
                $start_time=microtime(true);
                $t=$X->sql($sql);
                $end_time=microtime(true);
                if (sizeof($t)==0) {
                    $process="Y";
                } else {
                    $process="Y";
                }
            }
        }
        if ($process=="Y") {
            //-- The Entity
            $post=array();
            $post['table_name']="edgar_entity";
            $post['action']="insert";
            $sql="select * from edgar_entity where cik = '" . $jsonArray['cik'] . "'";
            $current_cik=$jsonArray['cik'];
            $y=$X->sql($sql);
            if (sizeof($y)!=0) {
                $post['id']=$y[0]['id'];
            }
            foreach($jsonArray as $name => $value) {
                 if (!is_array($value)&&!is_object($value)) {
                     $post[$name]=$value;
                 }
                 if ($name=="name") $post['entityName']=$value;
            }
            if (!isset($post['ein'])) $post['ein']="";
            if (!isset($post['phone'])) $post['phone']="";
            if (!isset($post['fiscalYearEnd'])) $post['fiscalYearEnd']="";
            print_r($post);

            $X->post($post);
            //-- Addresses

            if (isset($jsonArray['addresses'])) {
            foreach($jsonArray['addresses'] as $name => $value) {
                    $address_type=$name;
                    $post=$value;
                    $post['table_name']="edgar_address";
                    $post['action']="insert";
                    $sql="select id from edgar_address where cik = '" . $current_cik . "' and address_type = '" . $address_type . "'";
                    $y=$X->sql($sql);
                    if (sizeof($y)>0) $post['id']=$y[0]['id'];
                    $post['cik']=$current_cik;
                    if (!isset($post['street1'])) $post['street1']="";
                    if (!isset($post['street2'])) $post['street2']="";
                    if (!isset($post['stateOrCountry'])) $post['stateOrCountry']="";
                    if (!isset($post['stateOrCountryDescription'])) $post['stateOrCountryDescription']="";
                    if (!isset($post['zipCode'])) $post['zipCode']="";
                    print_r($post);
                    $X->post($post);
            }
            }

            if (isset($jsonArray['formerNames'])) {
            foreach($jsonArray['formerNames'] as $n) {
                    $post=array();
                    $post['action']="insert";
                    $post['table_name']="edgar_former_name";
                    $post['cik']=$current_cik;
                    $post['former_name']=$n['name'];
                    $post['name_from']=$n['from'];
                    $post['name_to']=$n['to'];
                    $sql="select id from edgar_former_name where cik = '" . str_replace("'","''",$current_cik) . "' and former_name = '" . str_replace("'","''",$n['name']) . "'";
                    $sql=str_replace("\\","",$sql);

                    //echo $sql . "\r\n";
                    $start_time=microtime(true);
                    $y=$X->sql($sql);
                    $end_time=microtime(true);
                    //echo $end_time-$start_time . "\r\n";
                    if (sizeof($y)>0) $post['id']=$y[0]['id'];
                    print_r($post);
                    $X->post($post);
            }
            }

            if (isset($jsonArray['filings']['recent'])) {
                $i=0;
                        echo $current_cik . "\r\n";
               foreach($jsonArray['filings']['recent']['accessionNumber'] as $an) {
                        $post=array();
                        $post['action']="insert";
                        $post['table_name']="edgar_filing";
                        $post['accessionNumber']=$jsonArray['filings']['recent']['accessionNumber'][$i];
                        $post['cik']=$current_cik;
                        $post['filingDate']=$jsonArray['filings']['recent']['filingDate'][$i];
                        $post['reportDate']=$jsonArray['filings']['recent']['reportDate'][$i];
                        $post['acceptanceDateTime']=$jsonArray['filings']['recent']['acceptanceDateTime'][$i];
                        $post['act']=$jsonArray['filings']['recent']['act'][$i];
                        $post['form']=$jsonArray['filings']['recent']['form'][$i];
                        $post['fileNumber']=$jsonArray['filings']['recent']['fileNumber'][$i];
                        $post['filmNumber']=$jsonArray['filings']['recent']['filmNumber'][$i];
                        $post['items']=$jsonArray['filings']['recent']['items'][$i];
                        $post['size']=$jsonArray['filings']['recent']['size'][$i];
                        if (isset($jsonArray['filings']['recent']['isXRBL'])) $post['isXBRL']=$jsonArray['filings']['recent']['isXRBL'][$i];
                        if (isset($jsonArray['filings']['recent']['isInlineXRBL'])) $post['isInlineXBRL']=$jsonArray['filings']['recent']['isInlineXRBL'][$i];
                        $post['primaryDocument']=$jsonArray['filings']['recent']['primaryDocument'][$i];
                        $post['primaryDocDescription']=$jsonArray['filings']['recent']['primaryDocDescription'][$i];
                        //if ($post['isXBRL']=="") $post['isXBRL']=0;
                        //if ($post['isInlineXBRL']=="") $post['isInlineXBRL']=0;
                        $start_time=microtime(true);
                        print_r($post);
                        $X->postFast($post);
                        $end_time=microtime(true);
                        //echo $end_time-$start_time . "\r\n";
                        $i++;
                }
                }
        }
?>
