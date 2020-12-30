<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Members/
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr9
 *
 *  License: MIT
 */

// Always define page name
define('PAGE', 'members');
$page_title = $members_language->get('members', 'members');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

if(rtrim($_GET['route'], '/') == "/members") {
    // Load all users
    $users = DB::getInstance()->query('SELECT id FROM nl2_users')->results();
} else {
    // Sort by groups
    $gid = explode('/', $route);
    $gid = $gid[count($gid) - 1];
    
    if (!strlen($gid)) {
        require_once(ROOT_PATH . '/404.php');
        die();
    }
    
    $gid = explode('-', $gid);
    if (!is_numeric($gid[0])) {
        require_once(ROOT_PATH . '/404.php');
        die();
    }

    $users = DB::getInstance()->query('SELECT DISTINCT(user_id) AS id FROM nl2_users_groups INNER JOIN nl2_users ON user_id=nl2_users.id WHERE nl2_users_groups.group_id = ?', array($gid[0]))->results();
}

// Retrieve hided groups from cache
$cache->setCache('members_module_cache');
$hided_groups = array();
if($cache->isCached('hided_groups')) {
    $hided_groups = $cache->retrieve('hided_groups');
}

$groups = $queries->orderAll('groups', '`order`', 'ASC');   
foreach($groups as $group){
    if(in_array($group->id, $hided_groups)) {
        continue;
    }
    
    $group_array[] = array(
        'name' => $group->name,
        'link' => URL::build('/members/' . Output::getClean($group->id) .'-'. Output::getClean($group->name)),
    );
}

$user_array = array();
foreach($users as $item){
    $target_user = new User($item->id);

    $user_array[] = array(
        'username' => $target_user->getDisplayname(true),
        'nickname' => $target_user->getDisplayname(),
        'avatar' => $target_user->getAvatar(),
        'groups' => $target_user->getAllGroups(true),
        'style' => $target_user->getGroupClass(),
        'joined' => date('d M Y', $target_user->data()->joined),
        'profile' => $target_user->getProfileURL()
    );
}
    
// Language values
$smarty->assign(array(
    'MEMBERS_TITLE' => $members_language->get('members', 'members'),
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

if(TEMPLATE != 'DefaultRevamp') {
$template->addCSSFiles(array(
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/custom/panel_templates/Default/assets/css/dataTables.bootstrap4.min.css' => array()
));

$template->addJSFiles(array(
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/core/assets/plugins/dataTables/jquery.dataTables.min.js' => array(),
    (defined('CONFIG_PATH') ? CONFIG_PATH : '') . '/custom/panel_templates/Default/assets/js/dataTables.bootstrap4.min.js' => array()
));

$template->addJSScript('
    $(document).ready(function() {
        $(\'.dataTables-users\').dataTable({
            responsive: true,
            language: {
                "lengthMenu": "' . $language->get('table', 'display_records_per_page') . '",
                "zeroRecords": "' . $language->get('table', 'nothing_found') . '",
                "info": "' . $language->get('table', 'page_x_of_y') . '",
                "infoEmpty": "' . $language->get('table', 'no_records') . '",
                "infoFiltered": "' . $language->get('table', 'filtered') . '",
                "search": "' . $language->get('general', 'search') . ' ",
                "paginate": {
                    "next": "' . $language->get('general', 'next') . '",
                    "previous": "' . $language->get('general', 'previous') . '"
                }
            }
        });
    });
');
} else {
    
    $template->addJSFiles(array(
        'https://code.jquery.com/jquery-3.3.1.js' => array(),
        'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js' => array(),
        'https://cdn.datatables.net/1.10.19/js/dataTables.semanticui.min.js' => array(),
        'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js' => array()
    ));

    
    $template->addJSScript('
        $(document).ready(function() {
        $(\'#example\').DataTable();
        } );
    ');
}

$smarty->assign('WIDGETS', $widgets->getWidgets());

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');
    
// Display template
$template->displayTemplate('members.tpl', $smarty);
