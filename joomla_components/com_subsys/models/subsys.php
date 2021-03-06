<?php
/**
 * @author Ravi Shakya
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_4
 * @license		GNU/GPL
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

/**
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class SubsysModelSubsys extends JModel
{
	/**
	 *
	 * @var array
	 */
	var $_data;
	
	/**
   * Items total
   * @var integer
   */
  var $_total = null;
 
  /**
   * Pagination object
   * @var object
   */
  var $_pagination = null;
  var $_where = null;
  var $_list = null;
  

function __construct()
  {
 	 parent::__construct();
   $db =& JFactory::getDBO();
	  $mainframe = JFactory::getApplication();
	  global $option;
	
	  $search = $mainframe-> getUserStateFromRequest( $option.'search','search','','string' );
	  $origSearch = $search;
   $search = JString::strtolower( $search );
   
   	// Get the user state
  $filter_order = $mainframe->getUserStateFromRequest($option.'filter_order','filter_order', 'sub_name');
  $filter_order_Dir = $mainframe->getUserStateFromRequest($option.'filter_order_Dir','filter_order_Dir', 'ASC');
 
 //dump($search, "SEARCH");
 
	// Get pagination request variables
	$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
	$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
	$where = array();
    if ( $search ) {
        $where[] = 'sub_name LIKE "%'.$db->getEscaped($search).'%"';
    }   
    $where      = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
 
	// In case limit has been changed, adjust it
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
	$this->setState('limit', $limit);
	$this->setState('limitstart', $limitstart);
	
	// Build the list array for use in the layout
  $lists['order'] = $filter_order;
  $lists['order_Dir'] = $filter_order_Dir;
	
	$lists['search']= $origSearch; 
	$this->_where = $where; 
	$this->_lists = $lists;
  }


	/**
	 * Returns the query
	 * @return string The query to be used to retrieve the rows from the database
	 */
	function _buildQuery()
	{
	 
		 $query = ' SELECT * '
			. ' FROM sms_subscribers ';	
			if($this->_where){ $query .=  $this->_where; }
			$query .= $this-> _buildQueryOrderBy();

//dump($this->_where, "WHERE");
//dump($query, "QUERY");
//print $query;
		return $query;
	}
	
	function _buildQueryOrderBy()
{
  global $mainframe, $option;
  // Array of allowable order fields
  $orders = array('sub_code', 'sub_name', 'sub_address', 'sub_city', 'cdate');

  // Get the order field and direction, default order field
  // is 'ordering', default direction is ascending
  $filter_order = $mainframe->getUserStateFromRequest($option.'filter_order', 'filter_order', 'sub_name');
  $filter_order_Dir = strtoupper($mainframe->getUserStateFromRequest($option.'filter_order_Dir', 'filter_order_Dir', 'ASC'));

  // Validate the order direction, must be ASC or DESC
  if ($filter_order_Dir != 'ASC' && $filter_order_Dir != 'DESC')
  {
    $filter_order_Dir = 'ASC';
  }

  // If order column is unknown use the default
  if (!in_array($filter_order, $orders))
  {
    $filter_order = 'sub_name';
  }
  $orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir;
  if ($filter_order != 'sub_name')
  {
    $orderby .= ' , sub_name ';
  }
  // Return the ORDER BY clause

  return $orderby;
}

	/**
	 * Retrieves the data
	 * @return array Array of objects containing the data from the database
	 */
	function getData()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{
			$query = $this->_buildQuery();	
			$this->_data = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_data;
	}
	
	function getLists(){   
    return $this->_lists;
	}
	
	function getTotal()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_total)) {
 	    $query = $this->_buildQuery();
 	    $this->_total = $this->_getListCount($query);	
 	}
 	return $this->_total;
  }

function getPagination()
  {
 	// Load the content if it doesn't already exist
 	if (empty($this->_pagination)) {
 	    jimport('joomla.html.pagination');
 	    $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
 	}
 	return $this->_pagination;
  }
}
