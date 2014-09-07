app.controller("unifiedUI.ContainerCtrl", ["$scope", function($scope) {
	$scope.sidebarOpen = false;
	
	/**
	 * Toggle the sidebar's visibility
	 */
	$scope.toggleSidebar = function() {
		$scope.sidebarOpen = !$scope.sidebarOpen;
	};
}]);