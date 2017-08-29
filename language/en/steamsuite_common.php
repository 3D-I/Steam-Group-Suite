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
	'STEAM_ONLINE' => array(
		'0'		=>	'Offline',
		'1'		=>	'Online',
		'2'		=>	'Busy',
		'3'		=>	'Away',
		'4'		=>	'Snooze',
		'5'		=>	'Looking to trade',
		'6'		=>	'Looking to play',
	),

	'STEAM_NOT_IN_GAME'		=>	'Not in game',
	'STEAM_GAME_UNKNOWN'	=>	'Game unknown',
	'STEAM_UNKNOWN'			=>	'Private profile',
));
