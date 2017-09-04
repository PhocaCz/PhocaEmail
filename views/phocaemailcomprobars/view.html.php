<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
jimport( 'joomla.application.component.view' );
class PhocaEmailCpViewPhocaEmailComprobars extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;

	function display($tpl = null) {
		$this->t			= PhocaEmailUtils::setVars('comprobar');
		// Montamos los link a plantilla vistas interiores.
		
		
		//~ echo '<pre>';
		//~ print_r($this->t);
		//~ echo '</pre>';
		
		$resumen= array();
		//Para ello tengo que hacer una consulta en que haga la busqueda de aquellos que falte y lo identifique en items.
		$resumen = $this->get('Resumen');
		if (isset($_GET['opcion'])){
			if ($_GET['opcion'] === 'actualizarUsuariosEmail'){
				// Pulsaste en Btn Actualizar Usuario Email.
				$resumen['Realizado'] = $_GET['opcion'];
				// Ejecutamos modelo funcion actualizarUsuarios
				$resumen['Respuesta'] = $this->get('ActualizarUsuariosEmail');
				// Ahora debería actualizar el numero usuarios que hay en la lista Subscriptores (iniciacion).

				if (isset($resumen['Respuesta']['IdsAnhadirLista'])){
					$resumen['SuscriptoresLista'] .= $resumen['Respuesta']['IdsAnhadirLista'];
				}
				if (isset($resumen['Respuesta']['NuevoIdsAnhadirLista'])){
					$resumen['SuscriptoresLista'] .= $resumen['Respuesta']['NuevoIdsAnhadirLista'];
				}
				
				
				$resumen['NoUsuarios'] = count($resumen['Respuesta']['NoEncontrados']);
			} 
			if ($_GET['opcion'] === 'eliminaUsuariosLista') {
				$resumen['Realizado'] = $_GET['opcion'];
				// Ejecutamos modelo funcion actualizarUsuarios
				$resumen['EliminadosLista'] = $this->get('EliminarUsuariosLista');
				// Quiere decir que eliminaron usuarios de la lista Inicio, por lo que
				// cambiamos cantidad de Suscriptores de Lista inicio restando los Eliminados.
				// Asi si los Suscriptores de la lista son 0 , se muestra bottones actualizar.
				$resumen['SuscriptoresLista'] = $resumen['SuscriptoresLista'] - $resumen['EliminadosLista'];
				
			} 

		} 
		if ($resumen['SuscriptoresLista'] > 0){
			// Quiere decir que hay registros en Lista Iniciacion, por lo que entonces, debemos mostrar alert
			// Si acaba de realizar la opcion de actualizar , la alerta es una notice.
			if (isset ($_GET['opcion'])){
				if ($_GET['opcion'] === 'actualizarUsuariosEmail'){
					$typeAlerta = 'notice';
				}
			} else {
				$typeAlerta = 'warning';
			}
			JFactory::getApplication()->enqueueMessage('Hay Registros en la lista Iniciacion, antes de eliminarlos enviar el  newsletter de iniciacion', $typeAlerta);
		} else {
			JFactory::getApplication()->enqueueMessage('Sin comprobar email , si existen como usuarios de joomla', 'warning');
			$resumen['SinComprobarUsuarios'] = count($resumen['EmailEnvioNoUsuarios']);
		}
		
		
		
		$this->items		= $this->get('Items');
		
		$resumen['totalSubscriptos'] = count($this->items);

		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		

		$this->resumen = $resumen;
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[0][] = $item->id;
		}

		JHTML::stylesheet( $this->t['s'] );
		$a = 0 ;// Si mostramos botton [Añadir Id de Usuarios de Joomla y Añadir usuarios de Joomla que no esten]
		$c = 1 ;// No se muestra botton [Eliminar Usuarios de Joomla de la lista]

		if ( $resumen['SuscriptoresLista']){
			$a = 1 ;
			$c = 0 ;
		}
		$this->addToolbar($a,$c);
		parent::display($tpl);

	}

	function addToolbar($a=0,$c=0) {

		require_once JPATH_COMPONENT.'/helpers/'.$this->t['tasks'].'.php';
		$state	= $this->get('State');
		$class	= ucfirst($this->t['tasks']).'Helper';

		$canDo	= $class::getActions($this->t, $state->get('filter.subscriber_id'));
		// Titulo de opcion
		
		JToolBarHelper::title( JText::_( $this->t['l'].'_COMPROBAR' ), 'loop' );
		if ( $a == 0 ){
			// Botont de Añadir Id de Usuarios Joomla  si no has subcriptores en la lista iniciacion.
			JToolBarHelper::custom('phocaemailcomprobars.ComprobarUsuarios','loop.png','','Añadir Id y Usuarios de Joomla',false);
		}
		
		if ( $c == 0 ){
			// Botont de Añadir Id de Usuarios Joomla  si no has subcriptores en la lista iniciacion.
			JToolBarHelper::custom('phocaemailcomprobars.EliminaUsuariosLista','purge.png','','Eliminar de lista 1 los Usuarios',false);
		}
		
	
		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.'.$this->t['c'], true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> JText::_('JGRID_HEADING_ORDERING'),
			'a.name' 		=> JText::_($this->t['l'] . '_NAME'),
			'a.email' 		=> JText::_($this->t['l'] . '_EMAIL'),
			'a.date' 		=> JText::_($this->t['l'] . '_SIGN_UP_DATE'),
			'a.date_unsubscribe' 		=> JText::_($this->t['l'] . '_UNSUBSCRIBE_DATE'),
			'a.active' 		=> JText::_($this->t['l'] . '_ACTIVE_USER'),
			'a.hits' 		=> JText::_($this->t['l'] . '_ATTEMPTS'),
			'a.id' 			=> JText::_('JGRID_HEADING_ID')
		);
	}

}
?>
