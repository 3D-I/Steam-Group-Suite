<?php
/**
 *
 * Steam suite. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di, http://3di.space/32/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\steamsuite\acp;

/**
 * Steam suite ACP module info.
 */
class steamsuite_info
{
	public function module()
	{
		return array(
			'filename'	=> '\threedi\steamsuite\acp\steamsuite_module',
			'title'		=> 'ACP_STEAM_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_STEAM_SETTINGS',
					'auth'	=> 'ext_threedi/steamsuite && acl_a_steamsuite_admin',
					'cat'	=> array('ACP_STEAM_TITLE')
				),
				'config'	=> array(
					'title'	=> 'ACP_STEAM_CONFIG_SETTINGS',
					'auth'	=> 'ext_threedi/steamsuite && acl_a_steamsuite_admin',
					'cat'	=> array('ACP_STEAM_TITLE')
				),
				'style'	=> array(
					'title'	=> 'ACP_STEAM_STYLE_SETTINGS',
					'auth'	=> 'ext_threedi/steamsuite && acl_a_steamsuite_admin',
					'cat'	=> array('ACP_STEAM_TITLE')
				),
			),
		);
	}
}
