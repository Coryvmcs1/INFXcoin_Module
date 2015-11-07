<?php
include("../../../init.php");
$invoiceid = mysql_real_escape_string($_GET['id']);
$invstatus = mysql_fetch_assoc(mysql_query("SELECT status,notes FROM `tblinvoices` WHERE `id`='".$invoiceid."'"));
if($invstatus['status'] == "Paid"){
echo "<font style='font-size:17px;color:green;font-weight:bold;'>You Have Successfully paid your invoice.</font>";
}elseif($invstatus['notes'] == $_GET['hash']){
echo "<font style='font-size:17px;'>Unpaid</font>";
}elseif($invstatus['status'] = "Unpaid"){
echo "<font style='font-size:17px;'>Unpaid</font>";
}else{
echo "<font style='font-size:17px;color:red;font-weight:bold;'>You have transacted incorrect amount, Please contact support.</font>";
}
?>