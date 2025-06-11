<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\Phocaemail\Administrator\View\Info;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\HtmlView AS BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\CpHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\EmailHelper;
use Phoca\Component\Phocaemail\Administrator\View\Adminview\Adminview;
use Phoca\Component\Phocaemail\Administrator\Helper\UtilsHelper;

class HtmlView extends BaseHtmlView
{
	protected $t;
	protected $r;

	public function display($tpl = null)
	{
		$this->t	= UtilsHelper::setVars('phocaemailinfo');
		$this->r	= new Adminview();

		$this->t['component_head'] 	= $this->t['l'].'_PHOCA_EMAIL';
		$this->t['component_links']	= $this->r->getLinks(1);


		$this->t['version'] = EmailHelper::getPhocaVersion('com_phocaemail');
		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$canDo	= CpHelper::getActions($this->t['c']);

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocaemail" class="btn btn-primary btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAEMAIL_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAEMAIL_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		ToolbarHelper::title( Text::_($this->t['l'].'_PE_INFO' ), 'info fa-info-circle' );
		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_'.$this->t['c']);
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
