<?php 
/*
 *	Made by Partydragen And Samerton
 *  https://github.com/partydragen/Members/
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0
 *
 *  License: MIT
 *
 *  Members module info file
 */

class Members_Module extends Module {
	private $_members_language;

	public function __construct($members_language, $pages, $navigation, $cache) {
		$this->_members_language = $members_language;

		$name = 'Members';
		$author = '<a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>, <a href="https://samerton.me" target="_blank" rel="nofollow noopener">Samerton</a>';
		$module_version = '2.3.4';
		$nameless_version = '2.1.0';

		parent::__construct($this, $name, $author, $module_version, $nameless_version);

		// Define URLs which belong to this module
		$pages->add('Members', '/members', 'pages/members.php', 'members', true);
		$pages->add('Members', '/panel/members', 'pages/panel/members.php');

		// Check if module version changed
		$cache->setCache('members_module_cache');
		if (!$cache->isCached('module_version')) {
			$cache->store('module_version', $module_version);
		} else {
			if ($module_version != $cache->retrieve('module_version')) {
				// Version have changed, Perform actions
				$cache->store('module_version', $module_version);
				
                if ($cache->isCached('update_check')) {
                    $cache->erase('update_check');
                }
			}
		}
	}

	public function onInstall() {
		try {
			// Update main admin group permissions
			$group = DB::getInstance()->get('groups', ['id', '=', 2])->results();
			$group = $group[0];

			$group_permissions = json_decode($group->permissions, TRUE);
			$group_permissions['memberslist.edit'] = 1;

			$group_permissions = json_encode($group_permissions);
			DB::getInstance()->update('groups', 2, ['permissions' => $group_permissions]);
		} catch (Exception $e) {
			// Error
		}
	}

	public function onUninstall() {
		// No actions necessary
	}

	public function onEnable() {
		// No actions necessary
	}

	public function onDisable() {
		// No actions necessary
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {
		// AdminCP
		PermissionHandler::registerPermissions('Members', [
			'memberslist.edit' => $this->_members_language->get('members', 'members')
		]);

		// navigation link location
		$cache->setCache('members_module_cache');
		if (!$cache->isCached('link_location')) {
			$link_location = 1;
			$cache->store('link_location', 1);
		} else {
			$link_location = $cache->retrieve('link_location');
		}

		// Navigation icon
		$cache->setCache('navbar_icons');
		if (!$cache->isCached('members_icon')) {
			$icon = '';
		} else {
			$icon = $cache->retrieve('members_icon');
		}

		// Navigation order
		$cache->setCache('navbar_order');
		if (!$cache->isCached('members_order')) {
			// Create cache entry now
			$members_order = 3;
			$cache->store('members_order', 3);
		} else {
			$members_order = $cache->retrieve('members_order');
		}

		switch ($link_location) {
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

		if (defined('BACK_END')) {
			if ($user->hasPermission('memberslist.edit')) {
				$cache->setCache('panel_sidebar');
				if (!$cache->isCached('members_order')) {
					$order = 15;
					$cache->store('members_order', 15);
				} else {
					$order = $cache->retrieve('members_order');
				}

				if (!$cache->isCached('members_icon')) {
					$icon = '<i class="nav-icon fas fa-cogs"></i>';
					$cache->store('members_icon', $icon);
				} else {
					$icon = $cache->retrieve('members_icon');
				}

				$navs[2]->add('members_divider', mb_strtoupper($this->_members_language->get('members', 'members'), 'UTF-8'), 'divider', 'top', null, $order, '');
				$navs[2]->add('members', $this->_members_language->get('members', 'members'), URL::build('/panel/members'), 'top', null, $order + 0.1, $icon);
			}
		}

		// Check for module updates
        if (isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')) {
            if (rtrim($_GET['route'], '/') == '/panel/members' || rtrim($_GET['route'], '/') == '/members') {

                $cache->setCache('members_module_cache');
                if ($cache->isCached('update_check')) {
                    $update_check = $cache->retrieve('update_check');
                } else {
					require_once(ROOT_PATH . '/modules/Members/classes/Members.php');
                    $update_check = Members::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if (!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)) {  
                    $smarty->assign([
                        'NEW_UPDATE' => (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->_members_language->get('members', 'new_urgent_update_available_x', ['module' => $this->getName()]) : $this->_members_language->get('members', 'new_update_available_x', ['module' => $this->getName()]),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => $this->_members_language->get('members', 'current_version_x', ['version' => Output::getClean($this->getVersion())]),
                        'NEW_VERSION' => $this->_members_language->get('members', 'new_version_x', ['new_version' => Output::getClean($update_check->new_version)]),
                        'NAMELESS_UPDATE' => $this->_members_language->get('members', 'view_resource'),
                        'NAMELESS_UPDATE_LINK' => Output::getClean($update_check->link)
                    ]);
                }
            }
        }
	}

    public function getDebugInfo(): array {
        return [];
    }
}
