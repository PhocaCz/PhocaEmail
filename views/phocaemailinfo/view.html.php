<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class PhocaEmailCpViewPhocaEmailInfo extends JViewLegacy
{
	protected $t;
	
	public function display($tpl = null) {
		$this->t	= PhocaEmailUtils::setVars();
		JHTML::stylesheet( $this->t['s'] );
		$this->t['version'] = PhocaEmailHelper::getPhocaVersion('com_phocaemail');
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/'.$this->t['c'].'cp.php';
		$class	= $this->t['n'] . 'CpHelper';
		$canDo	= $class::getActions($this->t['c']);

		JToolbarHelper::title( JText::_($this->t['l'].'_PE_INFO' ), 'info' );
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_'.$this->t['c']);
		}
		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = JToolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocaemail" class="btn btn-small"><i class="icon-home-2" title="'.JText::_('COM_PHOCAEMAIL_CONTROL_PANEL').'"></i> '.JText::_('COM_PHOCAEMAIL_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);
		
		JToolbarHelper::divider();
		JToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
?>
