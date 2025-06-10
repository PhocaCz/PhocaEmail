<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\phocaemail\Administrator\View\Emaillists;

\defined( '_JEXEC' ) or die();

use Joomla\CMS\MVC\View\HtmlView AS BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Phoca\Component\phocaemail\Administrator\Helper\UtilsHelper;
use Phoca\Component\phocaemail\Administrator\View\Adminviews\Adminviews;
use Phoca\Component\phocaemail\Administrator\Helper\EmaillistsHelper;

class HtmlView extends BaseHtmlView
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $t;
	protected $r;
	public $filterForm;
    public $activeFilters;

	function display($tpl = null) {

		$this->t			= UtilsHelper::setVars('emaillist');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->filterForm   = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors), 500);
		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[0][] = $item->id;
		}

		$this->r = new Adminviews();


		$this->addToolbar();
		parent::display($tpl);

	}

	function addToolbar()
	{
		$state	= $this->get('State');
		$canDo	= EmaillistsHelper::getActions($this->t, $state->get('filter.subscriber_id'));

		ToolbarHelper::title( Text::_( $this->t['l'].'_MAILING_LISTS' ), 'list fa-list' );

		if ($canDo->get('core.create')) {
			ToolbarHelper::addNew($this->t['task'].'.add','JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			ToolbarHelper::editList($this->t['task'].'.edit','JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.edit.state')) {

			ToolbarHelper::divider();
			ToolbarHelper::custom($this->t['tasks'].'.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			ToolbarHelper::custom($this->t['tasks'].'.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
		}

		if ($canDo->get('core.delete')) {
			ToolbarHelper::deleteList( $this->t['l'].'_WARNING_DELETE_ITEMS', 'phocaemaillists.delete', $this->t['l'].'_DELETE');
		}
		ToolbarHelper::divider();
		ToolbarHelper::help( 'screen.'.$this->t['c'], true );
	}

	protected function getSortFields() {
		return array(
			'a.ordering'	=> Text::_('JGRID_HEADING_ORDERING'),
			'a.title' 		=> Text::_($this->t['l'] . '_TITLE'),
			'a.published' 		=> Text::_($this->t['l'] . '_PUBLISHED'),
			'a.id' 			=> Text::_('JGRID_HEADING_ID')
		);
	}
}