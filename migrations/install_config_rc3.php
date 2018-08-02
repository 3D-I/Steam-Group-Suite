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

class install_config_rc3 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return phpbb_version_compare($this->config['threedi_steamsuite'], '1.0.0-rc3', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\threedi\steamsuite\migrations\install_user_schema_rc3',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('threedi_steamsuite', '1.0.0-rc3')),
		);
	}
}
