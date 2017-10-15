<?php 
/*
 *	Made by Partydragen And Samerton
 *  https://github.com/partydragen/Members/
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 *
 *  Members initialisation file
 */

// Ensure module has been installed
$module_installed = $cache->retrieve('module_members');

// Initialise forum language
$members_language = new Language(ROOT_PATH . '/modules/Members/language', LANGUAGE);

// Define URLs which belong to this module
$pages->add('Members', '/members', 'pages/members.php', 'members', true);
$pages->add('Members', '/admin/members', 'pages/admin/members.php');

    $members_order = 3;
// Add link to admin sidebar
if(!isset($admin_sidebar)) $admin_sidebar = array();
$admin_sidebar['members'] = array(
	'title' => $members_language->get('members', 'members'),
	'url' => URL::build('/admin/members')
);

// navigation link location
$cache->setCache('members_module_cache');
if(!$cache->isCached('link_location')){
	$link_location = 1;
	$cache->store('link_location', '1');
} else {
	$link_location = $cache->retrieve('link_location');
}
if(!$cache->isCached('icon')){
	$icon = '';
	$cache->store('icon', '');
} else {
	$icon = htmlspecialchars_decode($cache->retrieve('icon'));
}

switch($link_location){
	case 1:
		// Navbar
		// Check cache for navbar link order
		$cache->setCache('navbar_order');
		if(!$cache->isCached('members_order')){
			// Create cache entry now
			$members_order = 3;
			$cache->store('members_order', 3);
		} else {
			$members_order = $cache->retrieve('members_order');
		}
		$navigation->add('members', $icon . ' ' . $members_language->get('members', 'members'), URL::build('/members'), 'top', null, $members_order);
	break;
	case 2:
		// "More" dropdown
		$navigation->addItemToDropdown('more_dropdown', 'members', $icon . ' ' . $members_language->get('members', 'members'), URL::build('/members'), 'top', null);
	break;
	case 3:
		// Footer
		$navigation->add('members', $icon . ' ' . $members_language->get('members', 'members'), URL::build('/members'), 'footer', null);
	break;
}