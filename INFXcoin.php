<?php

function infxcoin_config() {
    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"INFXcoin"),
     "INFXcoinAddress" => array("FriendlyName" => "Your INFXcoin address where you want to receive your INFXcoins", "Type" => "text", ),
    );
	return $configarray;
}

function infxcoin_link($params) {

	# Gateway Specific Variables
	$infxaddr = $params['INFXcoinAddress'];

	# Invoice Variables
	$invoiceid = $params['invoiceid'];
    $amount = $params['amount']; # Format: ##.##
    $currency = $params['currency']; # Currency Code

	# Client Variables
	$firstname = $params['clientdetails']['firstname'];
	$lastname = $params['clientdetails']['lastname'];
	$email = $params['clientdetails']['email'];

	# System Variables
	$systemurl = $params['systemurl'];
	
   $req = array(
        'INFXaddr'      => $infxaddr,
        'invoiceId'     => $invoiceid,
        'systemURL'     => $systemurl,
		'Amount'        => $amount,
		'Currency'      => $currency,
        'buyerName'     => "$firstname $lastname",
    );
	
	$form = '<form action="'.$systemurl.'/modules/gateways/infxcoin/invoice.php" method="POST">';

    foreach ($req as $required => $value) {
        $form.= '<input type="hidden" name="'.$required.'" value = "'.$value.'" />';
    }

    $form.='<input type="submit" value="'.$params['langpaynow'].'" />';
    $form.='</form>';

    return $form;
}



?>