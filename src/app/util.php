<?php


/*
 * Validates an object to see if it fits all requirements for the application
 */
function isValidDataforOrder($obj) {

	// validate user has all fields and they are consistent
	if(
		!isset($obj->user) ||
		!isset($obj->user->first_name) ||
		!isset($obj->user->last_name) ||
		!isset($obj->user->address) ||
		!isset($obj->user->city) ||
		!isset($obj->user->country) ||
		!isset($obj->user->zip) ||
		!isset($obj->user->phone) ||
		!isset($obj->user->email) ||
		!filter_var($obj->user->email, FILTER_VALIDATE_EMAIL) ||
		!isset($obj->user->cctype) ||
		!isset($obj->user->ccnum) ||
		!is_numeric($obj->user->ccnum) ||
		strlen($obj->user->ccnum) != 16 ||
		!isset($obj->user->cccvv) ||
		!is_numeric($obj->user->cccvv) ||
		strlen($obj->user->cccvv) != 3 ||
		!isset($obj->user->ccexp_year) ||
		!isset($obj->user->ccexp_month)
	) {
		return "Your personal data is not in a valid format";
	}

	// check credit card is not expired
	$expires = DateTime::createFromFormat('dmyy', '01' . sprintf('%02d', intval($obj->user->ccexp_month)+1) . $obj->user->ccexp_year);
	$now = new DateTime();
	if ($expires < $now) {
		return 'Your credit card is expired';
	}

	// validate cart exists and is an array
	if(!isset($obj->cart) || !is_array($obj->cart)) {
		return "Your shopping data is not in a valid format";
	}

	// validate each element in the cart
	foreach($obj->cart as $item) {
		if(
			!isset($item->amount) ||
			!is_numeric($item->amount) ||
			!isset($item->item) ||
			!isset($item->item->Id) ||
			!is_numeric($item->item->Id)
		) return "There are some items in your cart which are in a wrong format";
	}

	return true;
}


/* 
 * Issues a new order
 */
function fileOrder($obj) {

	$errors = array();
	$grandTotal = 0;

	// check the received data is in the right form
	$validation = isValidDataforOrder($obj);
	if($validation !== true) return $validation;

	$db = new DB();
	$db->beginTransaction();

	// fetch current products (only Id and Quantity)
	$products = $db->fetch('products', array(), array('Id', 'Name', 'Price', 'Quantity'));

	// loop on the received data
	foreach($obj->cart as $cartItem) {

		// get the desired item and the amount
		$id = $cartItem->item->Id;
		$amount = $cartItem->amount;

		// get what's in the store corresponding to the Id of the desired object
		$store = array_filter($products, function($obj) use ($id){
			return $obj->Id == $id;
		});

		// if nothing can be found for that Id, push an error
		if(empty($store)) {
			array_push($errors, "No product found with Id = ${id}");
		}
		else {
			// reset array key (there is at most one object, so the key should be 0)
			$store = array_values($store);

			// check the desired amount is satisfiable
			if($store[0]->Quantity < $amount) {
				array_push($errors, "There aren't enough of ". $store[0]->Name);
			}

			// update grand total
			$grandTotal += $store[0]->Price * $amount;
		}

		// update product quantity available in the store
		$newQuantity = $store[0]->Quantity - $amount;
		$db->update('products', array('Quantity', $newQuantity), array("\"Id\" = $id"));
	}

	// try to accept payment
	if(!pay()) {
		array_push($errors, 'Your payment couln\'t be processed, please review your billing data');
	}

	// insert order in database
	if($db->insert('orders', array(
		'User_FirstName' => $obj->user->first_name,
		'User_LastName' => $obj->user->last_name,
		'User_Address' => $obj->user->address,
		'User_City' => $obj->user->city,
		'User_Country' => $obj->user->country,
		'User_ZIP' => $obj->user->zip,
		'User_Phone' => $obj->user->phone,
		'User_Email' => $obj->user->email,
		'JSON' => json_encode($obj->cart)
	)) != 1) {
		array_push($errors, 'Couldn\'t save your order into the database, an error occurred');
	}

	// commit or rollback
	if(empty($errors)) {
		return $db->commit();
	}
	else {
		$db->rollBack();
		return implode($errors, '; ');
	}
}


/*
 * Issue payment: return whether payment succeeded or not
 */
function pay() {
	// dummy
	return true;
}


/*
 * Wrap PHPMailer to send email easier
 */
function sendEmail($to, $subject, $body){
	$mail = getMailer();
	$mail->addAddress($to);
	$mail->addBCC('giulio.roman@eurac.edu');
	$mail->addBCC('aurelia.pagano@unibz.it');
	$mail->Subject = $subject;
	$mail->Body = $body;

	if(!$mail->send()) {
	    return $mail->ErrorInfo;
	} else {
	    return true;
	}
}


/*
 * Send a confirmation email for the order
 */
function sendConfirmationEmail($orderData) {
	//TODO compose email message
	sendEmail('giulio.roman@eurac.edu', 'test', 'test');
}