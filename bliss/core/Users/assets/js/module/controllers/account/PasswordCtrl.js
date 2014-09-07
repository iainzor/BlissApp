app.controller("account.PasswordCtrl", ["$scope", "users.Account", function($scope, Account) {
	$scope.setPageTitle(["Change Password", "My Account"]);
	$scope.setBreadcrumb([{
		path: "account",
		title: "My Account"
	}, {
		path: "account/password",
		title: "Change Password"
	}]);

	$scope.currentPassword = null;
	$scope.newPassword = null;
	$scope.newPasswordConfirm = null;
	$scope.error = false;
	$scope.success = false;

	/**
	 * Save the new password
	 * 
	 * @returns void
	 */
	$scope.save = function() {
		$scope.setPageLoading(true);
		$scope.error = false;
		$scope.success = false;
		
		Account.save({
			path: "password"
		}, {
			currentPassword: $scope.currentPassword,
			newPassword: $scope.newPassword,
			newPasswordConfirm: $scope.newPasswordConfirm
		}, function(response) {
			$scope.setPageLoading(false);
			$scope.success = true;
			$scope.reset();
		}, function(response) {
			$scope.setPageLoading(false);
			
			if (response.data.message) {
				$scope.error = response.data.message;
			} else {
				$scope.error = "An unknown error occurred";
			}
		});
	};
	
	/**
	 * Reset the form
	 * 
	 * @returns void
	 */
	$scope.reset = function() {
		$scope.passwordForm.$setPristine();
		$scope.currentPassword = null;
		$scope.newPassword = null;
		$scope.newPasswordConfirm = null;
		$scope.error = false;
	};
}]);