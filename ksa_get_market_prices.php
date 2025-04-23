<?php
   
function getMarketPrice($symbol) {
    $apiKey = "TFQ5M15KX1E1QKUT";  
    $url = "https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=$symbol&apikey=$apiKey&outputsize=full";
    require('class.PSDB.php');
   
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);

    $X=new PSDB();

    $security_id=4;
    $company_id=2;
    foreach ($data["Time Series (Daily)"] as $date => $prices) {
        $post=array();
        $sql="select id from ksa_market_price where share_date = '" . $date . "' and security_id = " . $security_id;
        $rs=$X->sql($sql);
        if (sizeof($rs)>0) $post['id']=$rs[0]['id'];
        $post['table_name']="ksa_market_price";
        $post['security_id']=$security_id;
        $post['company_id']=$company_id;
        $post['share_date']=$date;
        $post['open_price']=$prices["1. open"];
        $post['high_price']=$prices["2. high"];
        $post['low_price']=$prices["3. low"];
        $post['close_price']=$prices["4. close"];
        $post['volume']=$prices["5. volume"];
        $X->post($post);
    }

}

$symbol = "KSEZ"; 
getMarketPrice("BMNR");


?>
