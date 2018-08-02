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
 * Steam suite ACP module.
 */
class steamsuite_module
{
	public $u_action;

	public function main($id, $mode)
	{
		global $config, $request, $template, $user, $phpbb_container, $phpbb_log;

		$steam = $phpbb_container->get('threedi.steamsuite.steamsuite');

		$user->add_lang_ext('threedi/steamsuite', array('acp_steamsuite'));
		$this->page_title = $user->lang('ACP_STEAM_TITLE');
		$this->tpl_name = 'steam_group_suite_acp';
		add_form_key('threedi/steamsuite');

		/* Do this now and forget */
		$errors = array();

		/* If we cancelled, we should return to the original situation */
		if ($request->is_set_post('cancel'))
		{
			$request->overwrite('threedi_steamsuite_api_key', $config['threedi_steamsuite_api_key']);
			$request->overwrite('threedi_steamsuite_group_id', $config['threedi_steamsuite_group_id']);
		}

		/* If requirements not met, warn admin, provide feedback, and end here */
		if ($requirement_failures = (int) $config['threedi_steamsuite_last_check_in'])
		{
			if ( $requirement_failures & $steam::COMMUNICATION_ERROR )
			{
				$errors[] = $user->lang('STEAMSUITE_LOG_CURL_COMM_ERROR');
			}
			if ( $requirement_failures & $steam::CURL_MISSING )
			{
				$errors[] = $user->lang('STEAMSUITE_LOG_CURL_ERROR');
			}
			if ( $requirement_failures & $steam::ICONV_MISSING )
			{
				$errors[] = $user->lang('STEAMSUITE_LOG_ICONV_ERROR');
			}
			if ( $requirement_failures & $steam::SIMPLEXMLLS_MISSING )
			{
				$errors[] = $user->lang('STEAMSUITE_LOG_SIMPLEXMLLS_ERROR');
			}
		}

		/* Let's read the style configuration */
		$color_configs = json_decode($config['threedi_steamsuite_css_colors'], true);

		$api_key = $request->variable('threedi_steamsuite_api_key', $config['threedi_steamsuite_api_key']);
		$group_id = $request->variable('threedi_steamsuite_group_id', $config['threedi_steamsuite_group_id']);

		/* Vars related to the Steam group */
		if ($api_key && $group_id)
		{
			$info = $steam->get_steam_group_info($group_id, !preg_match('#^[0-9]{18}$#', $group_id), $api_key);
		}
		$group_id = isset($info['groupid']) ? $info['groupid'] : $group_id;

		/* The main stuff */
		if ( $request->is_set_post('update') )
		{
			if (!check_form_key('threedi/steamsuite'))
			{
				trigger_error('FORM_INVALID');
			}

			/* Only Capital letters and numbers, max 32 */
			if ($api_key && !preg_match('/^[A-Z0-9]{32}$/', $api_key))
			{
				$errors[] = $user->lang('STEAMSUITE_API_KEY_INVALID');
			}
			/* Only numbers, max 18. It is a string anyway */
			if ($api_key && $group_id && !preg_match('/^[0-9]{18}$/', $group_id))
			{
				$errors[] = $user->lang('STEAMSUITE_GROUP_ID_INVALID');
			}
			/* If we have a group id but no info, then we didn't find it, it may not exist */
			if ($api_key && $group_id && empty($info))
			{
				$errors[] = $user->lang('STEAMSUITE_GROUP_ID_NOTFOUND');
			}
			/* If we have a group id but no api key, we cannot continue, either both or none */
			if (!$api_key && $group_id)
			{
				$errors[] = $user->lang('STEAMSUITE_GROUP_ID_NOAPIKEY');
			}

			/* No errors? Great, let's go. */
			if ( !count($errors) )
			{
				/**
				 * If we are changing the group id, the existing information
				 * in the database is not valid. Let's clear the DB.
				 */
				if ($group_id != $config['threedi_steamsuite_group_id'])
				{
					$config->set('threedi_steamsuite_ttl_last_success', 0);
					$config->set('threedi_steamsuite_ttl_last', 0);
					$steam->steamsuite_reset();
				}

				$config->set('threedi_steamsuite_api_key', $api_key);
				$config->set('threedi_steamsuite_group_id', $group_id);
				$config->set('threedi_steamsuite_ttl', $request->variable('threedi_steamsuite_ttl', (int) $config['threedi_steamsuite_ttl']));
				$config->set('threedi_steamsuite_ttl_rows', $request->variable('threedi_steamsuite_ttl_rows', (int) $config['threedi_steamsuite_ttl_rows']));
				$config->set('threedi_steamsuite_list_online', $request->variable('threedi_steamsuite_list_online', (int) $config['threedi_steamsuite_list_online']));
				$config->set('threedi_steamsuite_default_content', $request->variable('threedi_steamsuite_default_content', $config['threedi_steamsuite_default_content']));
				$config->set('threedi_steamsuite_default_avatar', $request->variable('threedi_steamsuite_default_avatar', (int) $config['threedi_steamsuite_default_avatar']));
				$config->set('threedi_steamsuite_miniprofile', $request->variable('threedi_steamsuite_miniprofile', (int) $config['threedi_steamsuite_miniprofile']));
				$config->set('threedi_steamsuite_avatar_panel_small', $request->variable('threedi_steamsuite_avatar_panel_small', (int) $config['threedi_steamsuite_avatar_panel_small']));
				$config->set('threedi_steamsuite_avatar_profile_big', $request->variable('threedi_steamsuite_avatar_profile_big', (int) $config['threedi_steamsuite_avatar_profile_big']));
				$config->set('threedi_steamsuite_panel_center', $request->variable('threedi_steamsuite_panel_center', (int) $config['threedi_steamsuite_panel_center']));
				$config->set('threedi_steamsuite_index', $request->variable('threedi_steamsuite_index', (int) $config['threedi_steamsuite_index']));
				$config->set('threedi_steamsuite_forums', $request->variable('threedi_steamsuite_forums', (int) $config['threedi_steamsuite_forums']));
				/* Styles' specific vars */
				$color_configs['vp_bkg'] = $request->variable('vp_bkg', $color_configs['vp_bkg']);
				$color_configs['vp_ing'] = $request->variable('vp_ing', $color_configs['vp_ing']);
				$color_configs['vp_onl'] = $request->variable('vp_onl', $color_configs['vp_onl']);
				$color_configs['vp_ofl'] = $request->variable('vp_ofl', $color_configs['vp_ofl']);
				$color_configs['vt_bkg'] = $request->variable('vt_bkg', $color_configs['vt_bkg']);
				$color_configs['vt_ing'] = $request->variable('vt_ing', $color_configs['vt_ing']);
				$color_configs['vt_onl'] = $request->variable('vt_onl', $color_configs['vt_onl']);
				$color_configs['vt_ofl'] = $request->variable('vt_ofl', $color_configs['vt_ofl']);
				$color_configs['icondark'] = $request->variable('icondark', $color_configs['icondark']);
				$config->set('threedi_steamsuite_css_colors', json_encode($color_configs));
				/* Log the action. */
				$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'STEAMSUITE_LOG_' . strtoupper($mode) . '_SAVED', false, array());
				/* Succes, give them a back-link */
				trigger_error($user->lang('ACP_STEAM_SETTINGS_SAVED') . adm_back_link($this->u_action));
			}
		}

		/**
		 * Vars to be assigned to the template for statistics
		 */
		$steam_date_attempt = ($config['threedi_steamsuite_ttl_last']) ? $user->format_date($config['threedi_steamsuite_ttl_last']) : '';
		$steam_last_success_refresh = ($config['threedi_steamsuite_ttl_last_success']) ? $user->format_date($config['threedi_steamsuite_ttl_last_success']) : '';

		/**
		 * Real timegap calculation
		 */
		$steam_times_gap_total = $steam->timegap( (int) $config['threedi_steamsuite_ttl_last_success'], (int) $config['threedi_steamsuite_ttl_last'] );
		$overdue = (strpos($steam_times_gap_total, '- ') === 0) ? true : false;
		$steam_times_gap_total = str_replace('- ', '', $steam_times_gap_total);

		/* If we don't have the Keys set, we must set them before anything else */
		if (!$api_key || !$group_id)
		{
			$mode = 'settings';
			$this->u_action = preg_replace('#mode=[^&]*#', 'mode=settings', $this->u_action);
		}

		/* Guess what.. */
		$template->assign_vars(array(
			// Common variables
			'S_IS_RHEA'							=> $steam->is_rhea(),
			'S_APIKEYS'							=> ( $api_key && $group_id ) ? true : false,
			'S_ERRORS'							=> ($errors) ? true : false,
			'ERRORS_MSG'						=> ($errors) ? implode('<br /><br />', $errors) : '',
			'U_ACTION'							=> $this->u_action,
			'STEAMSUITE_MODE'					=> $mode,
			// Core configuration
			'STEAMSUITE_API_KEY'				=> $api_key,
			'STEAMSUITE_GROUP_ID'				=> $group_id,
			'S_STEAMSUITE_GROUP_ID_CHANGED'		=> ($group_id != $config['threedi_steamsuite_group_id']) ? true : false,
			'S_STEAMSUITE_GROUP_ID_NOTFOUND'	=> ($group_id && empty($info)) ? true : false,
			'STEAMSUITE_GROUP_NAME'				=> isset($info['groupname']) ? $info['groupname'] : '',
			'U_STEAMSUITE_GROUP_PROFILE'		=> isset($info['groupurl']) ? $info['groupurl'] : '',
			'STEAMSUITE_GROUP_AVATAR'			=> isset($info['groupavatar']) ? substr_replace($info['groupavatar'], '_medium', strrpos($info['groupavatar'], '.'), 0) : '',
			'STEAMSUITE_GROUP_HEADLINE'			=> isset($info['groupheadline']) ? $info['groupheadline'] : '',
			'STEAMSUITE_GROUP_DESCRIPTION'		=> isset($info['groupdescription']) ? htmlspecialchars_decode($info['groupdescription']) : '',
			// Statistics
			'STEAMSUITE_TTL'					=> $request->variable('threedi_steamsuite_ttl', (int) $config['threedi_steamsuite_ttl']),
			'STEAMSUITE_REFRESH_LAST_ATTEMPT'	=> $steam_date_attempt,
			'STEAMSUITE_REFRESH_LAST_SUCCESS'	=> $steam_last_success_refresh,
			'S_STEAMSUITE_OVERDUE'				=> $overdue,
			'STEAMSUITE_TIMEGAP'				=> $steam_times_gap_total,
			// General configuration
			'STEAMSUITE_DEFAULT_CONTENT'		=> $config['threedi_steamsuite_default_content'], // none, ingame, online, all
			'STEAMSUITE_ROWS'					=> (int) $config['threedi_steamsuite_ttl_rows'],
			'STEAMSUITE_DEFAULT_AVATAR'			=> (int) $config['threedi_steamsuite_default_avatar'], // 0 = never, 1 = default, 2 = always
			'STEAMSUITE_ALLOW_MINIPROFILE'		=> (int) $config['threedi_steamsuite_miniprofile'], // 0: no, 1: on click, 2: fixed
			'STEAMSUITE_LIST_ONLINE'			=> ($config['threedi_steamsuite_list_online']) ? true : false,
			'STEAMSUITE_AVATAR_PANEL_SMALL'		=> ($config['threedi_steamsuite_avatar_panel_small']) ? true : false,
			'STEAMSUITE_AVATAR_PROFILE_BIG'		=> ($config['threedi_steamsuite_avatar_profile_big']) ? true : false,
			'STEAMSUITE_PANEL_CENTER'			=> ($config['threedi_steamsuite_panel_center']) ? true : false,
			// Template locations
			'STEAMSUITE_INDEX'					=> ($config['threedi_steamsuite_index']) ? true : false,
			'STEAMSUITE_FORUMS'					=> ($config['threedi_steamsuite_forums']) ? true : false,
			// Style configuration
			'VIEWPORT_BKG'						=>	$color_configs['vp_bkg'],
			'VIEWPORT_INGAME'					=>	$color_configs['vp_ing'],
			'VIEWPORT_ONLINE'					=>	$color_configs['vp_onl'],
			'VIEWPORT_OFFLINE'					=>	$color_configs['vp_ofl'],
			'VIEWTOPIC_BKG'						=>	$color_configs['vt_bkg'],
			'VIEWTOPIC_INGAME'					=>	$color_configs['vt_ing'],
			'VIEWTOPIC_ONLINE'					=>	$color_configs['vt_onl'],
			'VIEWTOPIC_OFFLINE'					=>	$color_configs['vt_ofl'],
			'S_31_ICON_DARK'					=>	$color_configs['icondark'],
		));
	}
}
