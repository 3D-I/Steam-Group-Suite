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
	// Legends
	'ACP_STEAM_STATS_API_KEYS'					=>	'Steam API Keys and statistics',
	'ACP_STEAM_CORE_OVERVIEW'					=>	'Overview',
	'ACP_STEAM_SETTINGS_BASIC'					=>	'Core Setup',
	'ACP_STEAM_GROUP_INFORMATION'				=>	'Steam Group information',
	'ACP_STEAM_STATS_LEGEND'					=>	'Refresh statistics',
	'ACP_STEAM_ERRORS'							=>	'Configuration errors',

	'ACP_STEAM_SETTINGS_VIEWPORT_GENERAL'		=>	'Configuration overview',
	'ACP_STEAM_SETTINGS_VIEWPORT'				=>	'Viewport Settings',
	'ACP_STEAM_TEMPLATE_LOCATIONS'				=>	'Locations',
	'ACP_STEAM_SETTINGS_VIEWPORT_STYLING'		=>	'Styling',

	'ACP_STEAM_SETTINGS_PROFILES'				=>	'Profiles Settings',
	'ACP_STEAM_SETTINGS_MINIPROFILE'			=>	'Miniprofile',
	'ACP_STEAM_SETTINGS_PROFILE'				=>	'Profile',

	'ACP_STEAM_SETTINGS_STYLE'					=>	'Style Settings',
	'ACP_STEAM_STYLE_ICONS'						=>	'Icons',
	'ACP_STEAM_STYLE_ICONS_SETTINGS'			=>	'Settings',
	'ACP_STEAM_STYLE_VIEWPORT'					=>	'Viewport Settings',
	'ACP_STEAM_STYLE_PROFILE'					=>	'Profile and Mini-profile Settings',
	'ACP_STEAM_SETTINGS_OTHER'					=>	'Other Settings',
	'ACP_STEAM_TEMPLATE_COLORS'					=>	'Color settings',

	//ACP keys: core
	'STEAMSUITE_API_KEY'						=>	'Steam Web API ID',
	'STEAMSUITE_API_KEY_EXPLAIN'				=>	'This ID allows to access the Steam servers in an automated way.<br><em>Only capital letters and numbers, max 32 characters; leave empty to clear current value, Group ID must be empty as well.</em>',
	'STEAMSUITE_GROUP_ID'						=>	'Steam Group ID',
	'STEAMSUITE_GROUP_ID_EXPLAIN'				=>	'Identifier of the Group that this forum is associated with.<br><em>May be specified as an ID (18 numeric digits) or as the group short name (groupurl); it will be converted to the ID always; leave empty to clear current value.</em>',

	// Group information
	'ACP_STEAM_GROUP_NAME'						=>	'Steam Group Name',
	'ACP_STEAM_GROUP_HEADLINE'					=>	'Steam Group Headline',
	'ACP_STEAM_GROUP_AVATAR_PROFILE'			=>	'Steam Group Avatar & Profile URL',
	'ACP_STEAM_GROUP_AVATAR_PROFILE_EXPLAIN'	=>	'Click on the avatar to take you to the group‘s Steam profile.',
	'ACP_STEAM_GROUP_DESCRIPTION'				=>	'Steam Group Description',
	'ACP_STEAM_GROUP_DESCRIPTION_EXPLAIN'		=>	'Description of the Group, if present',

	// Statistics
	'STEAMSUITE_TTL'							=>	'Steam Information Refresh time',
	'STEAMSUITE_TTL_EXPLAIN'					=>	'Time in minutes to re-fetch all Steam information (refresh).',
	'STEAM_LAST_ATTEMPT'						=>	'Last attempt',
	'STEAM_LAST_SUCCESS'						=>	'Last successful refresh',
	'STEAM_LAST_TIMEGAP'						=>	'Refresh time',
	'STEAM_LAST_TIMEGAP_INFO'					=>	'<em>HH:MM:SS</em>',

	// ACP messages and validation errors
	'ACP_STEAM_SETTINGS_SAVED'					=>	'Settings have been saved successfully!',
	'STEAMSUITE_MISSING_ACP_KEYS'				=>	'Please input both your private <strong>Steam WEB Api Key</strong> and your <strong>Steam Group Id</strong> to continue.<br><em>Without them the extension will not function.</em></strong>',
	'STEAMSUITE_API_KEY_INVALID'				=>	'<strong>Web API Key</strong> invalid. Only capital letters and numbers. Max 32 digits.',
	'STEAMSUITE_GROUP_ID_NOAPIKEY'				=>	'<strong>Group ID without API key</strong>, group ID cannot be verified.',
	'STEAMSUITE_GROUP_ID_INVALID'				=>	'<strong>Group ID invalid</strong>, only numbers. Max 18 digits.',
	'STEAMSUITE_GROUP_ID_NOTFOUND'				=>	'<strong>Group ID invalid</strong>, not found as specified, please verify.',
	'STEAMSUITE_GROUP_ID_CHANGED'				=>	'The Steam Group ID has changed, please confirm that the change is correct.<br><em>Note that changing the Group ID will reset the current Steam database information.</em>',
	'STEAMSUITE_REFRESH_OVERDUE'				=>	'<strong>Refresh time overdue</strong>. For some reason, the Steam information refresh has not been successful. <em>Please, review the Core Steam configuration.</em>',
	'STEAMSUITE_NEVER'							=>	'None yet',
	'STEAMSUITE_OVERDUE'						=>	'Overdue!',

	//ACP keys: settings
	'STEAMSUITE_DEFAULT_CONTENT'				=>	'Viewport default content',
	'STEAMSUITE_DEFAULT_CONTENT_EXPLAIN'		=>	'Steam users shown in viewport by default.',
	'STEAMSUITE_CONTENT_NONE'					=>	'None',
	'STEAMSUITE_CONTENT_INGAME'					=>	'All in Game',
	'STEAMSUITE_CONTENT_ONLINE'					=>	'All Online',
	'STEAMSUITE_CONTENT_ALL'					=>	'All',
	'STEAMSUITE_NUM_USERS_ROWS'					=>	'Viewport rows',
	'STEAMSUITE_NUM_USERS_ROWS_EXPLAIN'			=>	'Number of visible rows.',
	'STEAMSUITE_LIST_ONLINE'					=>	'Users shown',
	'STEAMSUITE_LIST_ONLINE_EXPLAIN'			=>	'Select what users will be shown in the viewport, only online users, or all. <em>"Online" suggested for slow connections.</em>',
	'STEAMSUITE_USERS_ALL'						=>	'All',
	'STEAMSUITE_USERS_ONLINE'					=>	'Online',
	// Default Avatar
	'STEAMSUITE_DEFAULT_AVATAR'					=>	'Default avatar',
	'STEAMSUITE_DEFAULT_AVATAR_EXPLAIN'	=>	'<strong>Steam avatar as user avatar</strong><br>Use the steam avatar as user forum avatar if the user has configured it, and granted public visibility to the steam profile. This will be visible only to users with steam permissions. The size of the steam avatar used is adapted to be the biggest that fits in the forum maximum avatar dimensions.',
	'STEAMSUITE_DEFAULT_AVATAR_REPLACE'			=>	'Always',
	'STEAMSUITE_DEFAULT_AVATAR_REPLACE_EXPLAIN'	=>	'Replace the forum avatar even if this is configured by the user',
	'STEAMSUITE_DEFAULT_AVATAR_DEFAULT'			=>	'As Default',
	'STEAMSUITE_DEFAULT_AVATAR_DEFAULT_EXPLAIN'	=>	'Use Steam avatar only for users that have not specified a forum avatar.',
	'STEAMSUITE_DEFAULT_AVATAR_NEVER'			=>	'Never',
	'STEAMSUITE_DEFAULT_AVATAR_NEVER_EXPLAIN'	=>	'Do not use the Steam avatar as forum avatar in any case',
	'STEAMSUITE_DEFAULT_AVATAR_YES_REPLACE'		=>	'Replace always.',
	'STEAMSUITE_DEFAULT_AVATAR_YES_USE'			=>	'Only use as default avatar.',
	'STEAMSUITE_DEFAULT_AVATAR_NO_THANKS'		=>	'Do not use it.',
	//
	'STEAMSUITE_ALLOW_MINIPROFILE'				=>	'Steam User‘s status',
	'STEAMSUITE_ALLOW_MINIPROFILE_EXPLAIN'		=>	'Display online and in game status in mini-profile next to posts.',
	'STEAMSUITE_MINIPROFILE_NEVER'				=>	'No',
	'STEAMSUITE_MINIPROFILE_ONCLICK'			=>	'On click',
	'STEAMSUITE_MINIPROFILE_FIXED'				=>	'Fixed',
	'STEAMSUITE_AVATAR_PANEL_SMALL'				=>	'Use smaller avatars in Viewport',
	'STEAMSUITE_AVATAR_PANEL_SMALL_EXPLAIN'		=>	'Show small avatar images (32x32) instead of medium (64x64). <em>"Small" suggested for slow connections.</em>',
	'STEAMSUITE_AVATAR_PROFILE_BIG'				=>	'Use bigger avatars in Profile',
	'STEAMSUITE_AVATAR_PROFILE_BIG_EXPLAIN'		=>	'Show full avatar image (184x184) instead of medium (64x64).',
	'STEAMSUITE_PANEL_CENTER'					=>	'Viewport style',
	'STEAMSUITE_PANEL_CENTER_EXPLAIN'			=>	'How to show each profile block: centered or packed (side by side)',
	'STEAMSUITE_PANEL_CENTERED'					=>	'Centered',
	'STEAMSUITE_PANEL_PACKED'					=>	'Packed',

	// Template event locations
	'STEAMSUITE_INDEX'							=>	'Index',
	'STEAMSUITE_INDEX_EXPLAIN'					=>	'Display on index, required. Either top or bottom.',
	'STEAMSUITE_TOP'							=>	'Top',
	'STEAMSUITE_BOTTOM'							=>	'Bottom',
	'STEAMSUITE_FORUMS'							=>	'Forums',
	'STEAMSUITE_FORUMS_EXPLAIN'					=>	'Yes to display at the top of forum pages.',

	//ACP keys: style
	'STEAMSUITE_COLORPICKER_EXPLAIN'			=>	'Input a color in #HexDec value or use the color-picker.',
	'STEAMSUITE_COLORPICKER_STORED'				=>	'Color #HexDec value and actual color stored in the DB.',
	'STEAMSUITE_SETTINGS_HEX_STORED'			=>	'Now',

	'STEAMSUITE_ICON_DARK'						=>	'Icons Set',
	'STEAMSUITE_ICON_DARK_YES'					=>	'Dark',
	'STEAMSUITE_ICON_DARK_NO'					=>	'Light',

	'STEAMSUITE_VIEWPORT_BKG'					=>	'Background',
	'STEAMSUITE_VIEWPORT_INGAME'				=>	'Ingame',
	'STEAMSUITE_VIEWPORT_ONLINE'				=>	'Online',
	'STEAMSUITE_VIEWPORT_OFFLINE'				=>	'Offline',

	'STEAMSUITE_VIEWTOPIC_BKG'					=>	'Background',
	'STEAMSUITE_VIEWTOPIC_BKG_EXPLAIN'			=>	'<em>Applies to mini-profile only</em>',
	'STEAMSUITE_VIEWTOPIC_INGAME'				=>	'Ingame',
	'STEAMSUITE_VIEWTOPIC_ONLINE'				=>	'Online',
	'STEAMSUITE_VIEWTOPIC_OFFLINE'				=>	'Offline',
	'STEAMSUITE_VIEWTOPIC_TEXT_EXPLAIN'			=>	'<em>Applies to mini-profile and profile avatar</em>',

	// ACP keys: user settings
	'ACP_STEAM_USER_SETTINGS'					=>	'User Steam profile',
	'ACP_STEAM_ID'								=>	'Steam ID 64',
	'ACP_STEAM_ID_EXPLAIN'						=>	'Steam identifier of the user.<br><em>May be specified as an ID (17 numeric digits) or as the user short name (userurl); it will be converted to the ID always; leave empty to clear current value.</em>',
	'ACP_STEAM_NAME'							=>	'Steam Name / Personaname',
	'ACP_STEAM_AVATAR_PROFILE'					=>	'Steam Avatar & Profile URL',
	'ACP_STEAM_AVATAR_PROFILE_EXPLAIN'			=>	'Click on the avatar to take you to the user‘s Steam profile.',
	'ACP_STEAM_PROFILE_VISIBLE'					=>	'Steamsuite personality visible',
	'ACP_STEAM_PROFILE_VISIBLE_EXPLAIN'			=>	'Allow other users to know the Steam personality (name and avatar) for this user.',
	'STEAMSUITE_STEAM_ID_CHANGED'				=>	'The Steam ID has changed, please confirm that the change is correct.',
	'STEAMSUITE_STEAM_ID_NOTFOUND'				=>	'The specified Steam ID was not found, please confirm that the value is correct.',
));
