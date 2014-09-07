app.controller("users.IndexCtrl", ["$scope", "users", function($scope, users) {
	$scope.users = users;
	$scope.setPageTitle(["Users"]);
}]);