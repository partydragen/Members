<?php 
/*
 *	Made by Partydragen And Samerton
 *  https://github.com/partydragen/Members/
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr6
 *
 *  License: MIT
 *
 *  Members module info file
 */

class Members_Module extends Module {
	private $_members_language;
	
	public function __construct($members_language, $pages, $navigation, $cache){
		$this->_members_language = $members_language;
		
		$name = 'Members';
		$author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>, <a href="https://samerton.me" target="_blank" rel="nofollow noopener">Samerton</a>';
		$module_version = '2.0.0-pr6';
		$nameless_version = '2.0.0-pr6';
		
		parent::__construct($this, $name, $author, $module_version, $nameless_version);
		
		// Define URLs which belong to this module
		$pages->add('Members', '/members', 'pages/members.php', 'members', true);
		$pages->add('Members', '/panel/members', 'pages/panel/members.php');
	}
	
	public function onInstall(){
		// Queries
		$queries = new Queries();
		
		try {
			// Update main admin group permissions
			$group = $queries->getWhere('groups', array('id', '=', 2));
			$group = $group[0];
			
			$group_permissions = json_decode($group->permissions, TRUE);
			$group_permissions['memberslist.edit'] = 1;
			
			$group_permissions = json_encode($group_permissions);
			$queries->update('groups', 2, array('permissions' => $group_permissions));
		} catch(Exception $e){
			// Error
		}
	}

	public function onUninstall(){
		// No actions necessary
	}

	public function onEnable(){
		// No actions necessary
	}

	public function onDisable(){
		// No actions necessary
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){
		// AdminCP
		PermissionHandler::registerPermissions('Members', array(
			'memberslist.edit' => $this->_members_language->get('members', 'members')
		));
		
		// navigation link location
		$cache->setCache('members_module_cache');
		if(!$cache->isCached('link_location')){
			$link_location = 1;
			$cache->store('link_location', 1);
		} else {
			$link_location = $cache->retrieve('link_location');
		}
		
		// Navigation icon
		$cache->setCache('navbar_icons');
		if(!$cache->isCached('members_icon')) {
			$icon = '';
		} else {
			$icon = $cache->retrieve('members_icon');
		}
		
		// Navigation order
		$cache->setCache('navbar_order');
		if(!$cache->isCached('members_order')){
			// Create cache entry now
			$members_order = 3;
			$cache->store('members_order', 3);
		} else {
			$members_order = $cache->retrieve('members_order');
		}
		
		switch($link_location){
			case 1:
				// Navbar
				$navs[0]->add('members', $this->_members_language->get('members', 'members'), URL::build('/members'), 'top', null, $members_order, $icon);
			break;
			case 2:
				// "More" dropdown
				$navs[0]->addItemToDropdown('more_dropdown', 'members', $this->_members_language->get('members', 'members'), URL::build('/members'), 'top', null, $icon, $members_order);
			break;
			case 3:
				// Footer
				$navs[0]->add('members', $this->_members_language->get('members', 'members'), URL::build('/members'), 'footer', null, $members_order, $icon);
			break;
		}

		if(defined('BACK_END')){
			if($user->hasPermission('memberslist.edit')){
				$cache->setCache('panel_sidebar');
				if(!$cache->isCached('members_order')){
					$order = 15;
					$cache->store('members_order', 15);
				} else {
					$order = $cache->retrieve('members_order');
				}

				if(!$cache->isCached('members_icon')){
					$icon = '<i class="nav-icon fas fa-cogs"></i>';
					$cache->store('members_icon', $icon);
				} else {
					$icon = $cache->retrieve('members_icon');
				}
				
				$navs[2]->add('members_divider', mb_strtoupper($this->_members_language->get('members', 'members'), 'UTF-8'), 'divider', 'top', null, $order, '');
				$navs[2]->add('members', $this->_members_language->get('members', 'members'), URL::build('/panel/members'), 'top', null, $order, $icon);
			}
		}
	}
}