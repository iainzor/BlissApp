app.directive("userPanel", [function() {
	return {
		templateUrl: "users/directives/panel.html",
		terminal: true,
		scope: {
			hideEmail: "=",
			hideNav: "=",
			hideHeader: "=",
			user: "=userPanel"
		},
		controller: ["$scope", "$attrs", function($scope, $attrs) {
			$scope.account = $scope.$parent.account;
			$scope.location = $scope.$parent.location;
		}]
	};
}]);