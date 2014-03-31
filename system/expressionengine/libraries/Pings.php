<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Pings Class
 *
 * @package		ExpressionEngine
 * @subpackage	Control Panel
 * @category	Control Panel
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */

class Pings {

	protected $ping_result;

	/**
	 * Is Registered?
	 *
	 * @return bool
	 **/
	public function is_registered()
	{
		if ( ! IS_CORE && ee()->config->item('license_number') == '')
		{
			return FALSE;
		}

		$cached = ee()->cache->get('software_registration', Cache::GLOBAL_SCOPE);

		if ( ! $cached OR $cached != ee()->config->item('license_number'))
		{
			// restrict the call to certain pages for performance and user experience
			$class = ee()->router->fetch_class();
			$method = ee()->router->fetch_method();

			if ($class == 'homepage' OR ($class == 'admin_system' && $method == 'software_license'))
			{
				$payload = array(
					'username'			=> ee()->config->item('ellislab_username'),
					'license_number'	=> (IS_CORE) ? 'CORE LICENSE' : ee()->config->item('license_number'),
					'domain'			=> ee()->config->item('site_url')
				);

				if ( ! $registration = $this->_do_ping('http://ping.ellislab.com/register.php', $payload))
				{
					// hard fail only when no valid license is entered
					if (ee()->config->item('license_number') == '')
					{
						return FALSE;
					}

					// save the failed request for a day only
					ee()->cache->save('software_registration', ee()->config->item('license_number'), 60*60*24, Cache::GLOBAL_SCOPE);
					return TRUE;
				}
				else
				{
					if ($registration != ee()->config->item('license_number'))
					{
						// may have been a server error, save the failed request for a day
						ee()->cache->save('software_registration', ee()->config->item('license_number'), 60*60*24, Cache::GLOBAL_SCOPE);
					}
					else
					{
						// keep for a week
						ee()->cache->save('software_registration', $registration, 60*60*24*7, Cache::GLOBAL_SCOPE);
					}
				}

			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * EE Version Check function
	 *
	 * Checks the current version of ExpressionEngine available from EllisLab
	 *
	 * @access	private
	 * @return	string
	 */
	public function get_version_info()
	{
		// Attempt to grab the local cached file
		$cached = ee()->cache->get('current_version', Cache::GLOBAL_SCOPE);

		if ( ! $cached)
		{
			$version_file = array();

			if ( ! $version_info = $this->_do_ping('http://versions.ellislab.com/versions_ee2.txt'))
			{
				$version_file['error'] = TRUE;
			}
			else
			{
				$version_info = explode("\n", trim($version_info));

				if (empty($version_info))
				{
					$version_file['error'] = TRUE;
				}
				else
				{
					foreach ($version_info as $version)
					{
						$version_file[] = explode('|', $version);
					}
				}
			}

			// Cache version information for a day
			ee()->cache->save(
				'current_version',
				$version_file,
				60 * 60 * 24,
				Cache::GLOBAL_SCOPE
			);
		}
		else
		{
			$version_file = $cached;
		}

		// one final check for good measure
		if ( ! $this->_is_valid_version_file($version_file))
		{
			return FALSE;
		}

		if (isset($version_file['error']) && $version_file['error'] == TRUE)
		{
			return FALSE;
		}

		return $version_file;
	}

	// --------------------------------------------------------------------

	/**
	 * Validate version file
	 * Prototype:
	 *  0 =>
	 *    array
	 *      0 => string '2.1.0' (length=5)
	 *      1 => string '20100805' (length=8)
	 *      2 => string 'normal' (length=6)
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _is_valid_version_file($version_file)
	{
		if ( ! is_array($version_file))
		{
			return FALSE;
		}

		foreach ($version_file as $version)
		{
			if ( ! is_array($version) OR count($version) != 3)
			{
				return FALSE;
			}

			foreach ($version as $val)
			{
				if ( ! is_string($val))
				{
					return FALSE;
				}
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Do the Ping
	 *
	 * @param string	The URL to ping
	 * @param array		The POST payload, if any
	 * @return bool
	 **/
	private function _do_ping($url, $payload = null)
	{
		$target = parse_url($url);

		$fp = @fsockopen($target['host'], 80, $errno, $errstr, 3);

		if ( ! $fp)
		{
			return FALSE;
		}

		if ( ! empty($payload))
		{
			$postdata = http_build_query($payload);

			fputs($fp, "POST {$target['path']} HTTP/1.1\r\n");
			fputs($fp, "Host: {$target['host']}\r\n");
			fputs($fp, "User-Agent: EE/EllisLab PHP/\r\n");
			fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-Length: ".strlen($postdata)."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, "{$postdata}\r\n\r\n");
		}
		else
		{
			fputs($fp,"GET {$url} HTTP/1.1\r\n" );
			fputs($fp,"Host: {$target['host']}\r\n");
			fputs($fp,"User-Agent: EE/EllisLab PHP/\r\n");
			fputs($fp,"If-Modified-Since: Fri, 01 Jan 2004 12:24:04\r\n");
			fputs($fp,"Connection: close\r\n\r\n");
		}

		$headers = TRUE;
		$response = '';
		while ( ! feof($fp))
		{
			$line = fgets($fp, 4096);

			if ($headers === FALSE)
			{
				$response .= $line;
			}
			elseif (trim($line) == '')
			{
				$headers = FALSE;
			}
		}

		fclose($fp);

		return $response;
	}

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file Pings.php */
/* Location: ./system/expressionengine/libraries/Pings.php */
