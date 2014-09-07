app.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/", {
		templateUrl: "./index/index.html"
	});
}]);