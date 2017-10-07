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

// Add link to navbar
$cache->setCache('navbar_order');
if(!$cache->isCached('members_order')){
    // Create cache entry now
    $members_order = 3;
    $cache->store('members_order', 3);
} else {
    $members_order = $cache->retrieve('members_order');
}
$navigation->add('members', $members_language->get('members', 'members'), URL::build('/members'), 'top', null, $members_order);