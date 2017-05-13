<?php
include('app/init.php');

// get http method and payload
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

	default:
		http_response_code(404);
		die('API request unknown');
}

// return json
header('Content-Type: application/json');
echo json_encode($res);

?>
