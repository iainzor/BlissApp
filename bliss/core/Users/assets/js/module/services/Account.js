app.factory("users.Account", ["bliss.$cacheResource", function($resource) {
	var r = $resource("./account/:path.json", {});
	
	r.account = {};
	
	/**
	 * Check if one or more actions are allowed for a resource
	 * 
	 * @param String resourceName
	 * @param Array actions
	 * @param Object params
	 * @returns boolean
	 */
	r.isAllowed = function(resourceName, actions, params) {
		if (!resourceName) {
			throw new Error("No resource name was provided");
		}
		if (!angular.isArray(actions)) {
			throw new Error("actions must be an Array");
		}
		
		var acl = r.account.acl;
		
		if (!acl) {
			return false;
		}
		
		var isAllowed = acl.allowByDefault;
		
		for (var action, _isAllowed, i = 0; i < actions.length; i++) {
			_isAllowed = false;
			action = actions[i];
			
			for (var resource, j = 0; j < acl.resources.length; j++) {
				resource = acl.resources[j];
				
				if (resource.name === resourceName) {
					_isAllowed = _checkPermissions(acl, resource.permissions, action, params);
					return _isAllowed;
				}
			}
			
			_isAllowed = _defaultPermission(acl, action, params); //acl.defaultPermissions[action] || acl.allowByDefault;
			if (_isAllowed) {
				return true;
			}
		}
		
		return isAllowed;
	};
	
	/**
	 * Check if an action is allowed in any of the permissions
	 * 
	 * @param Object acl
	 * @param Array permissions
	 * @param string action
	 * @param Object params
	 * @returns boolean
	 */
	var _checkPermissions = function(acl, permissions, action, params) {
		var _isAllowed = _defaultPermission(acl, action, params);
		
		angular.forEach(permissions, function(permission) {
			if (permission.action === action && _paramsMatch(permission.params, params)) {
				_isAllowed = permission.isAllowed;
			}
		});
		
		return _isAllowed;
	};
	
	/**
	 * Check the default permission for an action
	 * 
	 * @param Object acl
	 * @param String action
	 * @param Object params
	 * @returns boolean
	 */
	var _defaultPermission = function(acl, action, params) {
		var isAllowed = acl.allowByDefault;
		
		angular.forEach(acl.defaultPermissions, function(perm) {
			if (perm.action === action && _paramsMatch(perm.params, params)) {
				isAllowed = perm.isAllowed;
			}
		});
		
		//console.log(isAllowed);
		
		return isAllowed;
	};
	
	/**
	 * Check if paramsB matches paramsA
	 * 
	 * @param Object paramsA
	 * @param Object paramsB
	 * @returns boolean
	 */
	var _paramsMatch = function(paramsA, paramsB) {
		var count = 0;
		var found = 0;

		angular.forEach(paramsA, function(value, name) {
			count += 1;

			if (paramsB[name] === value) {
				found += 1;
			}
		});

		return count === found;
	};
	
	return r;
}]);