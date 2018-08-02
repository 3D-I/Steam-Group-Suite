<?php
/**
 *
 * Steam suite. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di, http://3di.space/32/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\steamsuite\ucp;

/**
 * Steam suite UCP module info.
 */
class steamsuite_info
{
	function module()
	{
		return array(
			'filename'	=> '\threedi\steamsuite\ucp\steamsuite_module',
			'title'		=> 'UCP_STEAM_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'UCP_STEAM_SETTINGS',
					'auth'	=> 'ext_threedi/steamsuite && acl_u_allow_steamsuite_view',
					'cat'	=> array('UCP_STEAM_TITLE')
				),
			),
		);
	}
}
