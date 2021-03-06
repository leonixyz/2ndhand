/* Second Hand Shop app */
var app = angular.module('eshop', ['ngAnimate', 'ngRoute']);


/* Main controller */
app.controller('secondhand', function($scope, $http, $routeParams, $location) {

	// get list of products
	$scope.products = [];
	$http.get('/api/products')
		.then(function(res){
			$scope.products = res.data;                
	});

	// define cart
	$scope.cart = [];

	// define user
	$scope.user = {
		first_name: "",
		last_name: "",
		address: "",
		city: "",
		country: "",
		zip: "",
		phone: "",
		email: "",
		cctype: "",
		ccnum: "",
		cccvv: "",
		ccexp_year: "",
		ccexp_month: ""
	};

	// define submission message
	$scope.submissionMessage = {
		title: null,
		text: null
	};

	// get current item from route parameters to be displayed in product detailed view
	$scope.getCurrentItem = function() {
		var id = $routeParams.ProductId;
		var item = $scope.products.find(function(item) {
			return item.Id == id;
		});
		if(item==null) {
			$location.path('/not-found');
		}
		return item;
	}

	// define grand total (and how to calculate it)
	$scope.grandTotal = 0;
	$scope.updateGrandTotal = function() {
		total = 0;
		$scope.cart.forEach(function(cartItem){
			total += cartItem.amount * cartItem.item.Price;
		});
		$scope.grandTotal = total;
	};

	// add an item to the cart
	$scope.addToCart = function(item) {
		var inCart = false;
		$scope.cart.forEach(function(cartItem) {
			if(cartItem.item.Id == item.Id) {
				inCart = true;
				cartItem.amount++;
			}
		});
		if(!inCart) {
			$scope.cart.push(
				{
					"amount" : 1,
					"item" : item
				});
		}
		item.Quantity--;
		$scope.updateGrandTotal();
		animateCartIcon();
		$scope.updateCartCounterBadge();
	}

	// change amount of objects of a kind in the cart
	$scope.changeAmount = function(cartItem, delta) {
		cartItem.item.Quantity -= delta;
		cartItem.amount += delta;
		if(cartItem.amount == 0) {
			$scope.cart.splice($scope.cart.indexOf(cartItem), 1);
		}
		$scope.updateGrandTotal();
		$scope.updateCartCounterBadge();
	}

	// counts the total amount of objects in the cart and updated the badge
	$scope.updateCartCounterBadge = function() {
		var count = 0;
		for(var i = 0; i < $scope.cart.length; i++) {
			count += $scope.cart[i].amount;
		}
		$('#cart-counter').html(count);
		$('#mobile-cart-counter').html(count);
	}

	// empty cart
	$scope.emptyCart = function() {
		$scope.cart.forEach(function(cartItem) {
			cartItem.item.Quantity += cartItem.amount;
		});
		$scope.cart = [];
		$scope.updateGrandTotal();
		$scope.updateCartCounterBadge();
	}

	// send order
	$scope.sendOrder = function() {
		$('#confirm-payment-button').toggleClass('hidden');
		$('.spinner').toggleClass('hidden');
		setTimeout(function() {
			$http({
				method: 'POST',
				url: '/api/orders',
				data: JSON.stringify({
					user: $scope.user,
					cart: $scope.cart
				})
			}).then(function successCallback(response) {
				$scope.submissionMessage.title = "Success!";
				$scope.submissionMessage.text = response.data;
				$('.spinner').toggleClass('hidden');
				$('#submissionModal').modal('show');
				$('#confirm-payment-button').toggleClass('hidden');
				$scope.emptyCart();
				$http.get('/api/products')
					.then(function(res){
						$scope.products = res.data;                
				});
			}, function errorCallback(response) {
				$scope.submissionMessage.title = "Error!";
				$scope.submissionMessage.text = response.data;
				$('.spinner').toggleClass('hidden');
				$('#submissionModal').modal('show');
				$('#confirm-payment-button').toggleClass('hidden');
			});
		}, 5000);
	}
});


/* App routing */
app.config(function($routeProvider) {
   $routeProvider
    .when("/products", {
        templateUrl : "templates/products.html"
    })
    .when("/products/:ProductId", {
        templateUrl : "templates/details.html"
    })
    .when("/cart", {
        templateUrl : "templates/cart.html"
    })
    .when("/checkout", {
        templateUrl : "templates/checkout.html"
    })
    .when("/about", {
        templateUrl : "templates/about.html"
    })
    .when("/", {
        templateUrl : "templates/home.html"
    })
    .otherwise({
        templateUrl : "templates/404.html"
    });
});


/* Custom filters */
app.filter('availableInStock', function() {
	return function(items) {
		var filtered = [];
		angular.forEach(items, function(item) {
			if(item.Quantity > 0) {
				filtered.push(item);
			}
		});
		return filtered;
	};
});

app.filter('isEmpty', function() {
	return function(items) {
		return items.length == 0;
	};
});
