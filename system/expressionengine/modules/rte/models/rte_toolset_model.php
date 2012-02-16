<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Rich Text Editor Module
 *
 * @package		ExpressionEngine
 * @subpackage	Modules
 * @category	Modules
 * @author		Aaron Gustafson
 * @link		http://easy-designs.net
 */
class Rte_toolset_model extends CI_Model {
	
	/**
	 * Get all the Toolsets
	 * 
	 * @access	public
	 * @param	bool $list Whether or not you want it to be a ID => name list
	 * @return	array The tools
	 */
	public function get_all( $list = FALSE )
	{
		$results = $this->db->get_where(
			'rte_toolsets',
			array(
				'member_id'	=> '0', # public only
				'site_id'	=> $this->config->item('site_id')
			)
		)->result_array();
		return $list ? $this->_make_list($results) : $results;
	}
	
	/**
	 * Get enabled Toolsets
	 * 
	 * @access	public
	 * @param	bool $list Whether or not you want it to be a ID => name list
	 * @return	array The tools
	 */
	public function get_active( $list = FALSE )
	{
		$results = $this->db->get_where(
			'rte_toolsets',
			array(
				'member_id'	=> '0', # public only
				'enabled' 	=> 'y',
				'site_id'	=> $this->config->item('site_id')
			)
		)->result_array();
		return $list ? $this->_make_list($results) : $results;
	}
	
	/**
	 * Get the toolsets available to a given Member
	 * 
	 * @access	public
	 * @return	array The tools in ID => name format
	 */
	public function get_member_options()
	{
		# get the toolsets
		$results = $this->db
						->where(
							"
							`site_id` = '{$this->config->item('site_id')}'
							AND
							( `member_id` = '{$this->session->userdata('member_id')}'
							  OR
							  ( `member_id` = '0' AND `enabled` = 'y' ) )
							",
							NULL, FALSE )
						->get('rte_toolsets')
						->result_array();
		# has this user made a personal toolset?
		$has_personal = FALSE;
		foreach ($results as $i => $toolset)
		{
			if ($toolset['member_id'] != 0)
			{
				$has_personal = TRUE;
				# move the personal one to the end of the array & rename it
				$tool = $toolset;
				unset($results[$i]);
				$results[] = $tool;
				break;
			}
		}
		# if no personal toolset, create one
		if ( ! $has_personal)
		{
			$results[] = array(
				'rte_toolset_id'	=> 'new',
				'name'				=> 'my_custom_toolset'
			);
		}
		return $this->_make_list($results);
	}
	
	/**
	 * Ge the tools for the member’s toolset
	 * 
	 * @param	bool
	 * @return	int The ID of the current member’s toolset
	 */
	public function get_member_toolset()
	{
		$result	= $this->db
					->select('rte_toolset_id')
					->get_where(
						'members',
						array( 'member_id' => $this->session->userdata('member_id') ),
						1
					  );
		# member’s choice
		if ($result->num_rows())
		{
			$toolset_id	= $result->row('rte_toolset_id');
		}
		# site default
		else
		{
			$toolset_id	= $this->config->item('rte_default_toolset_id');
		}
		return $toolset_id;
	}
	
	/**
	 * Get the tools for a given toolset
	 * 
	 * @access	public
	 * @param	int $toolset_id The Toolset ID
	 * @return	array A collection of Tool IDs
	 */
	public function get_tools( $toolset_id = 0 )
	{
		$result = $this->db
					->select('rte_tools')
					->get_where(
						'rte_toolsets',
						array( 'rte_toolset_id' => $toolset_id ),
						1
					  );
		return $result->num_rows() ? explode('|', $result->row('rte_tools')) : array();
	}
	
	/**
	 * Check to see if the toolset exists
	 * 
	 * @access	public
	 * @param	int $toolset_id The Toolset ID
	 * @return	bool
	 */
	public function exists( $toolset_id = FALSE )
	{
		$ret = FALSE;
		if ( !! $toolset_id)
		{
			$ret = ($this->db
						->get_where(
							'rte_toolsets',
							array( 'rte_toolset_id' => $toolset_id ),
							1
					 	  )
						->num_rows() > 0);
		}
		return $ret;
	}
	
	/**
	 * Check to see if the current member can access the Toolset
	 * 
	 * @access	public
	 * @param	int $toolset_id The Toolset ID
	 * @return	bool
	 */
	public function member_can_access( $toolset_id = FALSE )
	{
		// are you an admin?
		$admin = ( $this->session->userdata('group_id') == '1' );
		if ( ! $admin)
		{
			# get the group_ids with access
			$result = $this->db
						->select('module_member_groups.group_id')
						->from('module_member_groups')
						->join('modules', 'modules.module_id = module_member_groups.module_id')
						->where('modules.module_name', 'Rte')
						->get();

			if ($result->num_rows())
			{
				foreach ($result->result_array() as $r)
				{
					if ($this->session->userdata('group_id') == $r['group_id'])
					{
						$admin = TRUE;
						break;
					}
				}
			}
		}
		
		# grab the toolset
		$toolset = $this->get($toolset_id);
		
		return (($toolset->member_id != 0 && $toolset->member_id == $this->session->userdata('member_id')) ||
				($toolset->member_id == 0 && $admin));
	}
	
	/**
	 * Get the toolset
	 * 
	 * @access	public
	 * @param	int $toolset_id The Toolset ID
	 * @return	obj The Toolset row object
	 */
	public function get( $toolset_id = FALSE )
	{
		return $this->db
					->get_where(
						'rte_toolsets',
						array( 'rte_toolset_id' => $toolset_id ),
						1
					  )
					->row();
	}
	
	/**
	 * Tells you if a toolset is private
	 * 
	 * @access	public
	 * @param	int $toolset_id The Toolset ID
	 * @return	bool
	 */
	public function is_private( $toolset_id = FALSE )
	{
		return $this->db
					->select('member_id')
					->get_where(
						'rte_toolsets',
						array( 'rte_toolset_id' => $toolset_id ),
						1
					  )
					->row('member_id') != 0;
	}
	
	/**
	 * Save a toolset
	 * 
	 * @access	public
	 * @param	array $toolset The toolset details
	 * @param	int $toolset_id The ID of the Toolset (so you can update)
	 * @return	mixed
	 */
	public function save( $toolset = array(), $toolset_id = FALSE )
	{
		$toolset['site_id'] =  $this->config->item('site_id');
		
		$sql = FALSE;
		
		# update
		if ($toolset_id)
		{
			$existing	= $this->db
							->get_where( 'rte_toolsets', array( 'rte_toolset_id' => $toolset_id ) )
							->result_array();
			foreach ($toolset as $k => $v)
			{
				if ($v != $existing[0][$k])
				{
					$sql = $this->db->update_string( 'rte_toolsets', $toolset, array( 'rte_toolset_id' => $toolset_id ) );
					break;
				}
			}
		}
		# insert
		else
		{
			$sql = $this->db->insert_string( 'rte_toolsets', $toolset );
		}
		
		# run the SQL
		if ($sql)
		{
			$this->db->query($sql);
			return $this->db->affected_rows();
		}
		# fail
		else
		{
			return FALSE;
		}		
	}
	
	/**
	 * Delete a toolset
	 * 
	 * @access	public
	 * @param	int $toolset_id The ID of the toolset to delete
	 * @return	mixed
	 */
	public function delete( $toolset_id = FALSE )
	{
		if ($toolset_id)
		{
			$this->db
				->where( array( 'rte_toolset_id' => $toolset_id ) )
				->delete( 'rte_toolsets' );
			return $this->db->affected_rows();
		}
		return FALSE;
	}
	
	/**
	 * Load the Default Toolsets into the DB
	 * 
	 * @access	public
	 * @return	void
	 */
	public function load_default_toolsets()
	{
		$this->load->model('rte_tool_model');

		# default toolset
		$tool_ids = $this->rte_tool_model->get_tool_ids(array(
			'headings', 'bold', 'italic',
			'blockquote', 'unordered_list', 'ordered_list',
			'link', 'image', 'view_source'
		));
		$this->db->insert(
			'rte_toolsets',
			array(
				'site_id'	=> $this->config->item('site_id'),
				'name'		=> 'Default',
				'rte_tools'	=> implode('|', $tool_ids),
				'enabled'	=> 'y'
			)
		);
	}
	
	/**
	 * Check the name of the toolset for uniqueness
	 * 
	 * @access	public
	 * @param	string $name The Toolset name
	 * @param	int $toolset_id The ID of the toolset (optional)
	 * @return	bool
	 */
	public function check_name( $name, $toolset_id = FALSE )
	{
		$where = array(
			'name'		=> $name,
			'site_id'	=> $this->config->item('site_id')
	 	);
		if ($toolset_id !== FALSE)
		{
			$where['rte_toolset_id !='] = $toolset_id;
		}
		$query = $this->db->get_where('rte_toolsets', $where);
		return ! $query->num_rows();
	}

	/**
	 * Make the results array into an <option>-compatible list
	 * 
	 * @access	private
	 * @param	array $result The result array to convert
	 * @return	array An ID => name array
	 */
	private function _make_list( $result )
	{
		$return = array();
		
		foreach ($result as $r)
		{
			$return[$r['rte_toolset_id']] = $r['name'];
		}
		
		return $return;
	}
	
}
// END CLASS

/* End of file rte_toolset_model.php */
/* Location: ./system/expressionengine/modules/rte/models/rte_toolset_model.php */