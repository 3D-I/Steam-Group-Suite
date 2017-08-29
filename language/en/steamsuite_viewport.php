<?php
/**
 *
 * Steam suite. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di, http://3di.space/32/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	// Statistics
	'STEAM_LIST_USERS'	=> array(
		0	=> 'There are no users in the Group',
		1	=> 'There is <b>%d</b> user in the Group',
		2	=> 'There are a total of <b>%d</b> users in the Group',
	),
	'STEAM_LIST_ONLINE_INGAME'		=>	'Of those, there are <b>%1d</b> online and <b>%2d</b> in game',
	'STEAM_LAST_SUCCESS'			=>	'Last successful refresh',
	'STEAM_GROUP_TITLE'				=>	'Steam Group',
	'STEAM_GROUP_DESCRIPTION'		=>	'Description',

	// Viewport
	'STEAM_STATE'					=>	'Online',
	'STEAM_GAME'					=>	'In game',
	'STEAM_SELECT'					=>	'Select status...',
	'ANY_STATE'						=>	'All',
	'ANY_GAME'						=>	'All',
	'STEAM_IN_GAME'					=>	'In game',
	'STEAM_NOT_IN_GAME'				=>	'Not in game',
	'STEAM_ALL_OFFLINE'				=>	'All Offline',
	'STEAM_ALL_ONLINE'				=>	'All Online',
	'STEAM_SHOWING'					=>	'Showing ',
	'STEAM_VP_COLLAPSE_TITLE'		=>	'Show/Hide',
	'STEAM_VP_GROW_TITLE'			=>	'Grow',
	'STEAM_VP_SHRINK_TITLE'			=>	'Shrink',
	'STEAM_VP_SAVE_TITLE'			=>	'Save current configuration',
	'STEAM_VP_SAVE_CONFIRM'			=>	'You are saving this configuration as the default for this browser. Are you sure that you want to save this configuration?',
	'STEAM_VP_RESET_TITLE'			=>	'Reset configuration to forum defaults',
	'STEAM_VP_RESET_CONFIRM'		=>	'You are removing any saved configuration from this browser, the forum defaults will apply. Are you sure that you want to reset this configuration?',
));
