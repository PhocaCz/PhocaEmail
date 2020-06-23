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
jimport( 'joomla.filesystem.folder');
jimport( 'joomla.filesystem.file');

class PhocaEmailCpViewPhocaEmailWrite extends JViewLegacy
{
	protected $t;
	protected $p;
	protected $r;
	protected $re;

	function display($tpl = null) {

		$this->t	= PhocaEmailUtils::setVars();
		$this->r	= new PhocaEmailRenderAdminView();


		JFactory::getApplication()->input->set('hidemainmenu', true);


		$app				= JFactory::getApplication();
		$doc 				= JFactory::getDocument();
		$user				= JFactory::getUser();
		$this->t['path']	= PhocaEmailHelper::getPath();
		$params 			= JComponentHelper::getParams('com_phocaemail') ;
		$this->p['display_users_list']		= $params->get('display_users_list', 0);
		$this->p['display_groups_list']		= $params->get('display_groups_list', 0);
		$this->p['display_users_list_cc']	= $params->get('display_users_list_cc', 0);
		$this->p['display_users_list_bcc']	= $params->get('display_users_list_bcc', 0);
		$this->p['display_groups_list_cc']	= $params->get('display_groups_list_cc', 0);
		$this->p['display_groups_list_bcc']	= $params->get('display_groups_list_bcc', 0);
		$this->p['display_select_article']	= $params->get('display_select_article', 0);

	/*	JHTML::_('behavior.modal', 'a.modal');
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
			if (JFile::exists(JPATH_ROOT.'/plugins/phocapdf/virtuemart/virtuemarthelper.php')) {
				require_once(JPATH_ROOT.'/plugins/phocapdf/virtuemart/virtuemarthelper.php');
			} else {

				throw new Exception('Error - Phoca PDF VirtueMart Plugin Helper file could not be found in system', 500);
				return false;
			}

			$d	= JFactory::getApplication()->input->get('request');
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
			$this->re['to'] 			= $app->getUserStateFromRequest( $context.'to', 'to', '', 'string' );
			$this->re['cc'] 			= $app->getUserStateFromRequest( $context.'cc', 'cc', '', 'string' );
			$this->re['bcc'] 		= $app->getUserStateFromRequest( $context.'bcc', 'bcc', '', 'string' );
			$this->re['subject'] 	= $app->getUserStateFromRequest( $context.'subject', 'subject', '', 'string' );
			$this->re['message'] 	= $app->getUserStateFromRequest( $context.'message', 'message', '', 'string' );
			// Option - can be disabled
			$this->re['article_id']	= $app->getUserStateFromRequest( $context.'article_id', 'article_id', '', 'int' );
			$this->re['article_name']= $app->getUserStateFromRequest( $context.'article_name', 'article_name', JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE'), 'string' );
			$this->re['togroups'] 	= $app->getUserStateFromRequest( $context.'togroups', 'togroups', array(), 'array' );
			$this->re['tousers'] 	= $app->getUserStateFromRequest( $context.'tousers', 'tousers', array(), 'array' );

			$this->re['ccusers'] 	= $app->getUserStateFromRequest( $context.'ccusers', 'ccusers', array(), 'array' );
			$this->re['bccusers'] 	= $app->getUserStateFromRequest( $context.'bccusers', 'bccusers', array(), 'array' );

			$this->re['ccgroups'] 	= $app->getUserStateFromRequest( $context.'ccgroups', 'ccgroups', array(), 'array' );
			$this->re['bccgroups'] 	= $app->getUserStateFromRequest( $context.'bccgroups', 'bccgroups', array(), 'array' );

			$this->t['grouplist'] 		= PhocaEmailHelper::groupsList('togroups[]',$this->re['togroups'], '', true );
			$this->t['ccgrouplist'] 	= PhocaEmailHelper::groupsList('ccgroups[]',$this->re['ccgroups'], '', true );
			$this->t['bccgrouplist'] 	= PhocaEmailHelper::groupsList('bccgroups[]',$this->re['bccgroups'], '', true );
			$this->t['userlist'] 		= PhocaEmailHelper::usersList('tousers[]',$this->re['tousers'],1, NULL,'name',0 );
			$this->t['ccuserlist'] 		= PhocaEmailHelper::usersList('ccusers[]',$this->re['ccusers'],1, NULL,'name',0 );
			$this->t['bccuserlist'] 	= PhocaEmailHelper::usersList('bccusers[]',$this->re['bccusers'],1, NULL,'name',0 );

			$attachment			= JFolder::files ($this->t['path']['path_abs_nods'], '.', false, false, array('index.html'));
			if(!empty($attachment)) {
				foreach ($attachment as $key => $value){
					$this->t['attachment'][$key]['file'] 		= $value;
					$this->t['attachment'][$key]['checked'] 	= '';
					$this->t['attachment'][$key]['pdf'] 		= 0;
				}
			}


		}

		$conf = JFactory::getConfig();
        $editor = $conf->get('editor');
        $this->t['editor'] = \Joomla\CMS\Editor\Editor::getInstance($editor);
		//$this->t['editor'] 	= JFactory::getEditor();

		$this->addToolbar();
		parent::display($tpl);
	}

	//protected function addToolbar() {
	function addToolbar() {
		require_once JPATH_COMPONENT.'/helpers/phocaemailwrite.php';

		$state	= $this->get('State');
		$canDo	= PhocaEmailWriteHelper::getActions();
		JToolbarHelper::title( JText::_( 'COM_PHOCAEMAIL_WRITE' ), 'pencil' );

		if ($canDo->get('core.admin')) {
			//JToolbarHelper::preferences('com_phocaemail');
			JToolbarHelper::custom( 'phocaemailwrite.send', 'envelope', '', 'COM_PHOCAEMAIL_SEND', false);

			JToolbarHelper::cancel( 'phocaemailwrite.cancel', 'COM_PHOCAEMAIL_CANCEL');
			JToolbarHelper::divider();
		}

		JToolbarHelper::help( 'screen.phocaemail', true );
	}
}
?>
