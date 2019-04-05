<?php
/*
 *	Made by Partydragen And Samerton
 *  https://github.com/partydragen/Members/
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Panel API page
 */

// Can the user view the panel?
if($user->isLoggedIn()){
	if(!$user->canViewACP()){
		// No
		Redirect::to(URL::build('/'));
		die();
	}
	if(!$user->isAdmLoggedIn()){
		// Needs to authenticate
		Redirect::to(URL::build('/panel/auth'));
		die();
	} else {
		if(!$user->hasPermission('memberslist.edit')){
			require_once(ROOT_PATH . '/404.php');
			die();
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}

define('PAGE', 'panel');
define('PARENT_PAGE', 'members');
define('PANEL_PAGE', 'members');
$page_title = $members_language->get('members', 'members');
require_once(ROOT_PATH . '/core/templates/backend_init.php');

// Deal with input
if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$validate = new Validate();
		$validation = $validate->check($_POST, array(
			'link_location' => array(
				'required' => true
			),
			'icon' => array(
				'max' => 64
			)
		));
		
		if($validation->passed()){			
			try {
                // Get link location
                if(isset($_POST['link_location'])){
                    switch($_POST['link_location']){
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                            $location = $_POST['link_location'];
                            break;
                        default:
                            $location = 1;
                    }
                } else
                    $location = 1;

                // Update Link location cache
                $cache->setCache('members_module_cache');
                $cache->store('link_location', $location);
				
				// Update Icon cache
				$cache->setCache('navbar_icons');
				$cache->store('members_icon', Input::get('icon'));

			} catch(Exception $e){
				die($e->getMessage());
			}
		}
	} else {
		$error = $language->get('general', 'invalid_token');
	}
}
// Retrive link_location from cache
$cache->setCache('members_module_cache');
$link_location = $cache->retrieve('link_location');

// Retrive Icon from cache
$cache->setCache('navbar_icons');
$icon = $cache->retrieve('members_icon');

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

$smarty->assign(array(
	'PARENT_PAGE' => PARENT_PAGE,
	'DASHBOARD' => $language->get('admin', 'dashboard'),
	'MEMBERS' => $members_language->get('members', 'members'),
	'LINK_LOCATION' => $members_language->get('members', 'link_location'),
	'LINK_LOCATION_VALUE' => $link_location,
	'LINK_NAVBAR' => $language->get('admin', 'page_link_navbar'),
	'LINK_MORE' => $language->get('admin', 'page_link_more'),
	'LINK_FOOTER' => $language->get('admin', 'page_link_footer'),
	'LINK_NONE' => $language->get('admin', 'page_link_none'),
	'ICON' => $members_language->get('members', 'icon'),
	'ICON_EXAMPLE' => htmlspecialchars($members_language->get('members', 'icon_example')),
	'ICON_VALUE' => Output::getClean(htmlspecialchars_decode($icon)),
	'TOKEN' => Token::get(),
	'SUBMIT' => $language->get('general', 'submit')
));

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

require(ROOT_PATH . '/core/templates/panel_navbar.php');

// Display template
$template->displayTemplate('members/members.tpl', $smarty);