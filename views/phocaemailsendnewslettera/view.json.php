<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view');

class PhocaEmailCpViewPhocaEmailSendNewsletterA extends JViewLegacy
{
	protected $t;
	protected $p;
	protected $state;
	
	function display($tpl = null){
	
		$style = 'style="border-radius: 0;margin: 0;"';
	
		if (!JSession::checkToken('request')) {
			$response = array(
				'status' => '0',
				'error' => '<div class="alert alert-danger" '.$style.'>' . JText::_('JINVALID_TOKEN') . '</div>');
			echo json_encode($response);
			return;
		}
		
		$app				= JFactory::getApplication();
		$this->t['nid']		= $app->input->get( 'newsletterid', '', 'int'  );
		$this->t['sid']		= $app->input->get( 'subscriberid', '', 'int'  );
		
		$db = JFactory::getDBO();
		$app = JFactory::getApplication();
		
	
		// NEWSLETTER
		$query = 'SELECT a.id, a.title, a.subject, a.message, a.message_html, a.url, a.token'
				.' FROM #__phocaemail_newsletters AS a'
			    .' WHERE a.id = '.(int) $this->t['nid']
				.' AND a.published = 1';
		$db->setQuery($query, 0, 1);
		$newsletter = $db->loadAssoc();
	
		if (!empty($newsletter) && isset($newsletter['id']) && $newsletter['id'] > 0 && isset($newsletter['subject']) && isset($newsletter['message_html'])) {
		
		
			if ($newsletter['subject'] == '') {
				$response = array(
				'status' => '0',
				'error' => '<div class="alert alert-danger" '.$style.'>' . JText::_('COM_PHOCAEMAIL_ERROR_SELECTED_NEWSLETTER_DOES_NOT_HAVE_SUBJECT') . '</div>');
				echo json_encode($response);
				return;
			}
			
			if ($newsletter['message_html'] == '') {
				$response = array(
				'status' => '0',
				'error' => '<div class="alert alert-danger" '.$style.'>' . JText::_('COM_PHOCAEMAIL_ERROR_SELECTED_NEWSLETTER_DOES_NOT_INCLUE_CONTENT') . '</div>');
				echo json_encode($response);
				return;
			}
		
			
		}
		
		
		//SUBSCRIBER
		$query = 'SELECT a.id, a.name, a.email, a.token'
				.' FROM #__phocaemail_subscribers AS a'
			    .' WHERE a.id = '.(int) $this->t['sid']
				.' AND a.published = 1 AND a.active = 1';
		$db->setQuery($query, 0, 1);
		$subscriber = $db->loadAssoc();
		
		
		// CHECK THE LISTS
		$query = 'SELECT a.id_list'
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
		}
		
		// if subscriber is not assigned to any list, it can get all of them
		if (empty($sList)) {
			$subscriberList = true;
		}
		// if newsletter is not assigned to any list, it can get all of them
		if (empty($nList)) {
			$subscriberList = true;
		}
		
		if (!$subscriberList) {
			$response = array(
			'status' => '0',
			'error' => '<div class="alert alert-danger" '.$style.'>' . JText::_('COM_PHOCAEMAIL_ERROR_SUBSCRIBER_NOT_ASSIGNED_TO_LIST') . ': '.$subscriber['email'].'</div>');
			echo json_encode($response);
			return;
		}
		// ----
		
		jimport('joomla.mail.helper');
		
		if (!empty($subscriber) && isset($subscriber['id']) && $subscriber['id'] > 0 && isset($subscriber['name']) && isset($subscriber['email']) /*&& $subscriber['name'] != '' // name can be empty */) {
		
			if (!JMailHelper::isEmailAddress($subscriber['email'])) {
				$response = array(
				'status' => '0',
				'error' => '<div class="alert alert-danger" '.$style.'>' . JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_ADDRESS_NOT_VALID') . ': '.$$subscriber['email'].'</div>');
				echo json_encode($response);
				return;
			}
			
			$items	= PhocaEmailSendNewsletterEmail::getItems();
	
			$from 						= $items['from'];
			$fromName					= $items['fromname'];
			$recipient					= $subscriber['email'];
			$subject					= $newsletter['subject'];
			$body						= $newsletter['message_html'];
			$replace['name'] 			= $subscriber['name'];	
			$replace['email'] 			= $subscriber['email'];
			$replace['sitename'] 		= $items['sitename'];
			$replace['subscriptionname']= $items['subscriptionname'];
			
			if (isset($newsletter['url']) && $newsletter['url'] != '' ) {
				$replace['articlelink']	= $newsletter['url'];
			}
			if (isset($newsletter['token']) && $newsletter['token'] != '' ) {
				$replace['readonlinelink']	= PhocaEmailHelperRoute::getNewsletterRoute(0, 'readonline', $subscriber['token'], $newsletter['token']);
				$replace['readonlinelink'] 	= PhocaEmailUtils::getRightPathLink($replace['readonlinelink']);
			}
			
			if (isset($subscriber['token']) && $subscriber['token'] != '' ) {
				$replace['unsubscribelink']	= PhocaEmailHelperRoute::getNewsletterRoute(0, 'unsubscribe', $subscriber['token']);
				$replace['unsubscribelink'] = PhocaEmailUtils::getRightPathLink($replace['unsubscribelink']);
				$replace['activationlink']	= PhocaEmailHelperRoute::getNewsletterRoute(0, 'activate', $subscriber['token']);
				$replace['activationlink'] 	= PhocaEmailUtils::getRightPathLink($replace['activationlink']);
			}
			//$replace['unsubscribelink'] = PhocaEmailSendNewsletterEmail::getEmailLink($subscriber['email'], 'unsubscribe');				
			
			$body = PhocaEmailSendNewsletterEmail::completeMail($body, $replace);
			$subject = PhocaEmailSendNewsletterEmail::completeMail($subject, $replace);
		
		
			$body = PhocaEmailUtils::fixImagesPath($body);
			$body = PhocaEmailUtils::fixLinksPath($body);
			
			$html = '<!DOCTYPE html><html><head>
			<meta http-equiv="content-type" content="text/html; charset=utf-8" />
			<title>'.$subject.'</title></head><body style="padding:0;margin:0;">';
			$html .= $body;
			$html .= '</body></html>';
		
			$mail = JFactory::getMailer();
			//$send = $mail->sendMail($from, $fromName, $recipient, $subject, $body, $mode = false, $cc = null, $bcc = null, $attachment = null, $replyTo = null, $replyToName = null);	
			$send = $mail->sendMail($from, $fromName, $recipient, $subject, $html, true, null, null, null, null, null);	
			
			
			$msgE 	= $app->getMessageQueue();
			$msgEA 	= array();
			$msgET	= '';
			if (!empty($msgE)) {
				foreach($msgE as $k => $v) {
					if (isset($v['message'])) {
						$type = isset($v['type']) ? htmlspecialchars($v['type']) : 'message';
						$msgEA[] = '<div class="alert alert-'.$type.'" '.$style.'>'. $v['message'].'</div>';
						
					}
				}
			}
			if (!empty($msgEA)) {
				$msgET = implode('', $msgEA);
			}
			
			
			if ($send) {
				
				$response = array(
				'status' => '1',
				'message' => $msgET . '<div class="alert alert-success" '.$style.'>'  . JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_SENT') . ' (' . $subscriber['name'].' - '.$subscriber['email'] . ')</div>');
				echo json_encode($response);
				return;
			
			} else {
				$response = array(
				'status' => '0',
				'error' => $msgET . '<div class="alert alert-danger" '.$style.'>' . JText::_('COM_PHOCAEMAIL_ERROR_SENDING_EMAIL_CHECK_EMAIL_SETTINGS') . ': '.$subscriber['email'].'</div>');
				echo json_encode($response);
				return;
			}
		
		} else {
			$response = array(
			'status' => '0',
			'error' => '<div class="alert alert-danger" '.$style.'>' . JText::_('COM_PHOCAEMAIL_ERROR_SUBSCRIBER_NOT_FOUND') . '</div>');
			echo json_encode($response);
			return;
		}
		
		
		
		

			


	
		exit;	
	}
}
?>