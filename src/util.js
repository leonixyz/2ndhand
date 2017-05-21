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