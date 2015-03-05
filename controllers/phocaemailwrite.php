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
	
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$app						= JFactory::getApplication();

		$redirect			= 'index.php?option=com_phocaemail&view=phocaemailwrite';
		$post				= JRequest::get('post');
		$post['from']		= JRequest::getVar( 'from', '', 'post', 'string', JREQUEST_NOTRIM );
		$post['fromname']	= JRequest::getVar( 'fromname', '', 'post', 'string', JREQUEST_NOTRIM  );
		$post['to']			= JRequest::getVar( 'to', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['cc']			= JRequest::getVar( 'cc', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$post['bcc']		= JRequest::getVar( 'bcc', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$post['subject']	= JRequest::getVar( 'subject', '', 'post', 'string', JREQUEST_NOTRIM );
		$post['message']	= JRequest::getVar( 'message', '', 'post', 'string', JREQUEST_ALLOWHTML);
		$post['attachment']	= JRequest::getVar( 'attachment', array(), 'post', 'array' );
		
		// Option - can be disabled
		$post['article_name']= JRequest::getVar( 'article_name', JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE'), 'post', 'string', JREQUEST_ALLOWRAW );
		$post['togroups']	= JRequest::getVar( 'togroups', array(), 'post', 'array' );
		$post['tousers']	= JRequest::getVar( 'tousers', array(), 'post', 'array' );
		$post['ccusers']	= JRequest::getVar( 'ccusers', array(), 'post', 'array' );
		$post['bccusers']	= JRequest::getVar( 'bccusers', array(), 'post', 'array' );
		$post['article_id']	= JRequest::getVar( 'article_id', '', 'post', 'int' );
		
		// Different Extensions
		$post['type']		= JRequest::getVar( 'type', '', 'post', 'string' );
		$post['ext']		= JRequest::getVar( 'ext', 'phocaemail', 'post', 'string' );
		
		
		//Add to state (if we are returning back)
		if ($post['ext'] == 'virtuemart') {
			$post['order_id']		= JRequest::getVar( 'order_id', 0, 'post', 'int' );
			$post['delivery_id']	= JRequest::getVar( 'delivery_id', 0, 'post', 'int' );
			$post['ainvoice']		= JRequest::getVar( 'ainvoice', 0, 'post', 'int' );
			$post['adelnote']		= JRequest::getVar( 'adelnote', 0, 'post', 'int' );
			$post['areceipt']		= JRequest::getVar( 'areceipt', 0, 'post', 'int' );
			
			
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
			$app->getUserStateFromRequest( $context.'tousers', 'tousers', $post['tousers'], 'array' );
			$app->getUserStateFromRequest( $context.'ccusers', 'ccusers', $post['ccusers'], 'array' );
			$app->getUserStateFromRequest( $context.'bccusers', 'bccusers', $post['bccusers'], 'array' );
			
			$app->getUserStateFromRequest( $context.'from', 'from', $post['from'], 'string' );
			$app->getUserStateFromRequest( $context.'fromname', 'fromname', $post['fromname'], 'string' );
			$app->getUserStateFromRequest( $context.'to', 'to', $post['to'], 'string' );
			$app->getUserStateFromRequest( $context.'cc', 'cc', $post['cc'], 'string' );
			$app->getUserStateFromRequest( $context.'bcc', 'bcc', $post['bcc'], 'string' );
			$app->getUserStateFromRequest( $context.'subject', 'subject', $post['subject'], 'string' );
			
			// ==========================================================================================
			// Remember the HTML -  GUIDE to edit core file
			// Comment the following line
			$app->getUserStateFromRequest( $context.'message', 'message', $post['message'], 'html' );
			
			// Uncomment the following line
			//$app->getUserStateFromRequest( $context.'message', 'message', $post['message'], 'string', JREQUEST_ALLOWRAW );
			
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
			// $new_state = JRequest::getVar($request, null, 'default', $type);
			// TO:
			// $new_state = JRequest::getVar($request, null, 'default', $type, $mask);
			
			// ==========================================================================================
			
			$app->getUserStateFromRequest( $context.'ext', 'ext', $post['ext'], 'string' );

		}
		
		
		
		
		// Call static function as it can be called by other extensions
		$warning 	= array();
		$error		= array();
		
		$send = PhocaEmailSend::send($post, $warning, $error, $redirect );
		
		if (!$send) {
			// Error will be returned
			$msg = implode('<br />', $warning) . implode('<br />', $error);
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