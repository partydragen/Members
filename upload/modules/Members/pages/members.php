<?php
/*
 *	Made by Partydragen And Samerton
 *  https://github.com/partydragen/Members/
 *  NamelessMC version 2.0.0-pr3
 *
 *  License: MIT
 */

// Always define page name
define('PAGE', 'members');
?>
<!DOCTYPE html>
<html lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>">
  <head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

    <!-- Site Properties -->
	<?php 
	$title = $members_language->get('members', 'members');
	require('core/templates/header.php'); 
	?>
  
  </head>
  <body>
    <?php
	require('core/templates/navbar.php');
	require('core/templates/footer.php');

	$users = $queries->orderAll("users", "USERNAME", "ASC");
	$groups = $queries->getAll("groups", array("id", "<>", 0));
	$user_array = array();

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
		'MEMBERS' => $user_array
	));
	
	$smarty->display('custom/templates/' . TEMPLATE . '/members.tpl');

    require('core/templates/scripts.php'); ?>
	
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

  </body>
</html>
