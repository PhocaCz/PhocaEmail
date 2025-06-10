<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\phocaemail\Administrator\View\Newsletter;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\HtmlView AS BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Phoca\Component\phocaemail\Administrator\Helper\UtilsHelper;
use Phoca\Component\phocaemail\Administrator\Helper\NewslettersHelper;
use Phoca\Component\phocaemail\Administrator\View\Adminview\Adminview;

class HtmlView extends BaseHtmlView
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;
	protected $r;

	public function display($tpl = null)
	{
		$this->t		= UtilsHelper::setVars('newsletter');
		$this->r 		= new Adminview();
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);
		$user		= Factory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= NewslettersHelper::getActions($this->t, $this->state->get('filter.newsletter_id'));

		$text = $isNew ? Text::_( $this->t['l'] . '_NEW' ) : Text::_($this->t['l'] . '_EDIT');
		ToolbarHelper::title(   Text::_( $this->t['l'] . '_NEWSLETTER' ).': <small><small>[ ' . $text.' ]</small></small>' , 'mail fa-paper-plane');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			ToolbarHelper::apply($this->t['task'].'.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save($this->t['task'].'.save', 'JTOOLBAR_SAVE');
			ToolbarHelper::addNew($this->t['task'].'.save2new', 'JTOOLBAR_SAVE_AND_NEW');
		}

		if (empty($this->item->id))  {
			ToolbarHelper::cancel($this->t['task'].'.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			ToolbarHelper::cancel($this->t['task'].'.cancel', 'JTOOLBAR_CLOSE');
		}

		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}
}