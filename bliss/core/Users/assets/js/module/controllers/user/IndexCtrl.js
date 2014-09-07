app.controller("user.IndexCtrl", ["$scope", "user", function($scope, user) {
	$scope.user = user;
	
	$scope.setPageTitle([$scope.user.nickname, "Users"]);
	$scope.setBreadcrumb([{
		path: "users",
		title: "Users"
	}, {
		path: $scope.user.path,
		title: $scope.user.nickname
	}]);
}]);