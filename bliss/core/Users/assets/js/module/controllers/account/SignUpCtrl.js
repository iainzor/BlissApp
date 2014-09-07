app.controller("account.SignUpCtrl", ["$scope", "$timeout", "users.Account", function($scope, $timeout, Account) {
	$scope.setSidebarVisible(false);
	$scope.setPageTitle(["Sign Up"]);
	
	$scope.errors = [];
	$scope.user = {};
	$scope.acl = {
		canSignIn: Account.isAllowed("users", ["read"], {controller:"account",action:"sign-in"})
	};
	
	$scope.$watch("user.username", function(username) {
		$scope.user.nickname = username;
	});
   
	/**
	 * Perform actions when the form is submitted
	 */
	$scope.submit = function() {
		$scope.setPageLoading(true);
		$scope.errors = [];
	};
	
	/**
	 * Check if a field has an error
	 * 
	 * @param string field
	 * @returns boolean
	 */
	$scope.hasError = function(field) {
		var hasError = false;
		angular.forEach($scope.errors, function(error) {
			if (error.field === field) {
				hasError = true;
			}
		});
		return hasError;
	};
	
	/**
	 * Get an error message for a field
	 * 
	 * @param string field
	 * @returns string
	 */
	$scope.getError = function(field) {
		var message = null;
		angular.forEach($scope.errors, function(error) {
			if (error.field === field) {
				message = error.message;
			}
		});
		return message;
	};
	
	/**
	 * Handle the results from the form
	 */
	var resultWindow = angular.element(document.getElementById("resultWindow"));
	resultWindow.on("load", function(e) {
		var body = resultWindow[0].contentDocument.body;
		var contents = body.innerHTML;
		
		if (!contents) {
			return;
		}
		
		var response = angular.fromJson(contents);
		
		if (!response.isValid) {
			$scope.$apply(function() {
				$scope.setPageLoading(false);
				$scope.errors = response.errors;
			});
		} else {
			$scope.$apply(function() {
				$scope.user = response.user;
				$timeout(function() {
					var base = document.getElementsByTagName("base")[0];
					window.location.href = base.href;
				}, 1000);
			});
		}
	});
}]);