<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\phocaemail\Administrator\View\Cp;

\defined( '_JEXEC' ) or die();

use Joomla\CMS\MVC\View\HtmlView AS BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Phoca\Component\phocaemail\Administrator\View\Adminview\Adminview;
use Phoca\Component\phocaemail\Administrator\Helper\EmailHelper;
use Phoca\Component\phocaemail\Administrator\Helper\CpHelper;
use Phoca\Component\phocaemail\Administrator\Helper\UtilsHelper;

class HtmlView extends BaseHtmlView
{
	protected $t;
	protected $r;
	protected $views;

	public function display($tpl = null)
	{
		$this->t	= UtilsHelper::setVars('cp');
		$this->r	= new Adminview();

		$i = ' icon-';
		$d = 'duotone ';
		$this->views= array(
			'emailwrite'		=> array($this->t['l'] . '_SEND_EMAIL', $d.$i.'envelope', '#3366cc'),
			'sendnewsletter'	=> array($this->t['l'] . '_SEND_NEWSLETTER', $d.$i.'mass-mail', '#4a33cc'),
			'newsletters'		=> array($this->t['l'] . '_NEWSLETTERS', $d.$i.'components', '#cc3369'),
			'subscribers'		=> array($this->t['l'] . '_SUBSCRIBERS', $d.$i.'groups', '#cc4a33'),
			'emaillists'		=> array($this->t['l'] . '_MAILING_LISTS', $d.$i.'address', '#cc9633'),
			'info'				=> array($this->t['l'] . '_INFO', $d.$i.'info-circle', '#3378cc')
		);

		//JHtml::_('behavior.tooltip');
		$this->t['version'] = EmailHelper::getPhocaVersion('com_phocaemail');

		$this->addToolbar();
		parent::display($tpl);
	}

	function addToolbar() {
		$canDo	= CpHelper::getActions();
		ToolbarHelper::title( Text::_( 'COM_PHOCAEMAIL_PE_CONTROL_PANEL' ), 'home-2 cpanel fa-home' );

		// This button is unnecessary but it is displayed because Joomla! design bug
		$bar = Toolbar::getInstance( 'toolbar' );
		$dhtml = '<a href="index.php?option=com_phocaemail" class="btn btn-primary btn-small"><i class="icon-home-2" title="'.Text::_('COM_PHOCAEMAIL_CONTROL_PANEL').'"></i> '.Text::_('COM_PHOCAEMAIL_CONTROL_PANEL').'</a>';
		$bar->appendButton('Custom', $dhtml);

		if ($canDo->get('core.admin')) {
			ToolbarHelper::preferences('com_phocaemail');
			ToolbarHelper::divider();
		}

		ToolbarHelper::help( 'screen.phocaemail', true );
	}
}