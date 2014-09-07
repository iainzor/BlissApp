app.controller("account.MenuCtrl", ["$scope", "users.Account", "users.User", function($scope, Account, User) {
	$scope.acl = {
		canSignIn: Account.isAllowed(User.RESOURCE_NAME, ["read"], {controller:"account",action:"sign-in"}),
		canSignUp: Account.isAllowed(User.RESOURCE_NAME, ["read"], {controller:"account",action:"sign-up"})
	};
}]);