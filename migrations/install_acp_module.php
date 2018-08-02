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

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_data()
	{
		return array(
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_STEAM_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_STEAM_TITLE',
				array(
					'module_basename'	=> '\threedi\steamsuite\acp\steamsuite_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
