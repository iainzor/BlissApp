app.config(["$routeProvider", function($routeProvider) {
	$routeProvider.when("/account", {
		templateUrl: "./account/index.html",
		controller: "account.IndexCtrl"
	}).when("/account/profile", {
		templateUrl: "./account/profile.html",
		controller: "account.ProfileCtrl"
	}).when("/account/password", {
		templateUrl: "./account/password.html",
		controller: "account.PasswordCtrl"
	}).when("/account/settings", {
		templateUrl: "./account/settings.html",
		controller: "account.SettingsCtrl",
		resolve: {
			regions: ["system.Region", function(Region) {
				return Region.query().$promise;
			}]
		}
	}).when("/account/sign-in", {
		templateUrl: "./account/sign-in.html",
		controller: "account.SignInCtrl"
	}).when("/account/sign-up", {
		templateUrl: "./account/sign-up.html",
		controller: "account.SignUpCtrl"
	});
}]);