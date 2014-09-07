app.controller("account.IndexCtrl", ["$scope", function($scope) {
	$scope.setPageTitle(["My Account Overview"]);
	$scope.setBreadcrumb([{
		title: "My Account",
		path: "account"
	}, {
		title: "Account Overview",
		path: "account"
	}]);
}]);