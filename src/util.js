function toggleCartItemDetails(cartItem) {
	$(cartItem).find('.cart-item-details').first().toggleClass('hidden');
	$(cartItem).find('.badge').first().toggleClass('hidden');
	$(cartItem).find('.glyphicon').first().toggleClass('glyphicon-menu-down').toggleClass('glyphicon-menu-up');
}

function animateCartIcon() {
	$('#cart-icon').toggleClass('pulsating');
	$('#navbar-toggle-btn').toggleClass('pulsating');

	
	setTimeout(function() {
		$('#cart-icon').toggleClass('pulsating');
		$('#navbar-toggle-btn').toggleClass('pulsating');
	}, 700);
}

function onlyNumericInput(e) {
	var key_codes = [48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 0, 8];

	if (!($.inArray(e.which, key_codes) >= 0)) {
		e.preventDefault();
	}
}