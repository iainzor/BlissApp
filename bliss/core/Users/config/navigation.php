<?php
use Bliss\Navigation\Page\Page,
	Acl\Acl;

$user = \Users\Module::session()->getUser();

$nav = array(
	array(
		"title" => "Users",
		"path" => "users",
		"pages" => array()
	)
);

if ($user->isAllowed(\Users\Module::RESOURCE_NAME, Acl::CREATE)) {
	$nav[0]["pages"] = array_merge($nav[0]["pages"], array(
		array(
			"title" => "All Users",
			"path" => "users"
		),
		array(
			"type" => Page::TYPE_DIVIDER
		),
		array(
			"title" => "New User",
			"path" => "users/new"
		)
	));
}

return $nav;