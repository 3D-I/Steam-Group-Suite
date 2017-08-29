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
	'ACL_U_ALLOW_STEAMSUITE_VIEW'	=> 'Allow to use the Steamsuite extension',
	'ACL_A_STEAMSUITE_ADMIN'		=> 'Allow administering the Steamsuite extension',
));
