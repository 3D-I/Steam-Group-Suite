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
	// Profile
	'STEAM_STATUS_PROFILE'		=>	'Steam status overview',
	'STEAM_PROFILE_INFO'		=>	'Steam Badge',
	'STEAM_PROFILE_NAME'		=>	'Persona Name',
	'STEAM_PROFILE_STATE'		=>	'Online Status',
	'STEAM_PROFILE_INGAME'		=>	'In-Game Status',

	// Miniprofile
	'STEAM_PROFILE'				=>	'Steam user',
	'STEAM_PROFILE_PRIVATE'		=>	'This Steam personality is private',
));
