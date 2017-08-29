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
	// fieldset
	'UCP_STEAM_LOCAL_CONFIG'				=> 'Local forum configuration',
	'UCP_STEAM_PROFILE_VISIBLE'				=> 'Steamsuite personality visible',
	'UCP_STEAM_PROFILE_VISIBLE_EXPLAIN'		=> 'Make your Steam personality (name and avatar) visible to others through your forum profile. <em>This does not alter your Steam profile privacy settings, that will be respected.</em>',
	'UCP_STEAM_INFORMATION'					=> 'Information provided by Steam Server',
	'UCP_STEAM_ID'							=> 'Steam ID 64',
	'UCP_STEAM_ID_EXPLAIN'					=> 'Your personal SteamID64.<br><em>This has been received from Steam. Please, confirm it.</em>',
	'UCP_STEAM_NAME'						=> 'Steam Name / Personaname',
	'UCP_STEAM_AVATAR_PROFILE'				=> 'Steam Avatar & Profile URL',
	'UCP_STEAM_AVATAR_PROFILE_EXPLAIN'		=> 'Click on the avatar to take you to your Steam profile.',
	'UCP_SIGN_IN_THROUGH_STEAM'				=> 'Sign in Through Steam',
	'UCP_SIGN_IN_THROUGH_STEAM_EXPLAIN'		=> 'You do not have configured a Steam account.<br><em> If you want to configure one, please log on to Steam with your credentials.</em>',
	'UCP_SIGN_IN_THROUGH_STEAM_HELPLINE'	=> '<strong>By signing into this Board through Steam:</strong><br><br><strong>A</strong> - We will be able to map your forum profile to your Steam identity within the Steam Community, and validate that you belong to this Steam Group.<br><strong>B</strong> - A unique numeric identifier (Steam ID 64) will be shared with this forum, rather than your Steam login credentials, that will remain private.<br><strong>C</strong> - It is your choice to allow other forum users to know your Steam personality or not. If you decide not to show it, others will only know that you are part of the Steam Group.<br><strong>D</strong> - You may want to logoff from Steam after that. If you so wish, click on your Steam avatar and you will be redirect to Steam, there you can logout.',

	// Validation errors
	'STEAMSUITE_USER_GROUP_ID_NOT_IN_GROUP'	=> '<strong>Steam User ID invalid</strong>, it does not belong to the Group.',
	'STEAMSUITE_USER_GROUP_ID_TAKEN'		=> '<strong>Steam User ID invalid</strong>, it has been already taken.',

	// OpenID errors
	'OPENID_AUTH_CANCELED'					=> 'Steam Authentication operation cancelled.',
	'OPENID_AUTH_FAILED'					=> 'Steam Authentication failed.',
	'OPENID_COMMUNICATION_FAILED'			=> 'Steam Authentication communication failed.',

	// Confirmation and success
	'UCP_CONFIRM_DELETE'					=> '<strong>You are about to remove your Steam information from your profile</strong>.  Please confirm the operation to continue.',
	'UCP_STEAM_SAVED'						=> 'Steam settings have been saved successfully!',
	'UCP_STEAM_DELETED'						=> 'Steam settings have been removed successfully!',
));
