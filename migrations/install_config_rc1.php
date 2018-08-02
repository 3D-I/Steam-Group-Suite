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

class install_config_rc1 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return phpbb_version_compare($this->config['threedi_steamsuite'], '1.0.0-rc1', '>=');
	}

	static public function depends_on()
	{
		return array(
			'\threedi\steamsuite\migrations\install_config',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('threedi_steamsuite', '1.0.0-rc1')),

			/* Steam original colors set*/
			array('config.add', array('threedi_steamsuite_css_colors', json_encode(array(
							'vp_bkg' => '#273B52',
							'vp_ing' => '#90ba3c',
							'vp_onl' => '#66C0F4',
							'vp_ofl' => '#898989',
							'vt_bkg' => '#273B52',
							'vt_ing' => '#90ba3c',
							'vt_onl' => '#66C0F4',
							'vt_ofl' => '#898989',
							'icondark' => false,
						)))
				),

			array('config.add', array('threedi_steamsuite_default_content', 'none')),
			array('config.add', array('threedi_steamsuite_default_avatar', 0)), // default = never
			array('config.add', array('threedi_steamsuite_group_name', '')),
			array('config.add', array('threedi_steamsuite_group_urlname', '')),
			array('config.add', array('threedi_steamsuite_group_url', '')),
			array('config.add', array('threedi_steamsuite_group_avatar', '')),
			array('config.add', array('threedi_steamsuite_group_headline', '')),
			array('config_text.add', array('threedi_steamsuite_group_description', '')),
		);
	}
}
