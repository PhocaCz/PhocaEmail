<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemailutils.php');
require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemaillists.php');
require_once( JPATH_SITE.'/components/com_phocaemail/helpers/route.php');


class PhocaEmailControllerNewsletter extends JControllerForm
{
	
	function __construct() {
		
		parent::__construct();
		$this->registerTask( 'subscribe', 'subscribe' );
	}

		
	
	function subscribe() {
		
		
			
			
		$app    				= JFactory::getApplication();
		$uri 					= JFactory::getURI();
		$session 				= JFactory::getSession();
		$model 					= $this->getModel('newsletter');
		$email					= $this->input->get('email', '', 'string');
		$name					= $this->input->get('name', '', 'string');
		$privacy				= $this->input->get( 'privacy', false, 'string'  );
		$privacy 				= $privacy ? 1 : 0;
		$mailinglist			= $this->input->get('mailinglist', array(), 'array');
		
		
		$paramsC						= JComponentHelper::getParams('com_phocaemail') ;
		//$this->t['description']					= $this->p->get('description', '');
		$enable_subscription			= $paramsC->get('enable_subscription', 0);
		//$this->t['display_mailing_list'] 		= $this->p->get( 'display_mailing_list', 0 );
		//$this->t['display_subscription_form'] 	= $this->p->get( 'display_subscription_form', 0 );
		$display_name_form 				= $paramsC->get( 'display_name_form', 1 );
		$enable_captcha 				= $paramsC->get( 'enable_captcha', 0 );
		$display_privacy_checkbox_form	= $paramsC->get( 'display_privacy_checkbox_form', 0 );
		$session_suffix					= $paramsC->get( 'session_suffix', '' );
		
		$linkNewsletter			=	PhocaEmailHelperRoute::getNewsletterRoute();
		
		if (!JSession::checkToken()) {
			$msg = JText::_('COM_PHOCAEMAIL_ERROR_WRONG_FORM_DATA');
			$app->enqueueMessage($msg, 'error');
			$app->redirect(JRoute::_($uri));
			return false;
		}
			
		if ($enable_subscription == 0) {
			$msg = JText::_('COM_PHOCAEMAIL_ERROR_SUBSCRIPTION_DISABLED');
			$app->enqueueMessage($msg, 'error');
			$app->redirect(JRoute::_($uri));
			return false;
		}
		
		$namespaceC  = 'pheml' . $session_suffix . 'com';
		$namespaceM  = 'pheml' . $session_suffix . 'mod';
		$validC = $session->get('form_id_com', NULL, $namespaceC);
		$validM = $session->get('form_id_mod', NULL, $namespaceM);
		$session->clear('form_id_com', $namespaceC);
		$session->clear('form_id_mod', $namespaceM);
			
			
		if (!$validC && !$validM) {
			
			$msg = JText::_('COM_PHOCAEMAIL_POSSIBLE_SPAM_DETECTED');
			$app->enqueueMessage($msg, 'error');
			$app->redirect(JRoute::_($uri));
			return false;
		}

		if ( $enable_captcha != 0) {
			$validC  				= PhocaEmailUtils::isReCaptchaValid();
			if (!$validC) {
				$msg = JText::_('COM_PHOCAEMAIL_ERROR_WRONG_CAPTCHA_ADDED');
				$app->enqueueMessage($msg, 'error');
				//$app->redirect(JRoute::_($uri));
				$app->redirect(JRoute::_($linkNewsletter));//Back to component form if enabled
				
				return false;
			}
		}
		
		$error 	= 0;
		$msgA	= array();
		if ( $name == '' && (int)$display_name_form == 2) {
			$msgA[] = JText::_('COM_PHOCAEMAIL_ERROR_NAME_NOT_SET');
			$error = 1;
		}
		
		if ($email == '') {
			$msgA[] = JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_NOT_SET');
			$error = 1;
		}
		
		if ($privacy == 0 && (int)$display_privacy_checkbox_form == 2) {
			$msgA[] = JText::_('COM_PHOCAEMAIL_ERROR_YOU_NEED_TO_AGREE_TO_PRIVACY_TERMS_AND_CONDITIONS');
			$error = 1;
	
		}
			
			
		jimport('joomla.mail.helper');
		if ($email && $email != '' && !JMailHelper::isEmailAddress($email) && $error == 0) {
			$msgA[] = JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_ADDRESS_NOT_VALID');
			$error = 1;
		}
		
		if ($error == 1) {
			$app->enqueueMessage(implode('<br>', $msgA), 'error');
			$app->redirect(JRoute::_($uri));
			return false;
		}
				
				
				
				
		if ($error == 0) {

			$subscribed = $model->storeSubscriber($name, $email, $privacy, $mailinglist);
		
			if ($subscribed) {						
				// Send activation email
				$send = PhocaEmailSendNewsletterEmail::sendNewsLetterEmail($name, $email, 'activate');
				if ($send) {
					$msg = JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CREATED_ACTIVATION_LINK_SENT');
					$app->enqueueMessage($msg, 'success');
					$app->redirect(JRoute::_($uri));
					return true;
				} else {
					$msg = JText::_('COM_PHOCAEMAIL_ERROR_SENDING_EMAIL_PLEASE_CONTACT_ADMINISTRATOR');
					$app->enqueueMessage($msg, 'error');
					$app->redirect(JRoute::_($uri));
					return false;
				}
			} else {
				// Error set in model
			}
		
		}
		
		$msg = JText::_('COM_PHOCAEMAIL_ERROR_WHEN_SUBSCRIBING_NEWSLETTER');
		$app->enqueueMessage($msg, 'error');
		$app->redirect(JRoute::_($uri));
		return false;
	}
	
}
?>
