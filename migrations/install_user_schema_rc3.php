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

class install_user_schema_rc3 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/**
		 * If does exists go ahead
		 */
		return !$this->db_tools->sql_column_exists($this->table_prefix . 'users', 'steam_id');
	}

	static public function depends_on()
	{
		return array(
				'\threedi\steamsuite\migrations\install_user_schema_rc2',
				'\threedi\steamsuite\migrations\install_config_rc1',
			);
	}

	public function update_schema()
	{
		return array(
			'change_columns'	=> array(
				$this->table_prefix . 'steam_suite'		=> array(
					'gameid'		=> array('BINT', 0),
				),
			),
		);
	}
}
