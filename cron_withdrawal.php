<?php
error_reporting(E_ALL & ~ E_NOTICE); ini_set('display_errors', true);

require_once "maincore.php";
require_once "includes/dbconnector.class.php";

//Adding Lib for SuperiorCoin Functions
require "../vendor/autoload.php";
use Superior\Wallet;
$walletFaucet = new Superior\Wallet();



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

		
}
echo "</br><h5>Array with adresses</br></h5>";
echo $btcaddresses;
echo "</br></br><h5>Array with ammounts</br></h5>";
echo $btcamounts;


if (count($btcamounts) > $requestcount)
{	
		
	echo "</br><h3>There are ". count($btcamounts)." no processed withdrawals in our database . </br> 
	We will processing in group/lot of ". $requestcount." to run Superior Transfer cronjob.</br>";
	echo "RUnning cronjob </h3>";

	$btcaddresses= array_slice($btcaddresses, 0, $requestcount);
	$btcamounts = array_slice($btcamounts, 0, $requestcount);

    echo "</br><h1>Var dump btc ammounts</h1></br>";
	var_dump($btcamounts);
	var_dump($btcaddresses);

	$pablo='5NKJdxdiCmccLyw53D8MzUhZYzDDvdBXshrVhUgYSYjyJFk3Wn5bMjsDSCxzSi1d95M83fENY7uEmUm5t2Uj8rGEFXFTQ3q';
	$dennis = '5NbCTMansKp1AmRUV9sxxcBJEi4avk3dt7RsXsxo6vFVSqZCTEsuCgXTiQZCsKM5TdGQD2m6UpM58KoDLEtX7ofH61t9hNZ';
    

	$destination1 = (object) [
	    'amount' => '3',
	    'address' => $pablo
	];
	$destination2 = (object) [
	    'amount' => '2',
	    'address' => $dennis
	];

	$destination22 = array();
	$destination22[1] = (object) array('amount' => '1', 'address' => $pablo);
	$destination22[2] = (object) array('amount' => '1', 'address' => $dennis);

    
    echo "</br><h1>destination22</br></h1>";
	print_r($destination22);

	

	$options2 = [
	    'destinations' => $destination22[1],$destination22[2]
	];

	echo "</br><h1>option2</br></h1>";
	print_r($options2);
	

	for ($i=1; $i <count($destination22) ; $i++) { 
		
		$options2 = [
			'destinations' => $destination22[i];
		];
	}

	echo "</br><h1>option2</br></h1>";
	print_r($options2);

		


	$sup_transfer = $walletFaucet->transfer($options2);
	print_r($sup_transfer);
	$transfer_result = json_decode($sup_transfer);
	
	$now = new DateTime();
	echo $now->format('Y-m-d H:i:s');    // MySQL datetime format
	echo $now->getTimestamp();  



	//if "fee" exists in transfer response means that transfe was successfull
	if (isset($transfer_result->{'fee'})) {
		echo "</br> <h1>Success Transfer!</h1> </br>";
		$transfer_fee = $transfer_result->{'fee'};
		$transfer_hash = $transfer_result->{'tx_hash'};
		echo 
		"Transfer Fee: ".$transfer_fee. 
		"</br>Transfer Hash: ".$transfer_hash;


		$hash_transfer=$transfer_hash;

		for ($i=0;$i<$requestcount;$i++) {
			echo "Counter = ".$i." -- ";
			$wid = $withdrawalid[$i];
			print_r($wid);
			echo "- update tbl_withdrawal set status=1,reccode=".$hash_transfer." where withdrawal_id= ".$wid.".</br>";
			//$db2->query("update tbl_withdrawal set status=1,reccode='".$hash_transfer."',fee=".$transfer_fee." where withdrawal_id=".$wid."");
		}

	    echo "</br><h3>".$requestcount. " Withdrawals has been proceessed with hash number:".$hash_transfer."</h3>" ;


	//if "fee" not exists in transfer response means that error exists
	} else {
		$transfer_errorcode = $transfer_result->{'code'};
		$transfer_errormessage = $transfer_result->{'message'};
		echo "<h1>The Transfer has not been processed</br> Error Transfer!</h1> </br> ";
		echo 
		"Error Code: ".$transfer_errorcode. 
		"</br>Error Message: ".$transfer_errormessage;
	}
		
}
else {
	echo "</br><h3>There are only ". count($btcamounts)." withdrawals in our database </br> We must have more than ". $requestcount." withdrawals to run Superior Transfer Cronjob.</h3>";
				
}

?>