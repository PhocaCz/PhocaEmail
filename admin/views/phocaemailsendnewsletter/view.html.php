<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
jimport( 'joomla.application.component.view' );
jimport( 'joomla.filesystem.folder');
jimport( 'joomla.filesystem.file');

class PhocaEmailCpViewPhocaEmailSendNewsletter extends HtmlView
{
	protected $t;
	protected $p;
	protected $r;

	function display($tpl = null) {

		$this->t	= PhocaEmailUtils::setVars('sendnewsletter');
		$this->r	= new PhocaEmailRenderAdminView();

		Factory::getApplication()->input->set('hidemainmenu', true);


		$app				= Factory::getApplication();
		$doc 				= Factory::getDocument();
		$user				= Factory::getUser();
		$this->t['path']	= PhocaEmailHelper::getPath();
		$params 			= ComponentHelper::getParams('com_phocaemail') ;

		// GET IDs of Subscribers
	/*	$db = JFactory::getDBO();
		$query = 'SELECT a.id'
				.' FROM #__phocaemail_subscribers AS a'
			    .' WHERE a.published = 1'
				.' AND a.active = 1'
				.' ORDER BY a.id';
		$db->setQuery($query);
		$subscribers = $db->loadColumn();


		$this->t['subscriberlist']	= '';
		if (!empty($subscribers)) {
			$this->t['subscriberlist']	= implode(',', $subscribers);
		}*/

		$newsletterList 	= PhocaEmailHelper::newsletterList();

		$this->t['newsletterlist'] = $newsletterList['genericlist'];//select box

		// CHECK THE LISTS
	/*	$query = 'SELECT a.id_list'
				.' FROM #__phocaemail_subscriber_lists AS a'
			    .' WHERE a.id_subscriber = '.(int) $this->t['sid'];
		$db->setQuery($query);
		$sList = $db->loadColumn();
		$query = 'SELECT a.id_list'
				.' FROM #__phocaemail_newsletter_lists AS a'
			    .' WHERE a.id_newsletter = '.(int) $this->t['nid'];
		$db->setQuery($query);
		$nList = $db->loadColumn();

		$subscriberList = false;
		if (!empty($sList)) {
			foreach($sList as $k => $v) {
				if (!empty($nList) && in_array($v, $nList)) {
					$subscriberList = true;
				}
			}
		}*/

		$js = '';
		if (!empty($newsletterList['list'])) {
			foreach ($newsletterList['list'] as $k => $v) {

				if (isset($v->slist) && $v->slist != '') {
					$js .= 'subscribers['.$v->value.'] = ['.$v->slist.'];'. "\n";
				}

			}
		}
		$this->t['subscribersjs'] = $js;



		$conf = Factory::getConfig();
        $editor = $conf->get('editor');
        $this->t['editor'] = \Joomla\CMS\Editor\Editor::getInstance($editor);
		//$this->t['editor'] 	= JFactory::getEditor();

		$this->addToolbar();
		parent::display($tpl);
	}

	//protected function addToolbar() {
	function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/phocaemailsendnewsletter.php';

		$state	= $this->get('State');
		$canDo	= PhocaEmailSendNewsletterHelper::getActions();
		ToolbarHelper::title( Text::_( 'COM_PHOCAEMAIL_SEND_NEWSLETTER' ), 'pencil fa-pencil-alt' );

		if ($canDo->get('core.admin')) {
			//JToolbarHelper::preferences('com_phocaemail');
			ToolbarHelper::custom( 'phocaemailsendnewsletter.send', 'envelope', '', 'COM_PHOCAEMAIL_SEND', false);
			ToolbarHelper::cancel( 'phocaemailsendnewsletter.cancel', 'COM_PHOCAEMAIL_CANCEL' );
			ToolbarHelper::divider();
		}

		ToolbarHelper::help( 'screen.phocaemail', true );
	}
}
?>
