<?php
/**
 *
 * Steam suite. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di, http://3di.space/32/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\steamsuite\migrations;

class install_perms extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\threedi\steamsuite\migrations\install_config');
	}

	public function update_data()
	{
		return array(
			array('permission.add', array('u_allow_steamsuite_view')),
			array('permission.permission_set', array('REGISTERED', 'u_allow_steamsuite_view', 'group')),
		);
	}
}
