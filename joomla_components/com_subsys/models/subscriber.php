<?php
/**
 * 
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @license		GNU/GPL
 * @author Ravi Shakya
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.model');

/**
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class SubsysModelSubscriber extends JModel
{
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
//dump(JRequest::getVar('cid'), "CID");
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Method to set the identifier
	 *
	 * @access	public
	 * @param	int identifier
	 * @return	void
	 */
	function setId($id)
	{
		// Set id and wipe data
		$this->_id		= $id;
	//	dump($id, "ID passed");
		$this->_data	= null;
	}

	/**
	 * Method to get records
	 * @return object with data
	 */
	function &getData()
	{
		// Load the data
		if (empty( $this->_data )) {
			$query = ' SELECT * FROM sms_subscribers '.
					'  WHERE sub_id = '.$this->_id;
			$this->_db->setQuery( $query );
			$this->_data = $this->_db->loadObject();
		}
		if (!$this->_data) {
			$this->_data = new stdClass();
			$this->_data->sub_id = 0;
			$this->_data->sub_code = null;
			$this->_data->sub_name = null;
			$this->_data->sub_category = null;
			$this->_data->sub_address = null;		
			$this->_data->sub_city = null;
			$this->_data->sub_pobox = null;
			$this->_data->sub_phone = null;
			$this->_data->sub_fax = null;
			$this->_data->sub_email = null;
			$this->_data->sub_web = null;
			$this->_data->sub_cp = null;
			$this->_data->sub_cpd = null;
			$this->_data->sub_cdate = null;
			$this->_data->customer_id = null;
		}
		return $this->_data;
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store()
	{	
		$row =& $this->getTable();
	//	dump($row, "TABLEDATA");

		$data = JRequest::get( 'post' );
	//	dump($data, "POSTDATA");

		// Bind the form fields to the table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
		//	dump($this->_db->getErrorMsg(), "BINDERROR");
			return false;
		}

		// Make sure the  record is valid
		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
		//	dump($this->_db->getErrorMsg() , "VALIDITYERROR");
			return false;
		}

		// Store the web link table to the database
		if (!$row->store()) {
			$this->setError( $row->getErrorMsg() );
			//	dump($row->getErrorMsg() , "DBERROR");
			return false;
		}

		return true;
	}

	/**
	 * Method to delete record(s)
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delete()
	{
		$cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		$row =& $this->getTable();
		
		//dump($cids, "CIDS");

		if (count( $cids )) {
			foreach($cids as $cid) {
				if (!$row->delete( $cid )) {
					$this->setError( $row->getErrorMsg() );
					return false;
				}
			}
		}
		return true;
	}

}
