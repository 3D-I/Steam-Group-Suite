<?php
/**
 *
 * Steam suite. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di, http://3di.space/32/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\steamsuite\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Steam suite Event listener.
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\log */
	protected $log;

	/* @var \threedi\steamsuite\core\steamsuite */
	protected $steamsuite;

	/**
		* Constructor
		*
		* @param \phpbb\auth\auth						$auth			Authentication object
		* @param \phpbb\config\config					$config			Config Object
		* @param \phpbb\user							$user			User Object
		* @param \phpbb\request\request					$request		Request object
		* @param \phpbb\template\template				$template		Template object
		* @param \phpbb\log\log							$log			phpBB log
		* @param threedi\steamsuite\core\steamsuite		$steamsuite		Methods to be used by Class
		* @access public
	*/

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\log\log $log,
		\threedi\steamsuite\core\steamsuite	$steamsuite)
	{
		$this->auth				=	$auth;
		$this->config			=	$config;
		$this->user				=	$user;
		$this->request			=	$request;
		$this->template			=	$template;
		$this->log				=	$log;
		$this->steamsuite		=	$steamsuite;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'								=>	'steamsuite_load_language_on_setup',
			'core.permissions'								=>	'permissions',
			'core.user_setup_after'							=>	'steamsuite_refresh',
			'core.acp_users_modify_profile'					=>	'acp_users_modify_profile',
			'core.acp_users_profile_validate'				=>	'acp_users_profile_validate',
			'core.acp_users_profile_modify_sql_ary'			=>	'acp_users_profile_modify_sql_ary',
			'core.index_modify_page_title'					=>	'steamsuite_on_index',
			'core.viewforum_get_topic_data'					=>	'steamsuite_on_viewforum',
			'core.get_avatar_after'							=>	'steamsuite_default_avatar',
			'core.viewtopic_assign_template_vars_before'	=>	'steamsuite_viewtopic_template_vars',
			'core.viewtopic_cache_user_data'				=>	'steamsuite_viewtopic_cache_user_data',
			'core.viewtopic_modify_post_row'				=>	'steamsuite_viewtopic_poster_status',
			'core.memberlist_view_profile'					=>	'steamsuite_view_profile',
			'core.memberlist_prepare_profile_data'			=>	'steamsuite_prepare_profile_data',
		);
	}

// Global configuration

	/**
	 * Main language file inclusion
	 *
	 * @event core.user_setup
	 */
	public function steamsuite_load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'threedi/steamsuite',
			'lang_set' => 'steamsuite_global',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Permission's language file is automatically loaded
	 *
	 * @event core.permissions
	 */
	public function permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions += array(
			'u_allow_steamsuite_view' => array(
				'lang'	=> 'ACL_U_ALLOW_STEAMSUITE_VIEW',
				'cat'	=> 'misc',
			),
			'a_steamsuite_admin' => array(
				'lang'	=> 'ACL_A_STEAMSUITE_ADMIN',
				'cat'	=> 'misc',
			),
		);
		$event['permissions'] = $permissions;
	}

	/**
	 * Reloads the Steam data when needed (pseudo cron task)
	 *
	 * @event core.user_setup_after (since 3.1.6-RC1)
	 */
	public function steamsuite_refresh($event)
	{
		$this->steamsuite->steamsuite_refresh();
	}

// ACP User Profile

	/**
	 * Modify user data on editing profile in ACP
	 *
	 * @event core.acp_users_modify_profile
	 */
	public function acp_users_modify_profile($event)
	{
		if ($this->auth->acl_get('a_steamsuite_admin'))
		{
			$this->user->add_lang_ext('threedi/steamsuite', array('acp_steamsuite'));

			$data = $event['data'];

			$data['user_id'] = $event['user_id']; // Needed in the validate event, unavailable otherwise
			$data['steam_id'] = $this->request->variable('steam_id', $event['user_row']['steam_id']);
			$data['steam_profile_visible'] = $this->request->variable('steam_profile_visible', $event['user_row']['steam_profile_visible']);

			if ($data['steam_id'])
			{
				$info = $this->steamsuite->get_steam_user_info($data['steam_id'], !preg_match('#^\d{17,17}$#', $data['steam_id']));
			}
			$data['steam_id_notfound'] = ($data['steam_id'] && empty($info)) ? true : false;
			$data['steam_id'] = isset($info['steamid']) ? $info['steamid'] : $data['steam_id'];

			$this->template->assign_vars(array(
						'S_STEAM_ACP_USER'			=> true,
						'S_STEAM_PROFILE_VISIBLE'	=> $data['steam_profile_visible'] ? true : false,
						'STEAM_ID'					=> $data['steam_id'],
						'STEAM_NAME'				=> isset($info['personaname']) ? $info['personaname'] : null,
						'STEAM_AVATAR'				=> isset($info['avatar']) ? $info['avatar'] : null,
						'U_STEAM_PROFILE_URL'		=> isset($info['profileurl']) ? $info['profileurl'] : null,
						'S_STEAM_ID_CHANGED'		=> ($data['steam_id'] != $event['user_row']['steam_id']) ? true : false,
						'S_STEAM_ID_NOTFOUND'		=> $data['steam_id_notfound'],
			));

			$event['data'] = $data;
		}
	}

	/**
	 * Validate profile data in ACP before submitting to the database
	 *
	 * @event core.acp_users_profile_validate
	 */
	public function acp_users_profile_validate($event)
	{
		if (!empty($event['data']['steam_id']))
		{
			$errors = ($event['data']['steam_id_notfound']) ? array('STEAMSUITE_STEAM_ID_NOTFOUND') : $this->steamsuite->validate_steam_id($event['data']['steam_id'], $event['data']['user_id']);
			if ($errors)
			{
				$errors = array_merge($event['error'], $errors);
				$event['error'] = $errors;
			}
		}
	}

	/**
	 * Modify profile data in ACP before submitting to the database
	 *
	 * @event core.acp_users_profile_modify_sql_ary
	 */
	public function acp_users_profile_modify_sql_ary($event)
	{
		if (isset($event['data']['steam_id']))
		{
			$sql_ary = array_merge($event['sql_ary'], array(
							'steam_id' => $event['data']['steam_id'],
							'steam_profile_visible' => ($event['data']['steam_id']) ? $event['data']['steam_profile_visible'] : 0,
				));
			$event['sql_ary'] = $sql_ary;

			if ($event['data']['steam_id'] != $event['user_row']['steam_id'])
			{
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'STEAMSUITE_LOG_STEAMID_UPDATED', false, array(
						$event['user_row']['username'],
						$event['user_row']['steam_id'],
						$event['data']['steam_id'],
				));

				$this->log->add('user', $this->user->data['user_id'], $this->user->ip, 'STEAMSUITE_LOG_STEAMID_UPDATED_USER', false, array(
						'reportee_id' => $event['user_row']['user_id'],
						$event['user_row']['steam_id'],
						$event['data']['steam_id'],
				));
			}
		}
	}

// Index
// Viewforum

	/**
	 * Show viewport on index
	 *
	 * @event core.index_modify_page_title
	 */
	public function steamsuite_on_index($event)
	{
		if ($this->auth->acl_get('u_allow_steamsuite_view') || $this->auth->acl_get('a_steamsuite_admin'))
		{
			$this->steam_generate_viewport_data();
			$this->template->assign_vars(array(
					'S_STEAMSUITE'				=> true,
					'S_STEAMSUITE_INDEX_TOP'	=> ($this->config['threedi_steamsuite_index']) ? true : false,
					'S_STEAMSUITE_INDEX_BOTTOM'	=> ($this->config['threedi_steamsuite_index']) ? false : true,
					'S_STEAMSUITE_ADMIN'		=> ($this->auth->acl_get('a_steamsuite_admin')) ? true : false,
					'S_IS_RHEA'					=>	$this->steamsuite->is_rhea(),
				));
		}
	}

	/**
	 * Show viewport on viewforum
	 *
	 * @event core.viewforum_get_topic_data
	 */
	public function steamsuite_on_viewforum($event)
	{
		if ($this->config['threedi_steamsuite_forums'] && ($this->auth->acl_get('u_allow_steamsuite_view') || $this->auth->acl_get('a_steamsuite_admin')))
		{
			$this->steam_generate_viewport_data();
			$this->template->assign_vars(array(
					'S_STEAMSUITE'				=> true,
					'S_STEAMSUITE_FORUMS'		=> true,
					'S_STEAMSUITE_ADMIN'		=> ($this->auth->acl_get('a_steamsuite_admin')) ? true : false,
					'S_IS_RHEA'					=>	$this->steamsuite->is_rhea(),
				));
		}
	}

	/**
	 * Generate viewport data for the template
	 */
	public function steam_generate_viewport_data()
	{
		/* Load the relevant language files */
		$this->user->add_lang_ext('threedi/steamsuite', array('steamsuite_common', 'steamsuite_viewport'));

		/* Let's assign viewport configuration variables */
		$this->template->assign_vars(array(
			'STEAM_STORAGE'						=>	$this->config['cookie_name'] . "_{$this->user->data['user_id']}_",
			'STEAM_VIEWROWS'					=>	(int) $this->config['threedi_steamsuite_ttl_rows'],
			'STEAMSUITE_DEFAULT_CONTENT'		=>	$this->config['threedi_steamsuite_default_content'] ?: 'none',
			'S_STEAMSUITE_LIST_ONLINE'			=>	($this->config['threedi_steamsuite_list_online']) ? true : false,
			'S_STEAMSUITE_PANEL_AVATAR_SMALL'	=>	($this->config['threedi_steamsuite_avatar_panel_small']) ? true : false,
			'S_STEAMSUITE_PANEL_CENTER'			=>	($this->config['threedi_steamsuite_panel_center']) ? true : false,
		));

		/* Now assign general CSS related configuration */
		$this->template->assign_vars($this->steam_viewport_template_data());

		/* Now get the users */
		$steam_users = $this->steamsuite->get_steam_users_data();

		/* And send them to the template */
		foreach ($steam_users as $steam_user)
		{
			$this->template->assign_block_vars('steam_group', $this->steam_user_template_data($steam_user));
		}
		/* Add empty containers to fix end wraping (align last line) in flex box; this is a hack! */
		for ($i = 0; $i < 6; $i++)
		{
			$this->template->assign_block_vars('steam_group', ['STEAM_ID' => 0]);
		}

		/* Get statistics */
		list($stats_global, $stats_state, $stats_ingame) = $this->steamsuite->get_steam_users_statistics();

		/* Assign global statistics */
		$this->template->assign_vars($stats_global);

		/* Now loop through the state statistics... */
		foreach ($stats_state as $state_id => $state)
		{
			$this->template->assign_block_vars('steam_stats_state', array(
				'STEAM_STATE_ID'	=> $state_id,
				'STEAM_STATE'		=> $state['name'],
				'STEAM_STATE_COUNT'	=> $state['count'],
			));
		}

		/* ...and through the in-game statistics */
		foreach ($stats_ingame as $game_id => $game)
		{
			$this->template->assign_block_vars('steam_stats_game', array(
				'STEAM_GAME_ID'		=> $game_id,
				'STEAM_GAME'		=> $game['name'],
				'STEAM_GAME_COUNT'	=> $game['count'],
			));
		}
	}

	/**
	 * General template switches for viewport
	 *
	 * @return array Adecuate to send directly to the template
	 */
	protected function steam_viewport_template_data()
	{
		$color_configs = json_decode($this->config['threedi_steamsuite_css_colors'], true);

		return array(
			/* CSS colors viewport */
			'VIEWPORT_BKG'				=>	$color_configs['vp_bkg'],
			'VIEWPORT_INGAME'			=>	$color_configs['vp_ing'],
			'VIEWPORT_ONLINE'			=>	$color_configs['vp_onl'],
			'VIEWPORT_OFFLINE'			=>	$color_configs['vp_ofl'],
			/* CSS 3.1.x icons */
			'S_31_ICON_DARK'			=>	($color_configs['icondark']) ? ' icon-dark' : '',
		);
	}

// Steam avatars

	/**
	* Use the Steam avatar in place of forum avatar according to selected conditions
	*
	* @event core.get_avatar_after
	*/
	public function steamsuite_default_avatar($event)
	{
		/* If the ACP config is NEVER do nothing */
		if ((int) $this->config['threedi_steamsuite_default_avatar'] == 0)
		{
			return;
		}

		/**
		 * We are in the UCP/ACP, so we should not change the selected avatar
		 */
		if ($event['ignore_config'])
		{
			return;
		}

		$event_row = $event['row'];

		/**
		 * Check the Steam permissions and indexes existance first.
		 */
		if (!empty($event_row['steam_profile_visible']) && $this->auth->acl_get('u_allow_steamsuite_view') || $this->auth->acl_get('a_steamsuite_admin'))
		{
			/**
			 * Check for avatar and ACP settings first:
			 * if no avatar is set, and configuration is steam avatar as default
			 * or configuration is set to use steam avatar always if available
			 * then check for steam_id's and related data existance
			 */
			if ( (empty($event_row['avatar']) || ((int) $this->config['threedi_steamsuite_default_avatar'] == 2)) && (!empty($event_row['steam_id'])) && ($steam_profile = $this->steamsuite->get_profile_data($event_row['steam_id'])) )
			{
				/**
				 * Uses the maximum avatar size possible within the specified configuration
				 * It uses the next biggest steam avatar between those three available (all square)
				 */
				$width = $height = min((int) $this->config['avatar_max_width'], (int) $this->config['avatar_max_height'], 184);
				$avatar_sfx = ($width > 64) ? '_full' : (($width > 32) ? '_medium' : '');
				/**
				 * Avatar size soft-reduced to fit in the specified avatar forum sizes
				 */
				$src = 'src="' . ((string) substr_replace($steam_profile['steam_avatar'], $avatar_sfx, strrpos($steam_profile['steam_avatar'], '.'), 0)) . '"';
				$width = 'width="'. ((int) $width) .'"';
				$height = 'height="'. ((int) $height) .'"';
				$alt = 'alt="'. $this->user->lang('STEAM_AVATAR') . '"';
				/* Let's concatenate and replace */
				$event['html'] = '<img class="avatar" ' . "{$src} {$width} {$height} {$alt}" . ' />';
			}
		}
	}

// Viewtopic mini-profile

	/**
	 * Global template vars for viewtopic
	 *
	 * @event core.viewtopic_assign_template_vars_before
	 */
	public function steamsuite_viewtopic_template_vars($event)
	{
		if ($this->auth->acl_get('u_allow_steamsuite_view') || $this->auth->acl_get('a_steamsuite_admin'))
		{
			/* Load the relevant language files */
			$this->user->add_lang_ext('threedi/steamsuite', array('steamsuite_common', 'steamsuite_profile'));

			$this->template->assign_vars(array(
					'S_STEAMSUITE'				=> true,
					'S_STEAMSUITE_ADMIN'		=> ($this->auth->acl_get('a_steamsuite_admin')) ? true : false,
					'S_IS_RHEA'					=> $this->steamsuite->is_rhea(),
					'S_STEAMSUITE_MP_CLICK'		=> ($this->config['threedi_steamsuite_miniprofile'] == 1) ? true : false,
				));
			$this->template->assign_vars($this->steam_profile_template_data());
		}
	}

	/**
	 * Modify the users' data displayed within their posts
	 *
	 * @event core.viewtopic_cache_user_data
	 */
	public function steamsuite_viewtopic_cache_user_data($event)
	{
		/**
		 * Check permission prior to run the code
		 */
		if (($this->config['threedi_steamsuite_miniprofile'] && $this->auth->acl_get('u_allow_steamsuite_view')) || $this->auth->acl_get('a_steamsuite_admin'))
		{
			/**
			 * If the user has not configured a steam id or it is not in the group, nothing is shown
			 */
			if ($event['row']['steam_id'])
			{
				$array = $event['user_cache_data'];
				$array['steam_id'] = $event['row']['steam_id'];
				$array['steam_profile_visible'] = $event['row']['steam_profile_visible'];

				if ($steam_profile = $this->steamsuite->get_profile_data($event['row']['steam_id']))
				{
					if ($array['steam_profile_visible'] || $this->auth->acl_get('a_steamsuite_admin'))
					{
						$array = array_merge($array, $steam_profile);
					}
				}

				$event['user_cache_data'] = $array;
			}
		}
	}

	/**
	 * Modify the posts template block
	 *
	 * @event core.viewtopic_modify_post_row
	 */
	public function steamsuite_viewtopic_poster_status($event)
	{
		$post_row = $event['post_row'];

		$post_row['S_STEAM_USER'] = isset($event['user_poster_data']['steam_id']) ? (bool) $event['user_poster_data']['steam_id'] : false;
		$post_row['S_STEAM_PROFILE_VISIBLE'] = isset($event['user_poster_data']['steam_profile_visible']) ? (bool) $event['user_poster_data']['steam_profile_visible'] : false;

		if (isset($event['user_poster_data']['steam_state']))
		{
			$post_row += $this->steam_user_template_data($event['user_poster_data']);
		}

		$event['post_row'] = $post_row;
	}

// Viewprofile

	/**
	 * Add Steam data to view profile, if available and allowed
	 *
	 * @event core.memberlist_view_profile
	 */
	public function steamsuite_view_profile($event)
	{
		if (($this->auth->acl_get('u_allow_steamsuite_view') && $event['member']['steam_profile_visible']) ||
			($this->auth->acl_get('a_steamsuite_admin') && $event['member']['steam_id']))
		{
			/* Load the relevant language files */
			$this->user->add_lang_ext('threedi/steamsuite', array('steamsuite_common', 'steamsuite_profile'));

			/* Add Steam data to the already existing user data */
			$member = $event['member'];
			if ($steam_profile = $this->steamsuite->get_profile_data($member['steam_id']))
			{
				$event['member'] = array_merge($member, $steam_profile);
			}
		}
	}

	/**
	 * Add Steam template data to view profile
	 *
	 * @event core.memberlist_prepare_profile_data
	 */
	public function steamsuite_prepare_profile_data($event)
	{
		$data = $event['data'];
		$template_data = $event['template_data'];

		$template_data['S_STEAMSUITE'] = ($this->auth->acl_get('u_allow_steamsuite_view') || $this->auth->acl_get('a_steamsuite_admin')) ? true : false;
		$template_data['S_STEAM_USER'] = isset($data['steam_id']) ? (bool) $data['steam_id'] : false;
		$template_data['S_STEAM_PROFILE_VISIBLE'] = $data['steam_profile_visible'] ? true : false;
		$template_data['S_STEAMSUITE_ADMIN'] = ($this->auth->acl_get('a_steamsuite_admin')) ? true : false;
		$template_data['S_IS_RHEA'] = $this->steamsuite->is_rhea();

		if (!empty($data['steam_name']))
		{
			$template_data['S_STEAMSUITE_PROFILE_AVATAR_BIG'] = ($this->config['threedi_steamsuite_avatar_profile_big']) ? true : false;

			$template_data += $this->steam_profile_template_data();
			$template_data += $this->steam_user_template_data($data);
		}

		$event['template_data'] = $template_data;
	}

	/**
	 * General template switches for viewtopic miniprofile and memberlist profile
	 *
	 * @return array Adecuate to send directly to the template
	 */
	protected function steam_profile_template_data()
	{
		$color_configs = json_decode($this->config['threedi_steamsuite_css_colors'], true);

		return array(
			/* CSS colors viewtopic */
			'VIEWTOPIC_BKG'				=>	$color_configs['vt_bkg'],
			'VIEWTOPIC_INGAME'			=>	$color_configs['vt_ing'],
			'VIEWTOPIC_ONLINE'			=>	$color_configs['vt_onl'],
			'VIEWTOPIC_OFFLINE'			=>	$color_configs['vt_ofl'],
			/* CSS 3.1.x icons */
			'S_31_ICON_DARK'			=>	($color_configs['icondark']) ? ' icon-dark' : '',
		);
	}

	/**
	 * Convert steam user data received from the database into consistent template variables
	 *
	 * @param	array	$data	Array with data obtained from the database
	 * @return	array			Array with transformed data to be sent into the template
	 */
	protected function steam_user_template_data($data)
	{
		return array(
				'STEAM_ID'					=>	$data['steam_id'],
				'STEAM_NAME'				=>	htmlspecialchars($data['steam_name']),
				'U_STEAM_PROFILE'			=>	$data['steam_profile_url'],
				'S_STEAM_PROFILE_PUBLIC'	=>	(int) $data['steam_profile_public'] ? true : false,
				'STEAM_AVATAR'				=>	$data['steam_avatar'],
				'STEAM_AVATAR_MEDIUM'		=>	substr_replace($data['steam_avatar'], '_medium', strrpos($data['steam_avatar'], '.'), 0),
				'STEAM_AVATAR_LARGE'		=>	substr_replace($data['steam_avatar'], '_full', strrpos($data['steam_avatar'], '.'), 0),
				'STEAM_STATE_ID'			=>	$data['steam_state'],
				'STEAM_STATE'				=>	$this->user->lang['STEAM_ONLINE'][$data['steam_state']],
				'STEAM_INGAME_ID'			=>	(int) $data['steam_game_id'],
				'STEAM_INGAME'				=>	$data['steam_game_name'] ?: ($data['steam_game_id'] ? $this->user->lang['STEAM_GAME_UNKNOWN'] : $this->user->lang['STEAM_NOT_IN_GAME']),
				'STEAM_BADGE'				=> (string) $this->steamsuite->user_badge($data['steam_id']),
			);
	}
}
