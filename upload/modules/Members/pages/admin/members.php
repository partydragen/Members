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

// Can the user view the AdminCP?
if($user->isLoggedIn()){
	if(!$user->canViewACP()){
		// No
		Redirect::to(URL::build('/'));
		die();
	} else {
		// Check the user has re-authenticated
		if(!$user->isAdmLoggedIn()){
			// They haven't, do so now
			Redirect::to(URL::build('/admin/auth'));
			die();
		}
	}
} else {
	// Not logged in
	Redirect::to(URL::build('/login'));
	die();
}
$page = 'admin';
$admin_page = 'members';

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

                // Update cache
                $cache->setCache('members_module_cache');
                $cache->store('link_location', $location);
				
                // Update cache
                $cache->setCache('members_module_cache');
                $cache->store('icon', Output::getClean(Input::get('icon')));

			} catch(Exception $e){
				die($e->getMessage());
			}
		}
	} else {
		echo '<div class="alert alert-warning">' . $admin_language['invalid_token'] . '</div>';
	}
}
$cache->setCache('members_module_cache');
$link_location = $cache->retrieve('link_location');
$icon = $cache->retrieve('icon');
?>
<!DOCTYPE html>
<html lang="<?php echo (defined('HTML_LANG') ? HTML_LANG : 'en'); ?>">
  <head>
    <!-- Standard Meta -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	
	<?php 
	$title = $language->get('admin', 'admin_cp');
	require('core/templates/admin_header.php'); 
	?>
  
	<!-- Custom style -->
	<style>
	textarea {
		resize: none;
	}
	</style>
	
	<link rel="stylesheet" href="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.css">
  
  </head>

  <body>
    <?php require('modules/Core/pages/admin/navbar.php'); ?>
    <div class="container">
	  <div class="row">
		<div class="col-md-3">
		  <?php require('modules/Core/pages/admin/sidebar.php'); ?>
		</div>
		<div class="col-md-9">
		  <div class="card">
		    <div class="card-block">
				<h3 style="display:inline;"><?php echo $members_language->get('members', 'members'); ?></h3>
				<form action="" method="post">
				  <div class="form-group">
					<label for="link_location"><?php echo $members_language->get('members', 'link_location'); ?></label>
					<select class="form-control" id="link_location" name="link_location">
					  <option value="1"<?php if($link_location == 1) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_navbar'); ?></option>
					  <option value="2"<?php if($link_location == 2) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_more'); ?></option>
					  <option value="3"<?php if($link_location == 3) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_footer'); ?></option>
					  <option value="4"<?php if($link_location == 4) echo ' selected'; ?>><?php echo $language->get('admin', 'page_link_none'); ?></option>
					</select>
				  </div>
				  <div class="form-group">
					<label for="inputIcon"><?php echo $members_language->get('members', 'icon'); ?></label>
					<input type="text" class="form-control" name="icon" id="inputIcon" placeholder="<?php echo htmlspecialchars($members_language->get('members', 'icon_example')); ?>" value="<?php echo Output::getClean(htmlspecialchars_decode($icon)); ?>">
				  </div>
				  <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
				  <input type="submit" value="<?php echo $language->get('general', 'submit'); ?>" class="btn btn-primary">
				</form>
			</div>
		  </div>
		</div>
      </div>
    </div>
	<?php require('modules/Core/pages/admin/footer.php'); ?>

    <?php require('modules/Core/pages/admin/scripts.php'); ?>
	
	<script src="<?php if(defined('CONFIG_PATH')) echo CONFIG_PATH . '/'; else echo '/'; ?>core/assets/plugins/switchery/switchery.min.js"></script>
  </body>
</html>
