<?php
/*
 *	Made by Partydragen And Samerton
 *  https://github.com/partydragen/Members/
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr5
 *
 *  License: MIT
 */

// Always define page name
define('PAGE', 'members');
$page_title = $members_language->get('members', 'members');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

if(rtrim($_GET['route'], '/') == "/members") {
	$users = $queries->orderAll("users", "USERNAME", "ASC");			
} else {
	$usergroups1 = explode('/', $_GET['route']);
	$usergroups2 = explode('_', $usergroups1[3]);
	$users = $queries->getWhere('users', array('group_id', '=', $usergroups2[1]));
}

$groups = $queries->orderAll('groups', '`order`', 'ASC');
$user_array = array();
	
foreach($groups as $group1){
	$group_array[] = array(
		'groupname' => $group1->name,
		'grouplink' => URL::build('/members/sort/' . Output::getClean($group1->name) .'_'. Output::getClean($group1->id)),
	);
}

foreach($users as $individual){
	if(isset($selected_staff_group)){
		$user_group = $selected_staff_group->group_html;
	} else {
		$user_group = "";
		foreach($groups as $group){
			if($group->id === $individual->group_id){
				$user_group = $group->group_html;
				$style = $group->group_username_css;
				break;
			}
		}
	}
		
	$avatar = $user->getAvatar($individual->id, "../", 35);
		
	$user_array[] = array(
		'username' => Output::getClean($individual->username),
		'nickname' => Output::getClean($individual->nickname),
		'avatar' => $avatar,
		'group' => $user_group,
		'group_colour' => $style,
		'joined' => date('d M Y', $individual->joined),
		'profile' => URL::build('/profile/' . Output::getClean($individual->username))
	);
}
	
// Language values
$smarty->assign(array(
	'USERNAME' => $members_language->get('members', 'username'),
	'GROUP' => $members_language->get('members', 'group'),
	'CREATED' => $members_language->get('members', 'created'),
	'DISPLAY_ALL' => $members_language->get('members', 'all'),
	'MEMBERS' => $user_array,
	'GROUPS' => $group_array,
	'ALL_LINK' => URL::build('/members')
));
	
// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, array($navigation, $cc_nav, $mod_nav), $widgets);

$page_load = microtime(true) - $start;
define('PAGE_LOAD_TIME', str_replace('{x}', round($page_load, 3), $language->get('general', 'page_loaded_in')));

$template->onPageLoad();

$smarty->assign('WIDGETS', $widgets->getWidgets());

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');
	
// Display template
$template->displayTemplate('members.tpl', $smarty);

?>
	
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/dataTables/jquery.dataTables.min.js"></script>
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/dataTables/dataTables.bootstrap4.min.js"></script>

	<script type="text/javascript">
        $(document).ready(function() {
            $('.dataTables-users').dataTable({
                responsive: true,
				language: {
					"lengthMenu": "<?php echo $language->get('table', 'display_records_per_page'); ?>",
					"zeroRecords": "<?php echo $language->get('table', 'nothing_found'); ?>",
					"info": "<?php echo $language->get('table', 'page_x_of_y'); ?>",
					"infoEmpty": "<?php echo $language->get('table', 'no_records'); ?>",
					"infoFiltered": "<?php echo $language->get('table', 'filtered'); ?>",
					"search": "<?php echo $language->get('general', 'search'); ?> "
				}
            });
		});
	</script>