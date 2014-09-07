app.service("datastore.Query", ["bliss.$cacheResource", function($resource) {
	var r = $resource("./datastore/query/:resourceName.json", {
		resourceName: "@resourceName"
	});
	
	return r;
}]);