app.controller("account.SignInCtrl", ["$scope", "$timeout", "users.Account", function($scope, $timeout, Account) {
	$scope.setSidebarVisible(false);
	$scope.setPageTitle(["Sign In"]);
	
	$scope.error = false;
	$scope.user = false;
	$scope.acl = {
		canSignUp: Account.isAllowed("users", ["read"], {controller:"account",action:"sign-up"})
	};
	
	var resultWindow = angular.element(document.getElementById("resultWindow"));
	resultWindow.on("load", function(e) {
		var body = resultWindow[0].contentDocument.body;
		var contents = body.innerHTML;
		
		console.log(body, contents);
		
		if (!contents) {
			return;
		}
		
		var response = angular.fromJson(contents);
		
		if (response.result === "error") {
			$scope.$apply(function() {
				$scope.setPageLoading(false);
				$scope.error = response;
			});
		} else {
			$scope.$apply(function() {
				$scope.user = response.user;
			});
			$timeout(function() {
				var base = document.getElementsByTagName("base")[0];
				window.location.href = base.href;
			}, 1000);
		}
	});
	
	/**
	 * Perform actions when the form is submitted
	 */
	$scope.submit = function() {
		$scope.setPageLoading(true);
		$scope.error = false;
	};
}]);