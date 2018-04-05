<?php
error_reporting(E_ALL & ~ E_NOTICE); ini_set('display_errors', true);

require_once "maincore.php";
require_once "includes/dbconnector.class.php";



function ChangetoMili($amount,&$currency) {
	switch ($currency)
	{		
	    case "BTC" : $amount = $amount * 1000;$currency="mBTC";return $amount;
	    case "mBTC" :return $amount;
	    case "Satoshi" : $amount = $amount / 100000;$currency="mBTC";return $amount;
	}
}


$db=new DbConnector;
$db->queryres("select * from tbl_config where header='currency'");
$faucetcurrency=$db->res['value'];
$db->queryres("select * from tbl_config where header='pusername'");
$pusername=$db->res['value'];
$db->queryres("select * from tbl_config where header='papiname'");
$papiname=$db->res['value'];
$db->queryres("select * from tbl_config where header='ppassword'");
$ppassword=$db->res['value'];
$db->queryres("select * from tbl_config where header='requestcount'");
$requestcount=$db->res['value'];
	

//Change to mili bitcoin because asmoney get currencies based on milicoin
$db->query("select * from tbl_withdrawal where status=0");

$btcaddresses = array();
$btcamounts = array();
$withdrawalid = array();

while($res=$db->fetchArray()){

    $currency = $faucetcurrency;

    //print_r($res);		
	$db2->queryres("select * from tbl_user where user_id='".$res['user_id']."'");
	$address=$db2->res['address'];
	$amount = ChangetoMili($res['amount'],$currency);

	$btcaddresses[count($btcaddresses)] = $address;
	$btcamounts[count($btcamounts)] = $amount;
	$withdrawalid[count($withdrawalid)] = $res['withdrawal_id'];
	//print_r($btcaddresses);
		
}
echo "</br><h5>Array with adresses</br></h5>";
print_r($btcaddresses);
echo "</br></br><h5>Array with ammounts</br></h5>";
print_r($btcamounts);


if (count($btcamounts) > $requestcount)
{	
	$btcaddresses= array_slice($btcaddresses, 0, $requestcount);
	$btcamounts = array_slice($btcamounts, 0, $requestcount);

	
	echo "</br><h3>There are ". count($btcamounts)." no processed withdrawals in our database . </br> 
	We will processing in group/lot of ". $requestcount." to run Superior Transfer cronjob.</br>";
	echo "RUnning cronjob </h3>";

	$hash_transfer="333-kjjdkjdkkjdkdkj-00hash-33e3443434-11";

	for ($i=0;$i<$requestcount;$i++) {
		//while ( $i<= ($requestcount-1)) {
			
		echo "Counter = ".$i." -- ";
		$wid = $withdrawalid[$i];
		print_r($wid);
		echo "- update tbl_withdrawal set status=1,reccode=".$hash_transfer." where withdrawal_id= ".$wid.".</br>";
		$db2->query("update tbl_withdrawal set status=0,reccode='".$hash_transfer."' where withdrawal_id=".$wid."");
		//}
	}

    echo "</br><h3>".$requestcount. " Withdrawals has been proceessed with hash number:".$hash_transfer."</h3>" ;	
		
}
else {
	echo "</br><h3>There are only ". count($btcamounts)." withdrawals in our database </br> We must have more than ". $requestcount." withdrawals to run Superior Transfer Cronjob.</h3>";
				
}

?>