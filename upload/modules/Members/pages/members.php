<?php
/*
 *  Made by Partydragen
 *  https://github.com/partydragen/Members/
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 */

// Always define page name
define('PAGE', 'members');
$page_title = $members_language->get('members', 'members');
require_once(ROOT_PATH . '/core/templates/frontend_init.php');

if (rtrim($_GET['route'], '/') == "/members") {
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

    $users = DB::getInstance()->query('SELECT DISTINCT(user_id) AS id FROM nl2_users_groups INNER JOIN nl2_users ON user_id=nl2_users.id WHERE nl2_users_groups.group_id = ?', [$gid[0]])->results();
}

// Retrieve hided groups from cache
$cache->setCache('members_module_cache');
$hided_groups = [];
if ($cache->isCached('hided_groups')) {
    $hided_groups = $cache->retrieve('hided_groups');
    $hided_groups = is_array($hided_groups) ? $hided_groups : [];
}

$group_array = [];
$groups = DB::getInstance()->orderAll('groups', '`order`', 'ASC')->results();   
foreach ($groups as $group) {
    if (in_array($group->id, $hided_groups)) {
        continue;
    }

    $group_array[] = [
        'name' => $group->name,
        'link' => URL::build('/members/' . Output::getClean($group->id) .'-'. str_replace('/', '-', Output::getClean($group->name))),
    ];
}

$user_array = [];
foreach ($users as $item) {
    $target_user = new User($item->id);

    $user_array[] = [
        'username' => $target_user->getDisplayname(true),
        'nickname' => $target_user->getDisplayname(),
        'avatar' => $target_user->getAvatar(),
        'groups' => $target_user->getAllGroupHtml(),
        'style' => $target_user->getGroupClass(),
        'joined' => date(DATE_FORMAT, $target_user->data()->joined),
        'profile' => $target_user->getProfileURL()
    ];
}

// Language values
$smarty->assign([
    'MEMBERS_TITLE' => $members_language->get('members', 'members'),
    'USERNAME' => $members_language->get('members', 'username'),
    'GROUP' => $members_language->get('members', 'group'),
    'CREATED' => $members_language->get('members', 'created'),
    'DISPLAY_ALL' => $members_language->get('members', 'all'),
    'MEMBERS' => $user_array,
    'GROUPS' => $group_array,
    'ALL_LINK' => URL::build('/members')
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

$template->onPageLoad();

if (TEMPLATE == 'DefaultRevamp') {
    // Default Revamp Template
    $template->addJSFiles([
        'https://code.jquery.com/jquery-3.3.1.js' => [],
        'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js' => [],
        'https://cdn.datatables.net/1.10.19/js/dataTables.semanticui.min.js' => [],
        'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.3.1/semantic.min.js' => []
    ]);

    
    $template->addJSScript('
        $(document).ready(function() {
        $(\'#example\').DataTable();
        } );
    ');
}

$smarty->assign('WIDGETS_LEFT', $widgets->getWidgets('left'));
$smarty->assign('WIDGETS_RIGHT', $widgets->getWidgets('right'));

require(ROOT_PATH . '/core/templates/navbar.php');
require(ROOT_PATH . '/core/templates/footer.php');

// Display template
$template->displayTemplate('members.tpl', $smarty);