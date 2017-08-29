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

use threedi\steamsuite\openid\openid_exception;

/**
 * Steam suite UCP module.
 */
class steamsuite_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $request, $template, $user, $table_prefix, $phpbb_container, $phpbb_log;

		$steam = $phpbb_container->get('threedi.steamsuite.steamsuite');
		$steam_auth = $phpbb_container->get('threedi.steamsuite.steamsuite_auth');

		$user->add_lang_ext('threedi/steamsuite', array('ucp_steamsuite'));
		$this->page_title = $user->lang('UCP_STEAM_TITLE');
		$this->tpl_name = 'steam_group_suite_ucp';
		add_form_key('threedi/steamsuite_ucp');

		/* Instantiate array of errors first */
		$errors = array();

		/* If we cancelled, we should return to the situation in the Users table */
		if ($request->is_set_post('cancel'))
		{
			$request->overwrite('steam_id', $user->data['steam_id']);
		}

		/* If no steam id is set, then ask the user to identify through steam */
		if (!($steam_id = $request->variable('steam_id', $user->data['steam_id'])))
		{
			try
			{
				$identity = $steam_auth->authenticate();
			}
			catch (openid_exception $e)
			{
				$errors[] = $e->getMessage();
			}

			/**
			 * Get the Steam ID from the complete identity returned
			 * (http://steamcommunity.com/openid/id/{HereGoesTheSteamID})
			 */
			if (!empty($identity))
			{
				$ptn = "/^http:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
				preg_match($ptn, $identity, $matches);
				$steam_id = $matches[1];
			}
		}

		/* If we are trying to delete, ask for confirmation once more */
		if ($request->is_set_post('delete'))
		{
			$errors[] = 'UCP_CONFIRM_DELETE';
			$in_delete = true;
		}

		if ($steam_id)
		{
			$info = $steam->get_steam_user_info($steam_id);
		}

		/* We confirmed the operation, so let's proceed */
		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('threedi/steamsuite_ucp'))
			{
				$errors[] = 'FORM_INVALID';
			}

			/* Confirmed deletion, so reset Steam ID */
			if ($request->variable('in_delete', false))
			{
				$steam_id = '';
			}

			/* Validate the provided steam_id */
			if ($steam_id)
			{
				$errors += $steam->validate_steam_id($steam_id, (int) $user->data['user_id']);
			}

			/* Array of data to insert into the DB */
			$data = array(
				'steam_id' => $steam_id,
				'steam_profile_visible' => ($steam_id) ? $request->variable('steam_profile_visible', 0) : 0,
			);

			/* If there are no errors, proceed to the database */
			if (!count($errors))
			{
				if ($steam_id != $user->data['steam_id'])
				{
					/* Log the action */
					$phpbb_log->add('user', $user->data['user_id'], $user->ip, ($steam_id) ? 'STEAMSUITE_LOG_STEAMID_ADDED_USER' : 'STEAMSUITE_LOG_STEAMID_DELETED_USER', false,
						array(
							'reportee_id' => $user->data['user_id'],
							$user->data['username'],
							$steam_id ?: $user->data['steam_id'],
						)
					);
				}

				/* Update the DB */
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $data) . '
					WHERE user_id = ' . $user->data['user_id'];
				$db->sql_query($sql);

				/* Provide a back-link */
				meta_refresh(3, $this->u_action);
				$message = $user->lang((!$steam_id && $user->data['steam_id']) ? 'UCP_STEAM_DELETED' : 'UCP_STEAM_SAVED') . '<br /><br />' . $user->lang('RETURN_UCP', '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($message);
			}
		}

		$hidden_fields = !empty($in_delete) ? build_hidden_fields(array('in_delete' => true)) : '';

		/* Template stuffs */
		$template->assign_vars(array(
			'S_ERRORS'					=> ($errors) ? true : false,
			'ERRORS_MSG'				=> ($errors) ? implode('<br /><br />', array_map(array($user, 'lang'), $errors)) : '',

			'STEAM_ID'					=> $steam_id,
			'STEAM_NAME'				=> isset($info['personaname']) ? $info['personaname'] : null,
			'STEAM_AVATAR'				=> isset($info['avatar']) ? $info['avatar'] : null,

			'U_STEAM_PROFILE_URL'		=> isset($info['profileurl']) ? $info['profileurl'] : null,

			'S_STEAM_LOGIN'				=> $steam_id ? false : true,
			'S_STEAM_MAY_DELETE'		=> ($user->data['steam_id'] && empty($in_delete)) ? true : false,
			'S_STEAM_PROFILE_VISIBLE'	=> ($user->data['steam_profile_visible']) ? true : false,

			'S_UCP_ACTION'				=> $this->u_action,
			'S_HIDDEN_FIELDS'			=> $hidden_fields ?: null,
		));
	}
}
