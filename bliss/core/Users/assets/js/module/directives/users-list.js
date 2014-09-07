app.directive("usersList", ["users.User", function(User) {
	return {
		link: function($scope) {
			$scope.users = User.query();
		}
	};
}]);