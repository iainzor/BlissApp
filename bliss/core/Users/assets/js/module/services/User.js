app.factory("users.User", ["$resource", "$cacheFactory", function($resource, $cacheFactory) {
	var c = $cacheFactory.get("users.User") || $cacheFactory("users.User");
	var r = $resource("./users/:path/:action.json", {}, {
		query: {
			method: "GET",
			isArray: true,
			cache: c
		}
	});
	
	r.cache = c;
	
	r.RESOURCE_NAME = "users";
	
	return r;
}]);