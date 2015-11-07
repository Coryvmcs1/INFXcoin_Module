<?php

# Required File Includes
include("../../../init.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule = "INFXcoin"; # Enter your gateway module name here replacing template

$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback


$transaction_hash = $_GET['transaction_hash'];
  $input_transaction_hash = $_GET['input_transaction_hash'];
  $input_address = $_GET['input_address'];
  $value_in_influx = $_GET['value'];
  $value_in_infx = $value_in_influx / 100000000;
  $confirmations = $_GET['confirmations'];
  $security_hash = $_GET['security_hash'];
  $invoiceid = $_GET['invoiceid'];
  $ninfx = $_GET['ninfx'];
  $amount = $_GET['amount'];
  $fee = 0.00;
$invdata = mysql_fetch_assoc(mysql_query("SELECT status,notes FROM `tblinvoices` WHERE `id`='".$invoiceid."'"));

$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing

checkCbTransID($transaction_hash); # Checks transaction number isn't already in the database and ends processing if it does

// Verify the security hash
  $salt_hash = $invdata['notes']; // Get the salt_hash from DB
  $my_hash = md5( $salt_hash . "-" . $input_transaction_hash . "-" .
    $value_in_influx . "-" . $confirmations );
  if( $my_hash != $security_hash ) {
    die("Invalid Security Hash.");
  }


if ($confirmations > 6 && $transaction_hash) {
if($ninfx = $value_in_infx){
    # Successful
    addInvoicePayment($invoiceid,$transaction_hash,$amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
	logTransaction($GATEWAY["name"],$_POST,"Successful"); # Save to Gateway Log: name, data array, status
	mysql_query("UPDATE `tblinvoices` SET `notes`=''");
	echo "*ok*";
	}else{
mysql_query("UPDATE `tblinvoices` SET `notes`='Sent incorrect amount of INFX: ".$ninfx."'");
echo "*ok*";
}
}


?>