<?php
/**
 *
 * Steam suite. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di, http://3di.space/32/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\steamsuite\core;

class steamsuite
{
	const CURL_MISSING = 1;
	const ICONV_MISSING = 2;
	const SIMPLEXMLLS_MISSING = 4;
	const COMMUNICATION_ERROR = 8;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver */
	protected $db;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\log */
	protected $log;

	/**
	 * @var string - The database table the users of the Steam group are stored in
	 */
	protected $steam_suite_table;

	/* Steam keys */
	protected $group_id;
	protected $api_key;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config			$config					Config Object
	 * @param \phpbb\config\db_text			$config_text			Config Text Object
	 * @param \phpbb\db\driver\driver		$db						Database object
	 * @param \phpbb\user					$user					User object
	 * @param string						$steam_suite_table		Name of the Steam table
	 * @param \phpbb\log\log				$log					phpBB log
	 */
	public function __construct(
			\phpbb\config\config $config,
			\phpbb\config\db_text $config_text,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\user $user,
			$steam_suite_table,
			\phpbb\log\log $log)
	{
		$this->config		=	$config;
		$this->config_text	=	$config_text;
		$this->db			=	$db;
		$this->user			=	$user;
		$this->steam_suite	=	$steam_suite_table;
		$this->log			=	$log;

		/* Steam Group's ID */
		$this->group_id		=	$this->config['threedi_steamsuite_group_id'];

		/* Steam Web API key */
		$this->api_key		=	$this->config['threedi_steamsuite_api_key'];
	}

	/**
	 * Returns whether the API Keys are present or not
	 *
	 * @return bool
	 */
	public function check_api_keys()
	{
		return ( $this->config['threedi_steamsuite_api_key'] && $this->config['threedi_steamsuite_group_id'] );
	}

	/**
	 * Returns whether the phpBB is equal or greater than v3.2.0
	 *
	 * @return bool
	 */
	public function is_rhea()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.2.1', '>=');
	}

	/**
	 * Returns whether cURL() is available
	 *
	 * @return bool
	 */
	public function is_curl()
	{
		return function_exists('curl_version');
	}

	/**
	 * Returns whether iconv() is available
	 *
	 * @return bool
	 */
	public function is_iconv()
	{
		return function_exists('iconv');
	}

	/**
	 * Returns whether simplexml_load_string() is available
	 *
	 * @return bool
	 */
	public function is_simplexml_load_string()
	{
		return function_exists('simplexml_load_string');
	}

	/**
	 * Returns if the refresh time sat in ACP has passed
	 *
	 * @return bool
	 */
	public function steamsuite_refresh_time()
	{
		$last = (int) $this->config['threedi_steamsuite_ttl_last'];
		$success = (int) $this->config['threedi_steamsuite_ttl_last_success'];
		$ttl = ($last > $success) ? 60 : (int) $this->config['threedi_steamsuite_ttl'] * 60;

		return (bool) ($last + $ttl < time());
	}

	/**
	 * Checks if an error condition was already present, and if it has changed, it logs an error and saves new state
	 *
	 * @param int		$mask	The bitwise error mask before this check
	 * @param bool		$check	True if met, false if not
	 * @param int		$bit	Constant with the bit reference to update in case of change
	 * @param string	$logmsg	String to complete the log message to use
	 * @return int			The new bitwise error mask after this check
	 */
	protected function check_and_log($mask, $check, $bit, $logmsg)
	{
		if (!$check)
		{
			if (!($mask & $bit))
			{
				$this->log->add('critical', $this->user->data['user_id'], $this->user->ip, 'STEAMSUITE_LOG_' . $logmsg . '_ERROR');
				$mask &= $bit;
			}
		}
		else if ($mask & $bit)
		{
			$this->log->add('critical', $this->user->data['user_id'], $this->user->ip, 'STEAMSUITE_LOG_' . $logmsg . '_OK');
			$mask &= ~$bit;
		}
		return $mask;
	}

	/**
	 * Returns if the SteamSuite requirements are met
	 * If not, logs are recorded with the missing functions
	 * No logs will be repeated.
	 * When the situation is fixed, it will be logged as well.
	 *
	 * @return bool
	 */
	protected function steamsuite_check_requirements()
	{
		$last_check_in = (int) $this->config['threedi_steamsuite_last_check_in'];

		if ( $last_check_in || !$this->is_curl() || !$this->is_iconv() || !$this->is_simplexml_load_string() )
		{
			$last_check_in = $this->check_and_log($last_check_in, $this->is_curl(), self::CURL_MISSING, 'CURL');
			$last_check_in = $this->check_and_log($last_check_in, $this->is_iconv(), self::ICONV_MISSING, 'ICONV');
			$last_check_in = $this->check_and_log($last_check_in, $this->is_simplexml_load_string(), self::SIMPLEXMLLS_MISSING, 'SIMPLEXMLLS');

			/**
			 * Store the situation
			 */
			$this->config->set('threedi_steamsuite_last_check_in', $last_check_in );
		}

		return !($last_check_in & (self::CURL_MISSING | self::ICONV_MISSING | self::SIMPLEXMLLS_MISSING));
	}

	/**
	 * Executes a single curl query
	 *
	 * @return mixed	False on error, array with results on success
	 */
	protected function perform_curl_query($curl_url, $log_error = false)
	{
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_URL, $curl_url);
		$curl_response = curl_exec($curl_handle);
		$curl_code	= curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		curl_close($curl_handle);

		if ( ($curl_code !== 200) || (!$curl_response) )
		{
			if ($log_error)
			{
				$last_check_in = (int) $this->config['threedi_steamsuite_last_check_in'];

				if ($curl_code === 0)
				{
					$last_check_in = $this->check_and_log($last_check_in, false, self::COMMUNICATION_ERROR, 'HTTP_ZERO');
				}
				else if ($curl_code === 403)
				{
					$last_check_in = $this->check_and_log($last_check_in, false, self::COMMUNICATION_ERROR, 'HTTP_NO_AUTH');
				}
				else if ($curl_code === 404)
				{
					$last_check_in = $this->check_and_log($last_check_in, false, self::COMMUNICATION_ERROR, 'HTTP_404');
				}
				else
				{
					$last_check_in = $this->check_and_log($last_check_in, false, self::COMMUNICATION_ERROR, 'CURL_COMM');
				}

				$this->config->set('threedi_steamsuite_last_check_in', $last_check_in );
			}
			return false;
		}
		return $curl_response;
	}

	/**
	 * Executes a single curl XML query and sanitizes the returned data
	 *
	 * @return mixed	False on error, array with results on success
	 */
	protected function perform_xml_query($curl_url, $log_error = false)
	{
		if (!($curl_response = $this->perform_curl_query($curl_url, $log_error)))
		{
			return false;
		}

		/**
		 * We can not be sure that the xml is encoded in UTF-8
		 * or contains bad characters. Lesson taken. ;)
		 */
		$curl_response = @iconv('UTF-8', 'UTF-8//IGNORE', $curl_response);

		/**
		 * No 4-Bytes Unicode Chars allowed, instead, a 3-bytes Char replacement.
		 * For hexadecimal values do not use single quotes
		 * That's due to phpBB's 3.1.x no Emojis
		 */
		$curl_response = preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xef\xbf\xbd", $curl_response);

		/* Returns the XML object, first supressing errors */
		libxml_use_internal_errors(true);
		if (($xml = simplexml_load_string($curl_response, 'SimpleXMLElement', LIBXML_NOCDATA)) == false)
		{
			return false;
		}

		/* Returns the JSON representation of a value */
		$json = json_encode($xml);

		/**
		 * Returns the related PHP associative array.
		 * Casting to array because if json_decode will return NULL
		 * the error "Warning: Invalid argument supplied for foreach()"
		 * it is avoided, if you use it. Use of a ternary operator is also possible, instead.
		 */
		$php_response = (array) json_decode($json, true);

		return $php_response;
	}

	/**
	 * Executes a single curl json query, errors are ignored
	 *
	 * @return mixed	False on error, array with results on success
	 */
	protected function perform_json_query($curl_url, $log_error = false)
	{
		$curl_response = $this->perform_curl_query($curl_url, $log_error);
		$curl_response = $curl_response ? preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xef\xbf\xbd", $curl_response) : false;
		$php_response = $curl_response ? json_decode($curl_response, true) : false;

		return $php_response ? $php_response['response'] : false;
	}

	/**
	 * Empties the steam table in the database
	 *
	 * @return void
	 */
	protected function perform_db_reset()
	{
		/* TRUNCATE TABLE */
		switch ($this->db->get_sql_layer())
		{
			case 'sqlite':
			case 'sqlite3':
				$this->db->sql_query('DELETE FROM ' . $this->steam_suite);
			break;

			default:
				$this->db->sql_query('TRUNCATE TABLE ' . $this->steam_suite);
			break;
		}
	}

	/**
	 * Save the obtained information into the database
	 *
	 * @param array $players			Array with detailed data for each player
	 * @return void
	 */
	protected function perform_db_update($players)
	{
		$this->perform_db_reset();

		foreach( $players as $key => $group_member )
		{
			$data = array(
				'steam_id'			=> (isset($group_member['steamid'])) ? (string) $group_member['steamid'] : '0',
				'personaname'		=> (isset($group_member['personaname'])) ? (string) $group_member['personaname'] : '',
				'profileurl'		=> (isset($group_member['profileurl'])) ? (string) $group_member['profileurl'] : '',
				'avatar'			=> (isset($group_member['avatar'])) ? (string) $group_member['avatar'] : '',
				'personastate'		=> (isset($group_member['personastate'])) ? (int) $group_member['personastate'] : 0,
				'profilevisible'	=> (isset($group_member['communityvisibilitystate'])) ? (int) ($group_member['communityvisibilitystate'] == 3) : 0,
				'gameid'			=> (isset($group_member['gameid'])) ? (int) $group_member['gameid'] : 0,
				'gameextrainfo'		=> (isset($group_member['gameextrainfo'])) ? (string) $group_member['gameextrainfo'] : '',
				);

			if ( $data['steam_id'] )
			{
				$sql = 'INSERT INTO ' . $this->steam_suite . ' ' . $this->db->sql_build_array('INSERT', $data);
				$this->db->sql_query($sql);
			}
		}
	}

	/**
	 * Empties the config group data in the database
	 *
	 * @return void
	 */
	protected function perform_config_reset()
	{
		$this->perform_config_update('', array());
	}

	/**
	 * Save the obtained group information into config (and config_text)
	 *
	 * @param array $group_id			Steam ID for the group
	 * @param array $group_details		Array with detailed data for the group
	 * @return void
	 */
	protected function perform_config_update($group_id, $group_details)
	{
		$this->config->set('threedi_steamsuite_group_id', $group_id);
		$this->config->set('threedi_steamsuite_group_name', (isset($group_details['groupName'])) ? $group_details['groupName'] : '');
		$this->config->set('threedi_steamsuite_group_urlname', (isset($group_details['groupURL'])) ? $group_details['groupURL'] : '');
		$this->config->set('threedi_steamsuite_group_url', ($group_id) ? ('http://steamcommunity.com/' . (isset($group_details['groupURL']) ? ('groups/' . $group_details['groupURL']) : ('gid/' . $group_id))) : '');
		$this->config->set('threedi_steamsuite_group_avatar', (isset($group_details['avatarIcon'])) ? $group_details['avatarIcon'] : '');
		$this->config->set('threedi_steamsuite_group_headline', (isset($group_details['headline'])) ? $group_details['headline'] : '');
		$this->config_text->set('threedi_steamsuite_group_description', (isset($group_details['summary'])) ? $group_details['summary'] : '');
	}

	/**
	 * Resets the steam information in the database
	 *
	 * @return void
	 */
	public function steamsuite_reset()
	{
		$this->perform_config_reset();
		$this->perform_db_reset();
	}

	/**
	 * Does the cUrl side of the core engine
	 *
	 * @return void
	 */
	public function steamsuite_refresh()
	{
		/**
		 * Extension enabled in dormant status till
		 * the Web API Keys will be succesfully entered in ACP
		 */
		if ( !$this->check_api_keys() )
		{
			return;
		}

		/* Is the refresh time NOT YET passed? No need to refresh, take a rest */
		if ( !$this->steamsuite_refresh_time() )
		{
			return;
		}

		/**
		 * Are requirements still available? We validated these at extension enable time, but maybe...
		 * If they are not met, the situation is logged, and no further refresh action performed.
		 */
		if ( !$this->steamsuite_check_requirements() )
		{
			return;
		}

		 /**
		 * Here is where the core engine lives
		 *
		 * The refresh time has passed, and we meet the requirement, so we need to update.
		 */

		/* We do set a pseudo time passed value in order to avoid an infinite loop */
		$this->config->set('threedi_steamsuite_ttl_last', time());

		/* First we get the global list of members in the selected group */
		$list_ids_group = array();
		$page = 0;
		do
		{
			/* The list of members of a group may come in several pages */
			$curl_request = "http://steamcommunity.com/gid/{$this->group_id}/memberslistxml/?xml=1" . (($page++) ? "&p=$page" : "");
			if (!$curl_response = $this->perform_xml_query($curl_request, true))
			{
				return;
			}
			if ((int) $curl_response['currentPage'] == 1)
			{
				$group_details = $curl_response['groupDetails'];
			}
			$list_ids_group = array_merge($list_ids_group, $curl_response['members']['steamID64']);
		}
		while ((int) $curl_response['currentPage'] < (int) $curl_response['totalPages']);

		/* Chunking the array of users, due to Steam's API limit (max 100) */
		$list_ids_group_chunks = array_chunk($list_ids_group, 99, true);

		/* Now we flip the original list of ids in the group, to maintain the ordering */
		$list_ids_group = array_flip($list_ids_group);

		$players = array();
		foreach( $list_ids_group_chunks as $key => $chunk )
		{
			/* We got rid of the Web Api's limit of 100 here */
			$steamIds = implode(',', $chunk);

			/* Now we get member details in chunks */
			$curl_request = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=" . $this->api_key . "&steamids=" . $steamIds . "&format=xml";
			if (!$curl_response = $this->perform_xml_query($curl_request, true))
			{
				return;
			}

			foreach ($curl_response['players']['player'] as $player)
			{
				$players[$list_ids_group[$player['steamid']]] = $player;
			}
		}
		ksort($players);

		/* Now we write to the database the received data */
		$this->perform_config_update($this->group_id, $group_details);
		$this->perform_db_update($players);

		/* Memorise the last successful table's update time */
		$this->config->set('threedi_steamsuite_ttl_last_success', time() );
		/**
		 * If we got here is because all communication went well-
		 * So no possible errors: log if previous comm errors
		 */
		$this->check_and_log((int) $this->config['threedi_steamsuite_last_check_in'], true, self::COMMUNICATION_ERROR, 'CURL_COMM');
		$this->config->set('threedi_steamsuite_last_check_in', 0);
	}

	/**
	 * Get the data for all Steam users in the database
	 *
	 * @return array	Array of arrays with all Steam data relevant for a user
	 */
	public function get_steam_users_data()
	{
		$sql = 'SELECT steam_id,
					personaname as steam_name,
					profileurl as steam_profile_url,
					avatar as steam_avatar,
					personastate as steam_state,
					profilevisible as steam_profile_public,
					gameid as steam_game_id,
					gameextrainfo as steam_game_name
			FROM ' . $this->steam_suite . '
			ORDER BY steamsuite_id ASC';
		$result = $this->db->sql_query($sql);

		$steam_users = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);
		return $steam_users;
	}

	/**
	 * Get the statistics for all Steam users in the database
	 *
	 * @return array	Array of arrays with all Steam data relevant for a user
	 */
	public function get_steam_users_statistics()
	{
		$sql = 'SELECT *
			FROM ' . $this->steam_suite . '
			ORDER BY steamsuite_id ASC';
		$result = $this->db->sql_query($sql);
		$total_users = 0;
		/**
		 * Valid states are 0 to 6
		 */
		$state_ary = array(0, 0, 0, 0, 0, 0, 0);
		$game_ary_name = array('');
		$game_ary_count = array(0);

		while($value = $this->db->sql_fetchrow($result))
		{
			$state_ary[$value['personastate']]++;

			if (isset($game_ary_name[$value['gameid']]))
			{
				$game_ary_name[$value['gameid']] = $game_ary_name[$value['gameid']] ?: $value['gameextrainfo'];
				$game_ary_count[$value['gameid']]++;
			}
			else
			{
				$game_ary_name[$value['gameid']] = $value['gameextrainfo'];
				$game_ary_count[$value['gameid']] = 1;
			}

			$total_users++;
		}
		$this->db->sql_freeresult($result);

		$stats_state = $stats_ingame = [];

		/* Sort the associative array in ascending order, according to the key */
		ksort($state_ary);

		foreach ($state_ary as $state_id => $state_count)
		{
			$stats_state[$state_id] =  array(
				'name'		=> $this->user->lang['STEAM_ONLINE'][$state_id],
				'count'		=> $state_count,
			);
		}

		/* First get the users not in game, and remove them from the list */
		$not_in_game_count = $game_ary_count[0];
		$stats_ingame[0] = array(
			'name'		=> $this->user->lang['STEAM_NOT_IN_GAME'],
			'count'		=> $game_ary_count[0],
		);

		unset($game_ary_count[0]);

		/* Sort the associative array in descending order, according to the value */
		arsort($game_ary_count);

		foreach ($game_ary_count as $game_id => $game_count)
		{
			$stats_ingame[$game_id] = array(
				'name'		=> $game_ary_name[$game_id],
				'count'		=> $game_count,
			);
		}

		/* Now let's generate the global statistics */
		$stats_global = array(
			/* Total in group */
			'STEAM_USERS'						=>	(int) $total_users,
			/* All minus offline */
			'STEAM_ONLINE_COUNT'				=>	(int) $total_users - $state_ary[0],
			'STEAM_INGAME_COUNT'				=>	(int) $total_users - $not_in_game_count,
			'STEAMSUITE_REFRESH_LAST_SUCCESS'	=>	$this->user->format_date($this->config['threedi_steamsuite_ttl_last_success']),
			/* Here the group stats begins */
			'U_STEAMSUITE_GROUP_URL'		=>	$this->config['threedi_steamsuite_group_url'],
			'STEAMSUITE_GROUP_NAME'			=>	$this->config['threedi_steamsuite_group_name'],
			'STEAMSUITE_GROUP_URLNAME'		=>	$this->config['threedi_steamsuite_group_urlname'],
			'STEAMSUITE_GROUP_AVATAR'		=>	$this->config['threedi_steamsuite_group_avatar'],
			'STEAMSUITE_GROUP_HEADLINE'		=>	$this->config['threedi_steamsuite_group_headline'],
			'STEAMSUITE_GROUP_DESCRIPTION'	=>	htmlspecialchars_decode($this->config_text->get('threedi_steamsuite_group_description')),
		);

		return [$stats_global, $stats_state, $stats_ingame];
	}

	/**
	 * Get profile data of the user given a steam_id
	 *
	 * @param	string	$steam_id	Steam ID of the user to get data from
	 * @return	array				Array with all the profile data for the user
	 */
	public function get_profile_data($steam_id)
	{
		$sql = 'SELECT steam_id,
					personaname as steam_name,
					profileurl as steam_profile_url,
					avatar as steam_avatar,
					personastate as steam_state,
					profilevisible as steam_profile_public,
					gameid as steam_game_id,
					gameextrainfo as steam_game_name
				FROM ' . $this->steam_suite . '
				WHERE steam_id = ' . $steam_id;
		$result = $this->db->sql_query($sql);
		$value = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		return $value;
	}

	/**
	 * Get steam group information from steam server given a group_id
	 *
	 * @param	string	$group_id	Steam ID of the group to get data from, or vanity url for the group
	 * @param	bool	$is_vanity	Determine if the $steam_id is a proper one, or a vanity url (short group name)
	 * @param	bool	$api_key	Use this API key instead of the stored one (used during setup process)
	 * @return	array|false			Array with all the basic data for the group, false on error
	 */
	public function get_steam_group_info($group_id, $is_vanity = false, $api_key = '')
	{
		$info = false;

		$api_key = $api_key ?: $this->api_key;

		if ($is_vanity)
		{
			$response = $this->perform_json_query("http://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?key={$api_key}&vanityurl={$group_id}&url_type=2");
			if (!$response || !isset($response['success']) || $response['success'] !== 1)
			{
				return false;
			}
			$group_id = $response['steamid'];
		}

		if ($group_id == $this->config['threedi_steamsuite_group_id'])
		{
			$info['groupid'] = $group_id;
			$info['groupname'] = $this->config['threedi_steamsuite_group_name'];
			$info['groupurlname'] = $this->config['threedi_steamsuite_group_urlname'];
			$info['groupurl'] = $this->config['threedi_steamsuite_group_url'];
			$info['groupavatar'] = $this->config['threedi_steamsuite_group_avatar'];
			$info['groupheadline'] = $this->config['threedi_steamsuite_group_headline'];
			$info['groupdescription'] = $this->config_text->get('threedi_steamsuite_group_description');
		}
		else if ($group_data = $this->perform_xml_query("http://steamcommunity.com/gid/{$group_id}/memberslistxml/?xml=1"))
		{
			if (!empty($group_data['groupDetails']) && is_array($group_data['groupDetails']))
			{
				$data = $group_data['groupDetails'];
				$info['groupid'] = $group_id;
				$info['groupname'] = isset($data['groupName']) ? $data['groupName'] : '';
				$info['groupurlname'] = isset($data['groupURL']) ? $data['groupURL'] : '';
				$info['groupurl'] = 'http://steamcommunity.com/' . (!empty($data['groupURL']) ? ('groups/' . $data['groupURL']) : ('gid/' . $group_id));
				$info['groupavatar'] = isset($data['avatarIcon']) ? $data['avatarIcon'] : null;
				$info['groupheadline'] = isset($data['headline']) ? $data['headline'] : null;
				$info['groupdescription'] = isset($data['summary']) ? $data['summary'] : null;
			}
		}

		return $info;
	}

	/**
	 * Get steam user information from steam server given a steam_id
	 *
	 * @param	string	$steam_id	Steam ID of the user to get data from, or vanity url for the user
	 * @param	bool	$is_vanity	Determine if the $steam_id is a proper one, or a vanity url (short user name)
	 * @return	array|false			Array with all the profile data for the user, false on error
	 */
	public function get_steam_user_info($steam_id, $is_vanity = false)
	{
		$info = false;

		if ($is_vanity)
		{
			$response = $this->perform_json_query("http://api.steampowered.com/ISteamUser/ResolveVanityURL/v1/?key={$this->api_key}&vanityurl={$steam_id}");
			if (!$response || !isset($response['success']) || $response['success'] !== 1)
			{
				return false;
			}
			$steam_id = $response['steamid'];
		}

		if ($user_data = $this->perform_json_query("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$this->api_key}&steamids={$steam_id}"))
		{
			if (!empty($user_data['players']) && is_array($user_data['players']))
			{
				$data = $user_data['players'][0];
				$info['steamid'] = $steam_id;
				$info['personaname'] = $data['personaname'];
				$info['profileurl'] = $data['profileurl'];
				$info['avatar'] = $data['avatarmedium'];
			}
		}

		return $info;
	}

	/**
	 * Validate a given steam_id for a user
	 *
	 * @param	string	$steam_id	Steam ID to validate
	 * @param	int		$user_id	User ID of the user to check the steam id for
	 * @return	array				Array with language keys for errors found during validation, or empty if valid
	 */
	public function validate_steam_id($steam_id, $user_id)
	{
		$errors = array();

		/**
		 * Make sure the SteamID64 belongs to the Group's DB
		 */
		$sql = 'SELECT steam_id
			FROM ' . $this->steam_suite . '
			WHERE steam_id = ' . $steam_id;
		$result = $this->db->sql_query($sql);
		if (!$this->db->sql_fetchrow($result))
		{
			$errors[] = 'STEAMSUITE_USER_GROUP_ID_NOT_IN_GROUP';
		}
		$this->db->sql_freeresult($result);

		/**
		 * Make sure the SteamID64 has not been already taken
		 * by some other user.
		 */
		$sql = 'SELECT steam_id, user_id
			FROM ' . USERS_TABLE . '
			WHERE steam_id = ' . $steam_id . '
				AND user_id <> ' . $user_id;
		$result = $this->db->sql_query($sql);
		if ($this->db->sql_fetchrow($result))
		{
			$errors[] = 'STEAMSUITE_USER_GROUP_ID_TAKEN';
		}
		$this->db->sql_freeresult($result);

		return $errors;
	}

	/**
	 * Get diff of given timestamps
	 *
	 * @param	string	$last_success	Unix timestamp last success
	 * @param	string	$last_attempt	Unix timestamp last attempt
	 * @return	string					Formatted diff in HH:MM:SS format
	 */
	public function timegap($last_success, $last_attempt)
	{
		$success = new \DateTime();
		$success = $success->setTimestamp((int) $last_success);
		$attempt = new \DateTime();
		$attempt = $attempt->setTimestamp((int) $last_attempt);
		$gap = $attempt->diff($success);

		return $gap->format('%r %H:%I:%S');
	}

	/**
	 * Returns the URL to user badge's image
	 *
	 * @param	string	$steam_id64		SteamID64
	 * @return	string	$steam_badge	URL to image
	 */
	public function user_badge($steam_id)
	{
		$steam_badge = 'http://badges.steamprofile.com/profile/default/steam/' . $steam_id . '.png';

		return (string) $steam_badge;
	}
}
