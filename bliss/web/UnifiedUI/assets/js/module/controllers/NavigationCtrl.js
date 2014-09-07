app.controller("unified-ui.NavigationCtrl", ["$scope", "$location", function($scope, $location) {
	
	/**
	 * Check if a page is active
	 * 
	 * @param Object page
	 * @returns boolean
	 */
	$scope.isActive = function(page) {
		var regex = new RegExp("^/"+ page.path);
		
		return $location.path().match(regex);
	};
}]);