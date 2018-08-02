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

class install_config extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		/**
		 * If does NOT exists go ahead
		 */
		return isset($this->config['threedi_steamsuite']);
	}

	static public function depends_on()
	{
		return array('\threedi\steamsuite\migrations\install_user_schema');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('threedi_steamsuite', '1.0.0-b1')),
			array('config.add', array('threedi_steamsuite_api_key', '')),
			array('config.add', array('threedi_steamsuite_group_id', '')),
			array('config.add', array('threedi_steamsuite_ttl', 30)),
			array('config.add', array('threedi_steamsuite_ttl_last', '')),
			array('config.add', array('threedi_steamsuite_ttl_last_success', '')),
			array('config.add', array('threedi_steamsuite_miniprofile', 1)),
			array('config.add', array('threedi_steamsuite_ttl_rows', 2)),
			array('config.add', array('threedi_steamsuite_list_online', 0)),
			array('config.add', array('threedi_steamsuite_last_check_in', 0)),
			array('config.add', array('threedi_steamsuite_avatar_panel_small', 0)),
			array('config.add', array('threedi_steamsuite_avatar_profile_big', 0)),
			array('config.add', array('threedi_steamsuite_panel_center', 0)),
			/* By default the viewport is shown top of the index page */
			array('config.add', array('threedi_steamsuite_index', 1)),
			array('config.add', array('threedi_steamsuite_forums', 0)),
		);
	}
}
