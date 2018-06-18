<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport( 'joomla.filesystem.file' );
jimport( 'joomla.filesystem.folder' );

class PhocaEmailCpControllerPhocaEmailWrite extends PhocaEmailCpController
{
	function __construct() {
		parent::__construct();
		$this->registerTask( 'send'  , 'send' );
		$this->registerTask( 'cancel'  , 'cancel' );		
	}

	function cancel($key = NULL) {
		$this->setRedirect( 'index.php?option=com_phocaemail' );
	}
	
	function send () {
	
		JSession::checkToken() or jexit( 'Invalid Token' );
		$app						= JFactory::getApplication();

		$redirect			= 'index.php?option=com_phocaemail&view=phocaemailwrite';
		$post				= array();//JFactory::getApplication()->input->get('post');
		$post['from']		= $app->input->get( 'from', '',  'string');
		$post['fromname']	= $app->input->get( 'fromname', '', 'string' );
		$post['to']			= $app->input->get( 'to', '',  'string' );
		$post['cc']			= $app->input->get( 'cc', '',  'string' );
		$post['bcc']		= $app->input->get( 'bcc', '',  'string');
		$post['subject']	= $app->input->get( 'subject', '', 'string');
		$post['message']	= $app->input->get( 'message', '', 'raw');
		
		$post['attachment']	= $app->input->get( 'attachment', array(),  'array' );
		
		// Option - can be disabled
		$post['article_name']= $app->input->get( 'article_name', JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE'),  'string' );
		$post['togroups']	= $app->input->get( 'togroups', array(), 'array' );
		$post['ccgroups']	= $app->input->get( 'ccgroups', array(),  'array' );
		$post['bccgroups']	= $app->input->get( 'bccgroups', array(),  'array' );
		$post['tousers']	= $app->input->get( 'tousers', array(),  'array' );
		$post['ccusers']	= $app->input->get( 'ccusers', array(),  'array' );
		$post['bccusers']	= $app->input->get( 'bccusers', array(),  'array' );
		$post['article_id']	= $app->input->get( 'article_id', '',  'int' );
		
		// Different Extensions
		$post['type']		= $app->input->get( 'type', '',  'string' );
		$post['ext']		= $app->input->get( 'ext', 'phocaemail',  'string' );
		
		
		//Add to state (if we are returning back)
		if ($post['ext'] == 'virtuemart') {
			$post['order_id']		= $app->input->get( 'order_id', 0,  'int' );
			$post['delivery_id']	= $app->input->get( 'delivery_id', 0,  'int' );
			$post['ainvoice']		= $app->input->get( 'ainvoice', 0,  'int' );
			$post['adelnote']		= $app->input->get( 'adelnote', 0,  'int' );
			$post['areceipt']		= $app->input->get( 'areceipt', 0,  'int' );
			
			
			$context 	= 'com_phocaemail.vm.write.';
			$redirect	= 'index.php?option=com_phocaemail&view=phocaemailwrite&tmpl=component'
		.'&ext=virtuemart&type='.$post['type'].'&order_id='.(int)$post['order_id'].'&delivery_id='.(int)$post['delivery_id'];
			/*$app->getUserStateFromRequest( $context.'ainvoice', 'ainvoice', $post['ainvoice'] );
			$app->getUserStateFromRequest( $context.'adelnote', 'adelnote', $post['adelnote'] );
			$app->getUserStateFromRequest( $context.'areceipt', 'areceipt', $post['areceipt'] );*/
		} else {
			$context 	= 'com_phocaemail.write.';
			$redirect	= 'index.php?option=com_phocaemail&view=phocaemailwrite';
			// Option can be disabled - only for common form
			//$app->getUserStateFromRequest( $context.'article_id', 'article_id', $post['article_id'], 'int' );
			//$app->getUserStateFromRequest( $context.'article_name', 'article_name', $post['article_name'], 'string' );
			
			
			
			$app->getUserStateFromRequest( $context.'togroups', 'togroups', $post['togroups'], 'array' );
			$app->getUserStateFromRequest( $context.'ccgroups', 'ccgroups', $post['ccgroups'], 'array' );
			$app->getUserStateFromRequest( $context.'bccgroups', 'bccgroups', $post['bccgroups'], 'array' );
			$app->getUserStateFromRequest( $context.'tousers', 'tousers', $post['tousers'], 'array' );
			$app->getUserStateFromRequest( $context.'ccusers', 'ccusers', $post['ccusers'], 'array' );
			$app->getUserStateFromRequest( $context.'bccusers', 'bccusers', $post['bccusers'], 'array' );
			
			$app->getUserStateFromRequest( $context.'from', 'from', $post['from'], 'string' );
			$app->getUserStateFromRequest( $context.'fromname', 'fromname', $post['fromname'], 'string' );
			$app->getUserStateFromRequest( $context.'to', 'to', $post['to'], 'string' );
			$app->getUserStateFromRequest( $context.'cc', 'cc', $post['cc'], 'string' );
			$app->getUserStateFromRequest( $context.'bcc', 'bcc', $post['bcc'], 'string' );
			$app->getUserStateFromRequest( $context.'subject', 'subject', $post['subject'], 'string' );
			
			$app->setUserState( $context.'togroups', $post['togroups']);
			$app->setUserState( $context.'ccgroups',  $post['ccgroups']);
			$app->setUserState( $context.'bccgroups', $post['bccgroups']);
			$app->setUserState( $context.'tousers',  $post['tousers']);
			$app->setUserState( $context.'ccusers',  $post['ccusers'] );
			$app->setUserState( $context.'bccusers',  $post['bccusers']);
			
			$app->setUserState( $context.'from', $post['from'] );
			$app->setUserState( $context.'fromname', $post['fromname']);
			$app->setUserState( $context.'to', $post['to']);
			$app->setUserState( $context.'cc', $post['cc']);
			$app->setUserState( $context.'bcc', $post['bcc']);
			$app->setUserState( $context.'subject', $post['subject']);
			
		
			
			// ==========================================================================================
			// Remember the HTML -  GUIDE to edit core file
			// Comment the following line
			$app->getUserStateFromRequest( $context.'message', 'message', $post['message'], 'html' );
			
			// Uncomment the following line
			//$app->getUserStateFromRequest( $context.'message', 'message', $post['message'], 'string' );
			
			// EDIT 
			// file: libraries/joomla/application/application.php
			// line: cca 514
			// FROM:
			// public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
			// TO:
			// public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $mask = 0)
			//
			// line: cca 518
			// FROM:
			// $new_state = $app->input->get($request, null, 'default', $type);
			// TO:
			// $new_state = $app->input->get($request, null, 'default', $type, $mask);
			
			// ==========================================================================================
			
			$app->getUserStateFromRequest( $context.'ext', 'ext', $post['ext'], 'string' );

		}
		
		
		
		
		// Call static function as it can be called by other extensions
		$warning 	= array();
		$error		= array();
		
		$send = PhocaEmailSend::send($post, $warning, $error, $redirect );
		
		if (!$send) {
			// Error will be returned
			$msg = implode('<br />', $warning)  .  implode('<br />', $error);
			$app->enqueueMessage($msg, 'error');
			$app->redirect($redirect);
		} else {
			// Warning will be returned
			$msg = implode('<br />', $warning) . implode('<br />', $error);
			$app->enqueueMessage($msg);
			$app->redirect($redirect);
		}
	}
	
}
// utf-8 test: ä,ö,ü,ř,ž
?>