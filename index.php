<?php
require_once('class.PSDB.php');

$X=new PSDB();

$sql="SELECT first_name from ksa_user";
$rs=$X->sql($sql);

foreach($rs as $r) {
  echo $r['first_name'];

}

?>