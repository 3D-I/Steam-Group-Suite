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
	'STEAM_AVATAR'				=>	'Steam avatar',

	// Translators please do not change the following line, no need to translate it!
	'STEAMSUITE_CREDIT_LINE'	=>	' | <a href="https://github.com/3D-I/Steam-Group-Suite">Steam Group Suite</a> &copy; 2017, 2018 - 3Di',
));
