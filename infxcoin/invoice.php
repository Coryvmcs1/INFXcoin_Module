<?php
include("../../../init.php");
$errMSG = "";
$err = 0;
$infxaddr = $_POST['INFXaddr'];
$invoiceid = (int) $_POST['invoiceId'];
$amount = $_POST['Amount'];
$websiteurl = $_POST['systemURL'];
$usd = $_POST['Currency'];
$buyer = $_POST['buyerName'];

$invdata = mysql_fetch_assoc(mysql_query("SELECT status,notes FROM `tblinvoices` WHERE `id`='".$invoiceid."'"));
if(isset($_COOKIE['invoiceid'])){
$get_data = explode('|', $_COOKIE['invoiceid']); 
if($get_data[0] == $invoiceid){
$s = 1;
}
}
if($invdata['status'] != "Unpaid"){
$err = 2;
$errMSG = "Error: Invoice is already marked as paid.";
}
if($infxaddr == ""){
$err = 1;
$errMSG = "Error: Module error.";
}
if($err == 0){
 $usd = strtolower($usd);

 if( 'infx'==$usd ) {
  $infxval = 1;
 } else {
  $infxvalue = file_get_contents("https://api.influxcoin.xyz/rate.json");
  $obj = json_decode($infxvalue);
  $infxval = $obj->$usd;
 }
  
 if($infxval){
  $ninfx = number_format($amount / $infxval, 6);
 }else{
  $err = 3;
  $errMSG = "Unsupported Currency.";
 }
}
if($err == 0 && $s != 1){
$my_callback_url = ''.$websiteurl.'/modules/gateways/infxcoin/callback.php?invoiceid='.$invoiceid.'&ninfx='.$ninfx.'&amount='.$amount.'';
$root_url = 'https://api.influxcoin.xyz/receive.php';
$parameters = 'method=create&address=' . $infxaddr .'&callback='. urlencode($my_callback_url);
$response = file_get_contents($root_url . '?' . $parameters);
$object = json_decode($response);

mysql_query("UPDATE `tblinvoices` SET `notes`='".$object->salt_hash."'") or die("couldn't connect to dbase");
setcookie("invoiceid", $invoiceid.'|'.$object->input_address.'|'.$ninfx, time() + (60 * 20));
}


?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title>INFXcoin - Payments</title>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<script type="text/javascript" src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
<script type="text/javascript"> var auto_refresh = setInterval( function() { $('#status').load('status.php?id=<?php echo $invoiceid ?>&hash=<?php echo $object->salt_hash ?>'); }, 5000);</script>
<style>body{background: #fff;font: 13px Trebuchet MS, Arial, Helvetica, Sans-Serif;color: #333;line-height: 160%;margin: 0;padding: 0;text-align: center;}h1{font-size: 200%;font-weight: normal;margin-top:100px;}.centerdiv{margin:20px;}</style>
</head>
<body>
<h1>Howdy <?php echo $buyer ?> !</h1>
<div class="centerdiv"><img src="https://wallet.influxcoin.xyz/assets/img/infx.png" style="width:80px;height:80px;"/></div>
<div style="position:relative;">
<?php echo $errMSG ?>
<?php if($err == 0){ ?>
<font style="font-size:125%;">
1 INFXcoin = <?php echo $infxval . $usd ?></br>
Send  <b><?php if($s == 1){ echo $get_data[2]; }else{ echo $ninfx; } ?></b>  INFXcoins to : <b><?php if($s == 1){ echo $get_data[1]; }else{ echo $object->input_address; } ?></b></br></br>
</font></br>
Send the exact same amount of INFXcoins shown else there might be problem in crediting</br>
And send only one time for invoice.. Else it will be really hard to track & refund.</br>
After sending, wait for the transaction to confirm, it can take upto 15minutes.</br>

<div id="status" style="width:100%;margin-top:35px;text-align:center;"><font style="font-size:17px;">Unpaid</font></div>
<div id="warn" style="width:100%;margin-top:50px;text-align:center;"><font style="font-size:14px;color:red;">Do Not Refresh the Page.</font></div>

<?php } ?>

</div>
</body>
</html>