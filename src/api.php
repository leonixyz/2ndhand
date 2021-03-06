<?php

// initialize everything
include('app/init.php');

// get http method and request payload
$method = $_SERVER['REQUEST_METHOD'];
$payload = file_get_contents('php://input');

$signature = "$method";

// parse parameters and compose signature
if(isset($_REQUEST['p'])){
	$p = $_REQUEST['p'];
	$signature .= " $p";
	
	if(isset($_REQUEST['q'])){
		$q = $_REQUEST['q'];
		$signature .= " $q";
		
		if(isset($_REQUEST['r'])){
			$r = $_REQUEST['r'];
			$signature .= " $r";
		}
	}
}

// execute request only if it matches known signatures
switch($signature) {
	case "GET products":
		$db = new DB();
		$res = $db->fetch('products');
		break;


	case "POST orders":
		$orderData = json_decode($payload);
		// file a new order
		$res = fileOrder($orderData);
		if($res === true) {
			$res = 'Your order has been issued correctly and your payment processed';
			// send confirmation email
			$mail = sendConfirmationEmail($orderData);
			if($mail === true) {
				$res .= ', we sent you a confirmail email.';
			}
			else {
				$res .= ', but we couldn\'t send you a confirmation email. Reason: '.print_r($mail);
			}
		}
		else {
			http_response_code(400);
		}
		break;


	default:
		http_response_code(404);
		die('API request unknown');
}

// return json
header('Content-Type: application/json');
echo json_encode($res);
