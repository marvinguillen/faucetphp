<?php
error_reporting(E_ALL & ~ E_NOTICE); ini_set('display_errors', true);

require_once "maincore.php";
require_once "includes/dbconnector.class.php";


echo "----- ChangetoMili ------ </br>";
function ChangetoMili($amount,&$currency) {
	switch ($currency)
	{		
	    case "BTC" : $amount = $amount * 1000;$currency="mBTC";return $amount;
	    case "mBTC" :return $amount;
	    case "Satoshi" : $amount = $amount / 100000;$currency="mBTC";return $amount;
	}
}

echo "------- DbConnector ------ </br>";

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
	
//$api = new AsmoneyAPI($pusername,$papiname, $ppassword);

//Change to mili bitcoin because asmoney get currencies based on milicoin
$db->query("select * from tbl_withdrawal where status=0");

$btcaddresses = array();
$btcamounts = array();
$withdrawalid = array();

while($res=$db->fetchArray()){

    $currency = $faucetcurrency;

    print_r($res);
	echo "</br>---- Res ----- </br>";
		
	if($res['type']==0){
		
		$db2->queryres("select * from tbl_user where user_id='".$res['user_id']."'");
		$ausername=$db2->res['ausername'];

		$amount = ChangetoMili($res['amount'],$currency);
		//$r = $api->Transfer($ausername,$amount,$currency,'Withdrawal'); // Payment memo		echo $r['result'];
		
		
	}else{
	
		$db2->queryres("select * from tbl_user where user_id='".$res['user_id']."'");
		$address=$db2->res['address'];
		$amount = ChangetoMili($res['amount'],$currency);


		if( $currency=='mBTC' ){
			$btcaddresses[count($btcaddresses)] = $address;
			$btcamounts[count($btcamounts)] = $amount;
			$withdrawalid[count($withdrawalid)] = $res['withdrawal_id'];
//			$r = $api->TransferBTC($address,$amount,'mBTC','Withdrawal');
			print_r($btcaddresses);
			echo "</br>---- IF currency ----- </br>";		
		}
		
		
		if( $currency=='mLTC' ){
			$r = $api->TransferLTC($address,$amount,'mLTC','Withdrawal');
		}
		
		if( $currency!='mBTC' )
		if ($r['result'] == APIerror::OK){
			$batchno = $r['value'];
			$db2->query("update tbl_withdrawal set status=1,reccode='$batchno' where withdrawal_id='".$res['withdrawal_id']."'");
            echo "Withdrawal has been proceessed with bactch number " .$batchno. "<br>" ;
		} 
        else {
		    if ($r['result'] == APIerror::InvalidUser )
		    {		echo "Invalid coin address";		}	
        }    
	}

}

if (count($btcamounts) > $requestcount)
{	
	$r = $api->TransferToManyBTC($btcaddresses,$btcamounts,'mBTC','Withdrawal');
	if ($r['result'] == APIerror::OK){
			$batchno = $r['value'];
			for ($i=0;$i<count($withdrawalid);$i++) {
			$wid = $withdrawalid[$i];
			$db2->query("update tbl_withdrawal set status=1,reccode='$batchno' where withdrawal_id='".$wid."'");
			}	
            echo count($withdrawalid). " Withdrawals has been proceessed with bactch number " .$batchno. "<br>" ;
		} else {
		    if ($r['result'] == APIerror::InvalidUser )
		    {		echo "Invalid User";		}
		}
}


?>