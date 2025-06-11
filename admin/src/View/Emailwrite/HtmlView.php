<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\Phocaemail\Administrator\View\Emailwrite;

\defined( '_JEXEC' ) or die();

use Joomla\CMS\MVC\View\HtmlView AS BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\EmailHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\UtilsHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\EmailwriteHelper;
use Phoca\Component\Phocaemail\Administrator\View\Adminview\Adminview;
use Joomla\CMS\Editor\Editor;

class HtmlView extends BaseHtmlView
{
	protected $t;
	protected $p;
	protected $r;
	protected $re;
	protected $form;

	function display($tpl = null)
	{
		$this->t	= UtilsHelper::setVars();
		$this->r	= new Adminview();

		$this->form		= $this->get('Form');

		Factory::getApplication()->input->set('hidemainmenu', true);

		$app				= Factory::getApplication();
		$doc 				= Factory::getDocument();
		$user				= Factory::getUser();
		$this->t['path']	= EmailHelper::getPath();
		$params 			= ComponentHelper::getParams('com_phocaemail') ;
		$this->p['display_users_list']		= $params->get('display_users_list', 0);
		$this->p['display_groups_list']		= $params->get('display_groups_list', 0);
		$this->p['display_users_list_cc']	= $params->get('display_users_list_cc', 0);
		$this->p['display_users_list_bcc']	= $params->get('display_users_list_bcc', 0);
		$this->p['display_groups_list_cc']	= $params->get('display_groups_list_cc', 0);
		$this->p['display_groups_list_bcc']	= $params->get('display_groups_list_bcc', 0);
		$this->p['display_select_article']	= $params->get('display_select_article', 0);

	/*	JHtml::_('behavior.modal', 'a.modal');
		$js = "
		function jSelectArticle(id, title, object) {
			/* If the modal window will be refreshed, the object=article will be lost
			   and standard Joomla! id will be set, so correct it *//*
			if (object == 'id') {
				object = 'article';
			}
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			document.getElementById(object + '_name_display').value = title;
			document.getElementById('sbox-window').close();
		}";
		$doc->addScriptDeclaration($js);*/

		$this->t['tmpl'] = $app->input->get( 'tmpl', '', 'get', 'string' );
		if ($this->t['tmpl'] == 'component'){
			$css ='#system-message ul {margin-left: -35px;}
			#system-message dt {display:none;}
			#system-message ul li{
				height:30px;
				font-weight:bold;
				list-style-type:none;
				padding-top: 10px
			}';
			$doc->addStyleDeclaration($css);
		}

		// - - - - - - - - - - -
		// Third Extension
		// - - - - - - - - - - -
		$this->re['ext']	= $app->input->get( 'ext', '', 'get', 'string' );

		if ($this->re['ext'] == 'virtuemart') {
			// - - - - - - - - - -
			// VirtueMart
			$context = 'com_phocaemail.vm.write.';
			$this->re['order_id']		= $app->input->get( 'order_id', '', 'get', 'string' );
			$this->re['delivery_id']	= $app->input->get( 'delivery_id', '', 'get', 'string');
			if (is_file(JPATH_ROOT.'/plugins/phocapdf/virtuemart/virtuemarthelper.php')) {
				require_once(JPATH_ROOT.'/plugins/phocapdf/virtuemart/virtuemarthelper.php');
			} else {
				throw new Exception('Error - Phoca PDF VirtueMart Plugin Helper file could not be found in system', 500);
			}

			$d	= Factory::getApplication()->input->get('request');
			$r	= PhocaPDFVirtueMartHelper::getDeliveryData($d, $this->re['order_id'], $this->re['delivery_id']);

			if($this->re['type'] == 'invoice') {
				$this->t['attachment'][0]['file']		= $this->re['ainvoice'];
				$this->t['attachment'][0]['checked']	= $this->re['ainvoicech'];
				$this->t['attachment'][0]['pdf']		= 1;
			} else if ($this->re['type'] == 'receipt') {
				$this->t['attachment'][0]['file']		= $this->re['areceipt'];
				$this->t['attachment'][0]['checked']	= $this->re['areceiptch'];
				$this->t['attachment'][0]['pdf']		= 1;
			}
			$this->t['attachment'][1]['file']		= $this->re['adelnote'];
			$this->t['attachment'][1]['checked']	= $this->re['adelnotech'];
			$this->t['attachment'][1]['pdf']		= 1;

			$this->p['display_users_list'] 		= 0;
			$this->p['display_users_list_cc'] 	= 0;
			$this->p['display_users_list_bcc'] 	= 0;
			$this->p['display_groups_list_cc'] 	= 0;
			$this->p['display_groups_list_bcc'] 	= 0;
			$this->p['display_select_article'] 	= 0;

		} else {
			// - - - - - - - - - -
			// Common
			$context = 'com_phocaemail.write.';
			$this->re['from'] 		= $app->getUserStateFromRequest( $context.'from', 'from', $user->email, 'string' );
			$this->re['fromname'] 	= $app->getUserStateFromRequest( $context.'fromname', 'fromname', $user->name, 'string' );
			$this->re['to'] 		= $app->getUserStateFromRequest( $context.'to', 'to', '', 'string' );
			$this->re['cc'] 		= $app->getUserStateFromRequest( $context.'cc', 'cc', '', 'string' );
			$this->re['bcc'] 		= $app->getUserStateFromRequest( $context.'bcc', 'bcc', '', 'string' );
			$this->re['subject'] 	= $app->getUserStateFromRequest( $context.'subject', 'subject', '', 'string' );
			$this->re['message'] 	= $app->getUserStateFromRequest( $context.'message', 'message', '', 'string' );
			// Option - can be disabled
			$this->re['article_id']	= $app->getUserStateFromRequest( $context.'article_id', 'article_id', '', 'int' );
			$this->re['article_name']= $app->getUserStateFromRequest( $context.'article_name', 'article_name', Text::_('COM_PHOCAEMAIL_SELECT_ARTICLE'), 'string' );
			$this->re['togroups'] 	= $app->getUserStateFromRequest( $context.'togroups', 'togroups', array(), 'array' );
			$this->re['tousers'] 	= $app->getUserStateFromRequest( $context.'tousers', 'tousers', array(), 'array' );

			$this->re['ccusers'] 	= $app->getUserStateFromRequest( $context.'ccusers', 'ccusers', array(), 'array' );
			$this->re['bccusers'] 	= $app->getUserStateFromRequest( $context.'bccusers', 'bccusers', array(), 'array' );

			$this->re['ccgroups'] 	= $app->getUserStateFromRequest( $context.'ccgroups', 'ccgroups', array(), 'array' );
			$this->re['bccgroups'] 	= $app->getUserStateFromRequest( $context.'bccgroups', 'bccgroups', array(), 'array' );

			$this->t['grouplist'] 		= EmailHelper::groupsList('togroups[]',$this->re['togroups'], '', true );
			$this->t['ccgrouplist'] 	= EmailHelper::groupsList('ccgroups[]',$this->re['ccgroups'], '', true );
			$this->t['bccgrouplist'] 	= EmailHelper::groupsList('bccgroups[]',$this->re['bccgroups'], '', true );
			$this->t['userlist'] 		= EmailHelper::usersList('tousers[]',$this->re['tousers'],1, NULL,'name',0 );
			$this->t['ccuserlist'] 		= EmailHelper::usersList('ccusers[]',$this->re['ccusers'],1, NULL,'name',0 );
			$this->t['bccuserlist'] 	= EmailHelper::usersList('bccusers[]',$this->re['bccusers'],1, NULL,'name',0 );

			$attachment			= Folder::files ($this->t['path']['path_abs_nods'], '.', false, false, array('index.html'));
			if(!empty($attachment)) {
				foreach ($attachment as $key => $value){
					$this->t['attachment'][$key]['file'] 		= $value;
					$this->t['attachment'][$key]['checked'] 	= '';
					$this->t['attachment'][$key]['pdf'] 		= 0;
				}
			}
		}

		$conf = Factory::getConfig();
        $editor = $conf->get('editor');
        $this->t['editor'] = Editor::getInstance($editor);

		$this->addToolbar();
		parent::display($tpl);
	}

	//protected function addToolbar() {
	function addToolbar()
	{
		$canDo	= EmailwriteHelper::getActions();
		ToolbarHelper::title( Text::_( 'COM_PHOCAEMAIL_SEND_EMAIL' ), 'pencil fa-pencil-alt');

		if ($canDo->get('core.admin'))
		{
			ToolbarHelper::custom( 'emailwrite.send', 'envelope', '', 'COM_PHOCAEMAIL_SEND', false);

			ToolbarHelper::cancel( 'emailwrite.cancel', 'COM_PHOCAEMAIL_CANCEL');
			ToolbarHelper::divider();
		}

		ToolbarHelper::help( 'screen.phocaemail', true );
	}
}
