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
	// Titles and ACP menus
	'ACP_STEAM_TITLE'							=>	'Steamsuite',
	'ACP_STEAM_SETTINGS'						=>	'Core Settings',
	'ACP_STEAM_CONFIG_SETTINGS'					=>	'Configuration Settings',
	'ACP_STEAM_STYLE_SETTINGS'					=>	'Style Settings',
	'ACP_STEAM_SETTINGS_TITLE'					=>	'Steam Group Suite Settings',

	// ACP error logs
	'STEAMSUITE_LOG_HTTP_ZERO_ERROR'			=>	'<strong>Quering the Steam Web API provider failed, http = 0.</strong>',
	'STEAMSUITE_LOG_HTTP_404_ERROR'				=>	'<strong>Quering the Steam Web API provider gave a 404 not found error.</strong>',
	'STEAMSUITE_LOG_HTTP_NO_AUTH'				=>	'<strong>Quering the Steam Web API provider gave a 403 authorization error.</strong>',
	'STEAMSUITE_LOG_CURL_COMM_ERROR'			=>	'<strong>Quering the Steam Web API provider ocurrred an undetermined communication error.</strong>',
	'STEAMSUITE_LOG_CURL_ERROR'					=>	'The PHP function <strong>cUrl()</strong> seems disabled in your server. Ask for support at your hoster.',
	'STEAMSUITE_LOG_ICONV_ERROR'				=>	'The PHP function <strong>iconv()</strong> seems disabled in your server. Ask for support at your hoster.',
	'STEAMSUITE_LOG_SIMPLEXMLLS_ERROR'			=>	'The PHP function <strong>simplexml_load_string()</strong> seems disabled in your server. Ask for support at your hoster.',
	'STEAMSUITE_LOG_CURL_COMM_OK'				=>	'The communication with the Steam Web API provider has been restablished.',
	'STEAMSUITE_LOG_CURL_OK'					=>	'The PHP function <strong>cUrl()</strong> has been reenabled.',
	'STEAMSUITE_LOG_ICONV_OK'					=>	'The PHP function <strong>iconv()</strong> has been reenabled.',
	'STEAMSUITE_LOG_SIMPLEXMLLS_OK'				=>	'The PHP function <strong>simplexml_load_string()</strong> has been reenabled.',

	'STEAMSUITE_LOG_STEAMID_UPDATED'			=>	'<strong>User Steam id updated for “%1$s”</strong><br />»from “%2$s” to “%3$s”',
	'STEAMSUITE_LOG_STEAMID_UPDATED_USER'		=>	'<strong>Steam id updated</strong><br />» from “%1$s” to “%2$s”',
	'STEAMSUITE_LOG_STEAMID_ADDED_USER'			=>	'<strong>Steam id added</strong><br />» “%s”',
	'STEAMSUITE_LOG_STEAMID_DELETED_USER'		=>	'<strong>Steam id deleted</strong><br />» “%s”',
	'STEAMSUITE_LOG_SETTINGS_SAVED'				=>	'<strong>Steam Suite core settings saved.</strong>',
	'STEAMSUITE_LOG_CONFIG_SAVED'				=>	'Steam Suite general configuration saved.',
	'STEAMSUITE_LOG_STYLE_SAVED'				=>	'Steam Suite style settings saved.',
));
