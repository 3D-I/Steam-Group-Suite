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

class install_user_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/**
		 * If does NOT exists go ahead
		 */
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'steam_id');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'steam_suite'	=> array(
					'COLUMNS'		=> array(
						'steamsuite_id'		=> array('UINT', null, 'auto_increment'),
						'steam_id'			=> array('VCHAR:255', ''),
						'personaname'		=> array('VCHAR:255', ''),
						'profileurl'		=> array('VCHAR:255', ''),
						'avatar'			=> array('VCHAR:255', ''),
						'personastate'		=> array('UINT:1', '0'),
						'profilevisible'	=> array('UINT:1', '0'),
						'gameid'			=> array('UINT', '0'),
						'gameextrainfo'		=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY'	=> 'steamsuite_id',
					'KEYS' => array(
						'steam_id'			=> array('UNIQUE', 'steam_id'),
					),
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'users'	=> array(
					'steam_id'				=> array('VCHAR:255', ''),
					'steam_profile_visible'	=> array('TINT:1', '0'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'steam_id',
					'steam_profile_visible',
				),
			),
			'drop_tables'		=> array(
				$this->table_prefix . 'steam_suite',
			),
		);
	}
}
