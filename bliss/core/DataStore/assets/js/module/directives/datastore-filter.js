app.directive("datastoreFilter", ["$location", "$timeout", function($location, $timeout) {
	return {
		restrict: "EA",
		replace: true,
		templateUrl: "./datastore/directives/datastore-filter.html",
		scope: {
			filter: "=",
			src: "@"
		},
		controller: ["$rootScope", "$scope", function($root, $scope) {
			$scope.fields = [];
			
			/**
			 * Add a field to the filter
			 * 
			 * @param Object field
			 */
			$scope.addField = function(field) {
				var index = $scope.fields.indexOf(field);
				if (index === -1) {
					$scope.fields.push(field);
				}
			};
			
			/**
			 * Apply the filter and load the results
			 */
			$scope.apply = function() {
				$location.search({});
				angular.forEach($scope.fields, function(field) {
					if (field.value) {
						$location.search(field.alias, field.value);
					}
				});
			};
			
			$scope.$watch("filter", function() {
				if ($scope.filter && $scope.fields.length === 0) {
					var fields = [];
					
					angular.forEach($scope.filter.fields, function(field) {
						if (field.value) {
							fields.push(field);
						}
					});
					
					$scope.fields = fields;
				}
			}, true);
			
			
			var timer;
			$scope.$watch("fields", function() {
				if (timer) {
					$timeout.cancel(timer);
				}
				timer = $timeout(function() {
					$root.$broadcast("DataStoreFilterApplied", $scope.fields);
				}, 250);
			}, true);
		}]
	};
}]);