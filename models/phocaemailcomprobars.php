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
	
	
	public function getEmailphUsuario() {
		// Obtenemos array de email de aquellos usuarios que no tiene id de usuario de joomla
		$respuesta = array();
		$query	= $this->getListQuery();
		//$items	= $this->_getList($query, $this->getState('list.start'), $this->getState('list.limit'));

		$items	= $this->_getList($query);
		// Contamos los registros que no tiene usuario asignado.
		$i = 0;
		$x = 0;
		$NoUsuarios = array();
		$Usuarios = array();
		foreach ($items as $item){
			if (!isset($item->userid)){
				$NoUsuarios[$i] = $item->email;
				$i++;
			} else{
				$Usuarios[$x] = $item->userid;
				$x++;
			}
		}
		$respuesta['NoUserid'] = $NoUsuarios;
		$respuesta['UserID'] = $Usuarios;
		return $respuesta;
	}
	public function getResumen() {
		//	Esta funcion es la que utilizamos para contar:
		//  Cuantos de estos no tienen id de tabla user
		// 	Cuantos usuarios hay en la tabla de user.
		$respuesta = array();
		// Obtenemos Email de usuarios de newletter que no tiene iduser
		$usuariosNewsletter = $this->getEmailphUsuario();
		$respuesta['EmailEnvioNoUsuarios'] = $usuariosNewsletter['NoUserid'];
		$respuesta['UsuarioNewCuserid'] = $usuariosNewsletter['UserID'];
		$query = 'SELECT count(id) as cuantos FROM #__users ';
		$cuantos = $this->_getList($query);
		$respuesta['UsuarioJoomla'] = $cuantos[0]->cuantos;
		$cuantos = $this->getObtenerUsuariosLista();
		$respuesta['SuscriptoresLista'] = $cuantos[0]->cuantos;
		return $respuesta;
	}
		
	
	public function getActualizarUsuariosEmail() {
		// El objetivo es cubrir campo userid  de tabla phoca_subscribers ( tablas de registrados en newsletter)
		// Comprobamos que su email exista en la tabla usuarios de joomla (#__users) y obtenemos id para añadir.
		$respuesta = array();
		// Obtenemos los usuarios que no tienen userid y los que tiene
		$usuariosNewsletter = $this->getEmailphUsuario();
		$itemsConUserID = $usuariosNewsletter['UserID'];
		$itemsSinusuario = $usuariosNewsletter['NoUserid'];
		$strImSu = '"'.implode('","',$itemsSinusuario).'"';
		$query = "SELECT n.email as news_email,`date`,u.email as user_email,u.id as id_user 
					FROM `#__phocaemail_subscribers`as n  
					LEFT JOIN `#__users` as u ON u.email=n.email 
					WHERE n.email in (".$strImSu.")";
		//~ echo nl2br(str_replace('#__', 'mw3xj_', $query));
		// VISTA: Con esta consulta obtenemos los mismo registros ($itemsSinusuario) con un campo a mayores
		// llamodo id_user que es id  de la tabla #__users, si los email son iguales , sino muestra null
		$idUsuarios = $this->_getList($query);
		// Montamos consulta para UPDATE y ademas array de usuarios registrados en newsletter pero no 
		// su email no existe en la tabla #__users
		$when = array();
		$where = array();
		$NoExistenUsuario = array();
		$i = 0;
		foreach ($idUsuarios as $idUsuario){
			if ( isset($idUsuario->id_user)) {
			$when[] = 'WHEN "'.$idUsuario->user_email.'" THEN '.$idUsuario->id_user;
			$where[] = $idUsuario->user_email;
			$i ++;
			
			} else {
				// Quiere decir que No se encontro email tabla de usuarios joomla.
				$NoExistenUsuario[] = $idUsuario->news_email;
			}
		}
		if (count($where) >0 ){
			// Solo ejecutamos si hay usuarios para añadir id,sino es excusado... no :-)
			$strNEUsu = '"'.implode('","',$where).'"';
			$query = "UPDATE `#__phocaemail_subscribers` SET userid = CASE email 
					".implode(' 
					',$when). '
					END
					WHERE `email` IN ('.$strNEUsu .')';
					
			
			/* Ejemplo de update que tengo montar... 
			 * ver : https://www.ajimix.net/blog/actualizar-diferentes-filas-en-una-sola-consulta-sql/
			 * */
			$AnhadirId = $this->_getList($query);	
			// Ahora tengo que poner el campo (active) con valor uno a los email que cambie.
			$respuesta['CambioActivo'] = $this->getPonerActive($where);

			// Ahora obtengo IDs para añadir a la lista 
			$ids = $this->getObtenerIds($where);
			// Añado a la lista
			$respuesta['IdsAnhadirLista'] = $this->getAnhadirLista($ids);
		}
		$respuesta['NoEncontrados'] = $NoExistenUsuario;
		$respuesta['IDAnhadido'] = $i;
		// Ahora vamos obtenemos Usuarios que no están en newsletter (max 100),
		$respuesta['UsJoomla'] = $this->getObtenerUsuariosNoLista();
		// Ahora añadimos token a usuarios para añadir uno a uno.
		$respuesta['NuevosUsuarios'] = $this->getAnhadirUsuarios($respuesta['UsJoomla']);
		if ($respuesta['NuevosUsuarios']['Anhadidos'] >0 ){
			// Quiere decir que añadio usuarios a tabla phocaemail_subcripts, por lo debemos obtener ids para añadir lista.
			$ids = $this->getObtenerIds($respuesta['NuevosUsuarios']['email_anhadidos']);
			$respuesta['NuevoIdsAnhadirLista'] = $this->getAnhadirLista($ids);
		}
		
		return $respuesta;
	} 
	
	
	
	
	public function getObtenerUsuariosLista(){
		// Comprobamos que usuarios hay en la lista 1 ( Iniciacion).
		$respuesta = array();
		$query = 'SELECT count(id) as cuantos FROM #__phocaemail_subscriber_lists WHERE `id_list`=1';
		$respuesta = $this->_getList($query);
		return $respuesta;
	}
	
	public function getEliminarUsuariosLista(){
		// Eliminamos usuarios de la lista 1 ( Iniciacion).
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		// delete all custom keys for user 1001.
		$conditions = array(
		$db->quoteName('id_list') . ' = 1'
		);
		$query->delete($db->quoteName('#__phocaemail_subscriber_lists'));
		$query->where($conditions);
		$db->setQuery($query);
		$db->execute();
		$result = $db-> getAffectedRows();
		return $result;
	}
	
	public function getPonerActive($emails){
		// Este es el metodo de joomla... pero me da error.
			//https://docs.joomla.org/Inserting,_Updating_and_Removing_data_using_JDatabase/es
			if (count($emails >0)){
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				// Fields to update.
				$fields = array(
					$db->quoteName('active') . ' = 1 '
					
				);
				// Conditions for which records should be updated.
				$conditions = array();
				$wheres = array();
				foreach ($emails as $email){
					$wheres[] = '"'.$email.'"';
				}
				$conditions [] = $db->quoteName('email') . ' IN ('.implode(',',$wheres).')';
				$query->update($db->quoteName('#__phocaemail_subscribers'))->set($fields)->where($conditions);
				$db->setQuery($query);
				$db->execute();
				//~ $result = $query();;
			} else {
				$result = 0;
			}
			$result = $db-> getAffectedRows();
		return $result;
		}
		

		
	public function getObtenerIds($emails){
		
		// Buscamos los id de tabla __phocaemail_subscriber buscando por emails

		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all records from the user profile table where key begins with "custom.".
		// Order it by the ordering field.
		$query->select(array('id'));
		$query->from($db->quoteName('#__phocaemail_subscribers'));
		$where = array();
		foreach ($emails as $email) {
			$where[] = '(`email` = "'.$email.'")';
			
		}
		$query->where(implode(' OR ',$where));
		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();
		//~ echo '<pre>';
		//~ print_r($query);
		//~ echo '</pre>';
		
		return $results; 
		
	}	
	
	public function getAnhadirLista($ids){
		// Añadimos a la lista subscriptores Iniciacion (1) los id recibidos.
		// https://joomla.stackexchange.com/questions/5661/can-multiple-records-be-inserted-all-at-once-with-the-jdatabase#5665
		// Get a db connection.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = array('id_subscriber','id_list');
		$values = array();
		foreach ($ids as $id){
			$values[] =$id->id.",1";
		}
		// unico valor, a ver que pasa...
		//~ $valor= implode(',',$values);
		$query->insert($db->quoteName('#__phocaemail_subscriber_lists'));
		$query->columns($columns);
		$query->values($values);
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getAffectedRows();
		
		return $num_rows; 
	}
	
	public function getObtenerUsuariosNoLista () {
		// Objetivo obtener:
		// SELECT u.id, u.name, u.email, u.block, p.userid FROM `mw3xj_users` AS u LEFT JOIN `mw3xj_phocaemail_subscribers` AS p ON u.id = p.userid  where  p.userid is null
		// limitamos la consulta a 100 para no saturar servidor.
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select all articles for users who have a username which starts with 'a'.
		// Order it by the created date.
		// Note by putting 'a' as a second parameter will generate `#__content` AS `a`
		$query
			->select(array('u.id','u.name','u.email','u.block','p.userid'))
			->from($db->quoteName('#__users', 'u'))
			->join('LEFT', $db->quoteName('#__phocaemail_subscribers', 'p') . ' ON (' . $db->quoteName('u.id') . ' = ' . $db->quoteName('p.userid') . ')')
			->where($db->quoteName('p.userid') . ' IS NULL')
			->setLimit('100');
		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		// Load the results as a list of stdClass objects (see later for more options on retrieving data).
		$results = $db->loadObjectList();
		
		
		return $results;
			
	}
	
	public function getAnhadirUsuarios ($usuarios){
		// Recibimos objectos de usuarios que no está en newletter.
		// Ejemplo:
		// [id] => 0
		//		[name] => RAMON GUTIERREZ
		// 		[email] => monchuflo@hotmail.com
		//		[block] => 0
		// 		[userid] => 
		$respuesta = array();
		// Conexion : 
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$date = date("Y-m-d H:i:s");
		$columns = array('name','email','userid','token','date','hits','published','active','access');
		$values = array();
		// Email array () es para luego poder buscar ids y poder añadir a lista.
		$emails = array();
		$i = 0;
		foreach ($usuarios as $usuario){
			
				// Quiere decir que no esta bloqueado...por lo que lo añadimos.
				// model phocaemailsubscriber.php obtengo.
				$tokenArray = array('token');
				//~ $manager = new $tokenArray;
				$token = PhocaEmailHelper::getToken($tokenArray);
				$respuesta[] = $token;
				$usuarios[$i]->token = $token;
				$usuarios[$i]->date = date("Y-m-d H:i:s");
				$usuarios[$i]->active = 1;
				$usuarios[$i]->hits = 1;
				if ($usuario->block !== 0){
					$usuarios[$i]->published = 1;
				} else {
					// Quiere decir que el usuario esta bloqueado por no no publicamos este.
					$usuarios[$i]->published = 0;
				}
				$usuarios[$i]->userid = $usuarios[$i]->id;
				unset( $usuarios[$i]->id);
				unset( $usuarios[$i]->block);

				// Ahora montamos los valores.
				$values[] ='"'.$usuarios[$i]->name.'","'.$usuarios[$i]->email.'",'.$usuarios[$i]->userid.',"'
							.$usuarios[$i]->token.'","'.$usuarios[$i]->date.'",'.
							'1,'.$usuarios[$i]->published.',1,1';
				$emails[] = $usuarios[$i]->email;

			
			$i ++;
		}
		
		// Ahora añadimos los datos...
		$query->insert($db->quoteName('#__phocaemail_subscribers'));
		$query->columns($columns);
		$query->values($values);
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getAffectedRows();
		$respuesta['Anhadidos'] = $num_rows;
		$respuesta['email_anhadidos'] = $emails;
	
	return $respuesta;
	}
	
}
?>
