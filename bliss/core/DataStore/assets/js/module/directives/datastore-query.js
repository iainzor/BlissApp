app.directive("datastoreQuery", ["$location", "$cacheFactory", "datastore.Query", function($location, $cache, Query) {
	return {
		restrict: "EA",
		replace: true,
		templateUrl: "./datastore/directives/datastore-query.html",
		link: function($scope, $element, $attrs) {
			$scope.resourceName = $attrs.resourceName || $attrs.resourcename;
			$scope.filterOpen = false;
			$scope.cache = $cache.get($scope.resourceName) || $cache($scope.resourceName);
			$scope.params = $scope.cache.get("params") || $location.search();
			
			$scope.submit();
		},
		controller: ["$scope", function($scope) {
			$scope.submit = function() {
				$scope.cache.put("params", $scope.params);
				$scope.isLoading = true;
				
				var params = angular.extend({
					resourceName: $scope.resourceName
				}, $scope.params);
				
				return Query.get(params, function(response) {
					$scope.results = response.results;
					$scope.query = response.query;
					$scope.filter = $scope.filter || response.filter;
					$scope.isLoading = false;
				}, function(response) {
					$scope.isLoading = false;
					console.error(response.data);
				}).$promise;
			};
			
			$scope.$on("DataStoreFilterApplied", function(e, fields) {
				$scope.params = {};
				
				angular.forEach(fields, function(field) {
					if (field.value) {
						$scope.params[field.alias] = field.value;
					}
				});
				
				$scope.submit();
			});
		}]
	};
}]);