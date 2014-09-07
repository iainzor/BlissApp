app.controller("account.ProfileCtrl", ["$scope", "users.Account", function($scope, Account) {
	var _account = angular.copy($scope.account);
	
	$scope.setPageTitle(["Edit Profile", "My Account"]);
	$scope.setBreadcrumb([{
		title: "My Account",
		path: "account"
	}, {
		title: "Edit Profile",
		path: "account/profile"
	}]);
	
	/**
	 * Submit the form
	 * 
	 * @returns void
	 */
	$scope.save = function() {
		$scope.setPageLoading(true);
		Account.save({
			path: "profile"
		}, {
			user: $scope.account
		}, function(response) {
			$scope.setPageLoading(false);
			angular.extend($scope.account, response.user);
			$scope.profile.$setPristine();
		}, function(response) {
			$scope.setPageLoading(false);
			$scope.setError(response.data);
		});
	};
	
	/**
	 * Reset the form
	 * 
	 * @returns void
	 */
	$scope.reset = function() {
		angular.copy(_account, $scope.account);
		$scope.profile.$setPristine();
	};
	
}]);