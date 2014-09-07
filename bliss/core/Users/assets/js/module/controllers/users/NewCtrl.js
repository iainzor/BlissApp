app.controller("users.NewCtrl", ["$scope", "users.User", function($scope, User) {
	var _blankUser = {
		role: "user"
	};
	
	$scope.user = angular.copy(_blankUser);
	
	$scope.setPageTitle(["New User", "Users"]);
	$scope.setBreadcrumb([{
		title: "Users",
		path: "users"
	}, {
		title: "New User",
		path: "users/new"
	}]);
	
	/**
	 * Save the user
	 * 
	 * @returns void
	 */
	$scope.save = function() {
		$scope.setPageLoading(true);
		
		User.save({
			path: "new"
		}, {
			user: $scope.user
		}, function(response) {
			$scope.setPageLoading(false);
			$scope.user = response.user;
		}, function(response) {
			$scope.setPageLoading(false);
			console.error(response);
		});
	};
	
	/**
	 * Reset the form
	 * 
	 * @returns void
	 */
	$scope.reset = function() {
		$scope.user = angular.copy(_blankUser);
	};
}]);