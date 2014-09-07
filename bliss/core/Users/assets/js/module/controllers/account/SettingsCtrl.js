app.controller("account.SettingsCtrl", ["$scope", "regions", "users.Account", function($scope, regions, Account) {
	$scope.account.params.regionId = parseInt($scope.account.params.regionId) || 1;
	
	var _account = angular.copy($scope.account);
	
	$scope.error = false;
	$scope.regions = regions;
	$scope.region = null;
	
	$scope.setPageTitle(["Settings", "My Account"]);
	$scope.setBreadcrumb([{
		title: "My Account",
		path: "account"
	}, {
		title: "Settings",
		path: "account/settings"
	}]);

	$scope.$watch("account.params.regionId", function(id) {
		angular.forEach($scope.regions, function(region) {
			if (region.id === id) {
				$scope.region = region;
			}
		});
	});

	/**
	 * Save the user's settings
	 * 
	 * @returns void
	 */
	$scope.save = function() {
		$scope.setPageLoading(true);
		Account.save({
			path: "settings"
		}, {
			user: $scope.account
		}, function(response) {
			$scope.setPageLoading(false);
			$scope.settingsForm.$setPristine();
		}, function(response) {
			$scope.setPageLoading(false);
			$scope.error = response.data;
			console.error(response.data);
		});
	};
	
	/**
	 * Reset the form
	 * 
	 * @returns void
	 */
	$scope.reset = function() {
		angular.copy(_account, $scope.account);
		$scope.settingsForm.$setPristine();
	};
}]);