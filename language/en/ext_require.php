<?php
/**
 *
 * Steamsuite [English]. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017 - 3Di http://3di.space/32/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */
/**

* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'ERROR_MSG_3111_321_MISTMATCH'	=>	'Minimum 3.1.11 but less than 3.2.0@dev OR greater than 3.2.1 but less than 3.3.0@dev',
	'ERROR_MSG_SIMPLEXML'			=>	'PHP function "simplexml_load_string" not installed',
	'ERROR_MSG_ICONV'				=>	'PHP function "iconv" not installed',
	'ERROR_MSG_CURL'				=>	'PHP function "cUrl" not installed',
));
