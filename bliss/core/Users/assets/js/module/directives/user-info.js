app.directive("userInfo", [function() {
	return {
		scope: {
			user: "=userInfo",
			description: "@",
			compact: "=?"
		},
		replace: true,
		template:	'<div class="user-info" ng-class="{compact:compact}">'+
						'<div class="avatar">'+
							'<a href="./{{user.path}}">'+
								'<img bliss-gravatar="user.params.gravatarHash" width="40">'+
							'</a>'+
						'</div>'+
						'<p>'+
							'<a href="./{{user.path}}">{{user.nickname}}</a>'+
							'<br>'+
							'<small class="text-muted">{{description}}</small>'+
						'</p>'+
					'</div>',
		link: function($scope) {
			$scope.compact = $scope.compact === true ? true : false;
		}
	};
}]);