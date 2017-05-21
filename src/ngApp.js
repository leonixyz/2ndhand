var app = angular.module('eshop', ['ngAnimate', 'ngRoute']);

/* Main controller */
app.controller('secondhand', function($scope, $http, $routeParams) {

	// get list of products
	$scope.products = [];
	$http.get('/api/products')
		.then(function(res){
			$scope.products = res.data;                
	});

	// define cart
	$scope.cart = [];

	// get current item from route parameters to be displayed in product detailed view
	$scope.getCurrentItem = function() {
		var id = $routeParams.ProductId;
		return $scope.products.find(function(item) {
			return item.Id == id;
		});
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
	}

	// change amount of objects of a kind in the cart
	$scope.changeAmount = function(cartItem, delta) {
		cartItem.item.Quantity -= delta;
		cartItem.amount += delta;
		if(cartItem.amount == 0) {
			$scope.cart.splice($scope.cart.indexOf(cartItem), 1);
		}
		$scope.updateGrandTotal();
	}

	// empty cart
	$scope.emptyCart = function() {
		$scope.cart.forEach(function(cartItem) {
			cartItem.item.Quantity += cartItem.amount;
		});
		$scope.cart = [];
		$scope.updateGrandTotal();
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
    .when("/home", {
        templateUrl : "templates/home.html"
    })
    .otherwise({
        templateUrl : "templates/home.html"
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