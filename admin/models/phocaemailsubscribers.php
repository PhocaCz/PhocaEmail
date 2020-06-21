<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined( '_JEXEC' ) or die();
jimport('joomla.application.component.modellist');

class PhocaEmailCpModelPhocaEmailSubscribers extends JModelList
{
	protected	$option 		= 'com_phocaemail';
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'name', 'a.name',
				'email', 'a.email',
				'registered', 'a.registered',
				'active', 'a.active',
				'date', 'a.date',
				'type', 'a.type',
				'date_register', 'a.date_register',
				'date_active', 'a.date_active',
				'date_unsubscribe', 'a.date_unsubscribe',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'published','a.published',
				'mailing_list_title','ml.title',
				'mailing_list_id', 'ml.id',
				'hits', 'a.hits',
				'privacy','a.privacy'

			);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = 'a.date_register', $direction = 'DESC')
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$state = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $state);

		$active = $app->getUserStateFromRequest($this->context.'.filter.active', 'filter_active', '', 'string');
		$this->setState('filter.active', $active);

		$ml = $app->getUserStateFromRequest($this->context.'.filter.mailing_list_id', 'filter_mailing_list_id', '', 'string');
		$this->setState('filter.mailing_list_id', $ml);


		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_phocaemail');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');
		$id	.= ':'.$this->getState('filter.subscriber_id');
		$id	.= ':'.$this->getState('filter.active');
		$id	.= ':'.$this->getState('filter.mailing_list_id');

		return parent::getStoreId($id);
	}



/*	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$query	= $this->getListQuery();
		//$items	= $this->_getList($query, $this->getState('list.start'), $this->getState('list.limit'));

		$items	= $this->_getList($query);

		// Check for a database error.


		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}*/

	protected function getListQuery()
	{

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('`#__phocaemail_subscribers` AS a');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.


		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');



		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');



		$query->select('ua.id AS userid, ua.username AS username, ua.name AS usernameno');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.userid');


		// Join mail list
		$query->select(' GROUP_CONCAT(DISTINCT ml.title) AS mailing_list_title, ml.id AS mailing_list_id');
		$query->join('LEFT', '`#__phocaemail_subscriber_lists` AS sl ON sl.id_subscriber = a.id');
		$query->join('LEFT', '`#__phocaemail_lists` AS ml ON ml.id = sl.id_list');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by published state.
		$actived = $this->getState('filter.active');
		if (is_numeric($actived)) {
			$query->where('a.active = '.(int) $actived);
		}

		// Filter by published state.
		$ml = $this->getState('filter.mailing_list_id');
		if (is_numeric($ml)) {
			$query->where('ml.id = '.(int) $ml);
		}

		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = '.(int) $published);
		}
		else if ($published === '') {
			$query->where('(a.published IN (0, 1))');
		}



		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = ' . $db->quote($language));
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('( a.name LIKE '.$search.' OR a.email LIKE '.$search.')');
			}
		}

		$query->group('a.id');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.date_register');
		$orderDirn	= $this->state->get('list.direction', 'desc');


		/*if ($orderCol == 'a.ordering' || $orderCol == 'parentcat_title') {
			$orderCol = 'parentcat_title '.$orderDirn.', a.ordering';
		}*/
		$query->order($db->escape($orderCol.' '.$orderDirn));

		//echo nl2br(str_replace('#__', 'jos_', $query->__toString()));



		return $query;
	}

}
?>
