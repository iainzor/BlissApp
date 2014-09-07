app.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/users", {
		templateUrl: "./users/index.html",
		controller: "users.IndexCtrl",
		resolve: {
			users: ["users.User", function(User) {
				var d = User.query();
				return d.$promise;
			}]
		}
	}).when("/users/new", {
		templateUrl: "./users/new.html",
		controller: "users.NewCtrl"
	});
}]);