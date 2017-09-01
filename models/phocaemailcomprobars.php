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
//~ echo 'model/comprobars';
class PhocaEmailCpModelPhocaEmailComprobars extends JModelList
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
				'date_unsubscribe', 'a.date_unsubscribe',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'published','a.published',
				'hits', 'a.hits'
				
			);
		}
		parent::__construct($config);
	}
	
	protected function populateState($ordering = NULL, $direction = NULL)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $state);


		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_phocaemail');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.name', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.subscriber_id');

		return parent::getStoreId($id);
	}
	

		
	public function getItems()
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
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
	
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


		
		$query->select('ua.id AS userid, ua.username AS username, ua.name AS usernameno, ua.email AS emailusuario');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.userid');
		
		// Añadimos id de virtuemart
		$query->select('v.virtuemart_userinfo_id AS idVirtuemart,v.name AS namevirtuemart');
		$query->join('LEFT', '#__virtuemart_userinfos AS v ON v.virtuemart_user_id = a.userid');



		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.(int) $access);
		}

		// Filter by published state.
		$published = $this->getState('filter.state');
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
		$orderCol	= $this->state->get('list.ordering', 'title');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		/*if ($orderCol == 'a.ordering' || $orderCol == 'parentcat_title') {
			$orderCol = 'parentcat_title '.$orderDirn.', a.ordering';
		}*/
		$query->order($db->escape($orderCol.' '.$orderDirn));

		//~ echo nl2br(str_replace('#__', 'mw3xj_', $query->__toString()));
		
		
		return $query;
	}
	public function getEmailphNoUsuario() {
		$respuesta = array();
		$query	= $this->getListQuery();
		//$items	= $this->_getList($query, $this->getState('list.start'), $this->getState('list.limit'));

		$items	= $this->_getList($query);
		// Contamos los registros que no tiene usuario asignado.
		$i = 0;
		
		foreach ($items as $item){
			if (isset($item->userid)){
				$respuesta[$i] = $item->email;
				$i++;
			}
		}
		return $respuesta;
		}
	public function getActualizarUsuarios() {
		$respuesta = array();
		// Items que a comprobar que si existen como usuarios.
		$itemsSinusuario = $this->getEmailphNoUsuario();
		$strImSu = '"'.implode('","',$itemsSinusuario).'"';
		$query = "SELECT id,email FROM `#__users` WHERE `email` in (".$strImSu.")";
		
		$idUsuarios = $this->_getList($query);
		if (count($idUsuarios)>0){
			// Quiere decir que si hay usuario de jooomla que no estan registrados en phocanewletters
			$query = "UPDATE `mw3xj_phocaemail_subscribers` SET email=[value-1";
			/* Ejemplo de update que tengo montar... 
			 * ver : https://www.ajimix.net/blog/actualizar-diferentes-filas-en-una-sola-consulta-sql/
			 * */
			
		}
		
		
		
		
		return $idUsuarios;
	} 

}
?>
