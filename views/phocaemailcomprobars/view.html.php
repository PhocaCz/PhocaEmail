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
		//~ echo '<pre>';
		//~ print_r($this->t);
		//~ echo '</pre>';
		
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$resumen= array();
		$resumen['totalSubscriptos'] = count($this->items);
		//Para ello tengo que hacer una consulta en que haga la busqueda de aquellos que falte y lo identifique en items.
		$resumen['NoUsuarios'] = $this->get('ContarNoUsuario');
		
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
		
		$this->addToolbar();
		parent::display($tpl);
		
	}
	
	function addToolbar() {
	
		require_once JPATH_COMPONENT.'/helpers/'.$this->t['tasks'].'.php';
		$state	= $this->get('State');
		$class	= ucfirst($this->t['tasks']).'Helper';

		$canDo	= $class::getActions($this->t, $state->get('filter.subscriber_id'));

		JToolBarHelper::title( JText::_( $this->t['l'].'_SUBSCRIBERS' ), 'user' );
	
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew($this->t['task'].'.add','JTOOLBAR_NEW');
		}
	
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList($this->t['task'].'.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {

			JToolBarHelper::divider();
			JToolBarHelper::custom($this->t['tasks'].'.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom($this->t['tasks'].'.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}
	
		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList( $this->t['l'].'_WARNING_DELETE_ITEMS', 'phocaemailsubscribers.delete', $this->t['l'].'_DELETE');
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
