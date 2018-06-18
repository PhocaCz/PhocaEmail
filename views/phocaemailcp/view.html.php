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

class PhocaEmailCpViewPhocaEmailCp extends JViewLegacy
{
	protected $t;
	
	function display($tpl = null) {
		
		$this->t	= PhocaEmailUtils::setVars();
		$this->views= array(
		'write'		=> $this->t['l'] . '_SEND_EMAIL',
		'sendnewsletter'		=> $this->t['l'] . '_SEND_NEWSLETTER',
		'newsletters'		=> $this->t['l'] . '_NEWSLETTERS',
		'subscribers'		=> $this->t['l'] . '_SUBSCRIBERS',
		'lists'		=> $this->t['l'] . '_MAILING_LISTS',
		'info'		=> $this->t['l'] . '_INFO'
		);
		
		JHTML::stylesheet( $this->t['s'] );
		JHTML::_('behavior.tooltip');
		$this->t['version'] = PhocaEmailHelper::getPhocaVersion('com_phocaemail');

		$this->addToolbar();
		parent::display($tpl);
		
	}
	
	//protected function addToolbar() {
	function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/phocaemailcp.php';

		$state	= $this->get('State');
		$canDo	= PhocaEmailCpHelper::getActions();
		JToolbarHelper::title( JText::_( 'COM_PHOCAEMAIL_PE_CONTROL_PANEL' ), 'home-2 cpanel' );
		
		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocaemail" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCAEMAIL_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAEMAIL_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_phocaemail');
			JToolbarHelper::divider();
		}
		
		JToolbarHelper::help( 'screen.phocaemail', true );
	}
}
?>