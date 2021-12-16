<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\HTML\HTMLHelper;
jimport( 'joomla.application.component.view' );

class PhocaEmailCpViewPhocaEmailInfo extends HtmlView
{
	protected $t;
	protected $r;

	public function display($tpl = null) {
		$this->t	= PhocaEmailUtils::setVars('phocaemailinfo');
		$this->r	= new PhocaEmailRenderAdminView();

		$this->t['component_head'] 	= $this->t['l'].'_PHOCA_EMAIL';
		$this->t['component_links']	= $this->r->getLinks(1);


		$this->t['version'] = PhocaEmailHelper::getPhocaVersion('com_phocaemail');
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/'.$this->t['c'].'cp.php';
		$class	= $this->t['n'] . 'CpHelper';
		$canDo	= $class::getActions($this->t['c']);


		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocaemail" class="btn btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAEMAIL_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAEMAIL_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		ToolbarHelper::title( Text::_($this->t['l'].'_PE_INFO' ), 'info fa-info-circle' );
		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_'.$this->t['c']);
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
?>
