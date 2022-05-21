<?php 
/*
 *	Made by Partydragen And Samerton
 *  https://github.com/partydragen/Members/
 *  https://partydragen.com/
 *  NamelessMC version 2.0.0-pr13
 *
 *  License: MIT
 *
 *  Members initialisation file
 */

// Initialise forum language
$members_language = new Language(ROOT_PATH . '/modules/Members/language', LANGUAGE);

// Initialise module
require_once(ROOT_PATH . '/modules/Members/module.php');
$module = new Members_Module($members_language, $pages, $navigation, $cache);