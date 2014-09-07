app.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/users/:id-:alias?", {
		templateUrl: "./user/index.html",
		controller: "user.IndexCtrl",
		resolve: {
			user: ["$route", "users.User", function($route, User) {
				var d = User.get({
					path: $route.current.params.id
				});
				return d.$promise;
			}]
		}
	}).when("/users/:id*:alias?/edit", {
		templateUrl: "./user/edit.html",
		controller: "user.EditCtrl"
	}).when("/users/:id*:alias?/delete", {
		templateUrl: "./user/delete.html",
		controller: "user.DeleteCtrl"
	});
}]);