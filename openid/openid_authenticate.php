<?php
/**
 *
 * OpenID authentication module for phpbb.
 *
 * @copyright (c) 2017, javiexin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace threedi\steamsuite\openid;

require 'lightopenid/openid.php';

class openid_authenticate
{
	/** @var \phpbb\request\request */
	protected $request;

	/* string holding the OpenID provider URL, passed as parameter to the constructor */
	protected $provider;

	/**
	 * Constructor
	 *
	 * @param \phpbb\request\request	$request		Request object
	 * @param string					$provider		URL to the OpenID provider to use for authentication
	 */
	public function __construct(\phpbb\request\request $request, $provider)
	{
		$this->request = $request;
		$this->provider = $provider;
	}

	/**
	 * Authenticates a user through an OpenID provider
	 *
	 * @return string	Empty string after initialization, validated identity after authentication
	 * @throws openid_exception		On operation cancellation, validation failure or communication error
	 */
	public function authenticate()
	{
		$superglobals_disabled = $this->request->super_globals_disabled();
		$this->request->enable_super_globals();
		$id = false;

		try
		{
			$openid = new \LightOpenID(generate_board_url(true));
			if (!$openid->mode)
			{
				if ($this->request->is_set('login'))
				{
					$openid->identity = $this->provider;
					header('Location: ' . $openid->authUrl());
					exit;
				}
			}
			else if ($openid->mode == 'cancel')
			{
				throw new openid_exception('OPENID_AUTH_CANCELED');
			}
			else
			{
				if ($openid->validate())
				{
					$id = $openid->identity;
				}
				else
				{
					throw new openid_exception('OPENID_AUTH_FAILED');
				}
			}
		}
		catch (openid_exception $e)
		{
			if ($superglobals_disabled)
			{
				$this->request->disable_super_globals();
			}
			throw $e;
		}
		catch (ErrorException $e)
		{
			if ($superglobals_disabled)
			{
				$this->request->disable_super_globals();
			}
			throw new openid_exception('OPENID_COMMUNICATION_FAILED', array($e->getMessage()));
		}
		/* Not using finally as it is PHP 5.5 and above only */

		if ($superglobals_disabled)
		{
			$this->request->disable_super_globals();
		}
		return $id;
	}
}
