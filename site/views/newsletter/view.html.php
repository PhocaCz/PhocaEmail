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


class PhocaEmailViewNewsletter extends JViewLegacy
{
	protected $t;
	protected $p;

	function display($tpl = null){		
		
		$app					= JFactory::getApplication();
		$this->p 				= $app->getParams();
		$uri 					= JFactory::getURI();
		$document				= JFactory::getDocument();
		$model					= $this->getModel();
		
		$task					= $app->input->get('task', '', 'string');
		// SUBSCRIBE
		$email					= $app->input->get('email', '', 'string');
		$name					= $app->input->get('name', '', 'string');
		$mailinglist			= $app->input->get('mailinglist', array(), 'array');
		// ACTIVATE, SUBSCRIBE
		$uToken					= $app->input->get('u', '', 'string');
		// READ ONLINE
		$nToken					= $app->input->get('n', '', 'string');
		
		
		//$this->t['display_form']	= $this->p->get('display_form', 0);
		$this->t['description']		= $this->p->get('description', '');

		
		$error = 0;
		$this->t['text'] = '';
			
		// ---------
		// SUBSCRIBE
		// ---------
		if ($task == 'subscribe') {
		
			if (!JRequest::checkToken( 'request' )) {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_WRONG_FORM_DATA').'</div>';
				$error = 1;
			}
		
			if ($error == 0) {
				if ( $name == '') {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_NAME_NOT_SET').'</div>';
					$error = 1;
				}
				
				if ($email == '') {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_NOT_SET').'</div>';
					$error = 1;
				}
				
				jimport('joomla.mail.helper');
				if ($email && $email != '' && !JMailHelper::isEmailAddress($email)) {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_ADDRESS_NOT_VALID').'</div>';
					$error = 1;
				}
				
				if ($error == 0) {
		
					$subscribed = $model->storeSubscriber($name, $email, $mailinglist);
				
					if ($subscribed) {						
						// Send activation email
						$send = PhocaEmailSendNewsletterEmail::sendNewsLetterEmail($name, $email, 'activate');
						if ($send) {
							$this->t['text'] =  '<div class="alert alert-success" role="alert">'.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CREATED_ACTIVATION_LINK_SENT').'</div>';
						} else {
							$this->t['text'] =  '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_SENDING_EMAIL_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
						}
					} else {
						// Error set in model
					}
				
				}
			}
		}
		
		// ---------
		// ACTIVATE
		// ---------
		else if ($task == 'activate') {
			if ($uToken != '') {
				$activate = PhocaEmailSendNewsletterEmail::activateUser($uToken);
				
				if ($activate == 1) {
					$this->t['text'] =  '<div class="alert alert-success" role="alert"><h2>'.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CONFIRMED').'</h2><br />'.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CONFIRMED_THANK_YOU_FOR_SUBSCRIBING_TO_OUR_NEWSLETTER').'</div>';
				} else if ($activate == 2) {
					$this->t['text'] =  '<div class="alert alert-warning" role="alert"><h2>'.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CONFIRMED').'</h2><br />'.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_ALREADY_CONFIRMED').'</div>';
				
				} else {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_CONFIRMATION_YOUR_SUBSCRIPTION_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
				
				}
			} else {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_CONFIRMATION_YOUR_SUBSCRIPTION_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
				
			}
		}
		
		// ---------
		// UNSUBSCRIBE
		// ---------
		else if ($task == 'unsubscribe') {
			if ($uToken != '') {
				$unsubscribe = PhocaEmailSendNewsletterEmail::unsubscribeUser($uToken);
				if ($unsubscribe == 1) {
					$this->t['text'] =  '<div class="alert alert-success" role="alert"><h2>'.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CANCELED').'</h2><br />'.JText::_('COM_PHOCAEMAIL_YOU_HAVE_BEEN_UNSUBSCRIBED_FROM_OUR_NEWSLETTER').'</div>';
				} else if ($unsubscribe == 2) {
					$this->t['text'] =  '<div class="alert alert-success" role="alert"><h2>'.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CANCELED').'</h2><br />'.JText::_('COM_PHOCAEMAIL_YOU_HAVE_BEEN_UNSUBSCRIBED_FROM_OUR_NEWSLETTER_CONFIRMATION_SENT_TO_YOUR_EMAIL').'</div>';
				} else if ($unsubscribe == 3) {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_SUBSCRIPTION_NOT_ACTIVE_IN_OUR_NEWSLETTER').'</div>';
				
				} else {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_UNSUBSCRIBING_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
				
				}
			} else {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.JText::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_UNSUBSCRIBING_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
				
			}
		
		}
		// ---------
		// READONLINE
		// ---------
		else if ($task == 'readonline') {
			$newsletter = $model->getNewsletter($nToken);
			if (isset($newsletter->message_html) && $newsletter->message_html != '') {
				$subscriber 	= $model->getSubscriber($uToken);
				$items	= PhocaEmailSendNewsletterEmail::getItems();
				$replace['sitename'] 			= $items['sitename'];
				$replace['subscriptionname'] 	= $items['subscriptionname'];
				if (isset($subscriber->name) && $subscriber->name != '') {
					$replace['name'] 	= $subscriber->name;
				}
				if (isset($subscriber->email) && $subscriber->email != '') {
					$replace['email'] 	= $subscriber->email;
				}
				
				if (isset($subscriber->token) && $subscriber->token != '') {
					$replace['activationlink']		= PhocaEmailHelperRoute::getNewsletterRoute(0, 'activate', $subscriber->token);
					$replace['activationlink'] 		= PhocaEmailSendNewsletterEmail::getRightPathLink($replace['activationlink']);
					$replace['unsubscribelink']		= PhocaEmailHelperRoute::getNewsletterRoute(0, 'unsubscribe', $subscriber->token);
					$replace['unsubscribelink'] 	= PhocaEmailSendNewsletterEmail::getRightPathLink($replace['unsubscribelink']);
				}
				
				if (isset($subscriber->token) && $subscriber->token != '' && isset($newsletter->token) && $newsletter->token != '') {
					
					$replace['readonlinelink']	= PhocaEmailHelperRoute::getNewsletterRoute(0, 'readonline', $subscriber->token, $newsletter->token);
					$replace['readonlinelink'] 	= PhocaEmailSendNewsletterEmail::getRightPathLink($replace['readonlinelink']);
				}
				
				if (isset($newsletter->url) && $newsletter->url != '') {
					$replace['articlelink'] 	= $newsletter->url;
				}
				
				$this->t['text'] = PhocaEmailSendNewsletterEmail::completeMail($newsletter->message_html, $replace);
			} else {
				// No message, no error, no info about it
			}
		
		} else if ($this->t['description'] != '') {
			$this->t['text'] = '<div class="ph-desc" >'.JHTML::_('content.prepare', $this->t['description']).'</div>';			
		}
		
		parent::display($tpl);
	}
	
	protected function _prepareDocument() {
		
		$app			= JFactory::getApplication();
		$menus			= $app->getMenu();
		$menu 			= $menus->getActive();
		$pathway 		= $app->getPathway();
		$title 			= null;
		
	
		if ($menu) {
			 $this->p->def('page_heading',  $this->p->get('page_title', $menu->title));
		} else {
			 $this->p->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		
		  // get page title
          $title =  $this->p->get('page_title', '');
        
          // if still is no title is set take the sitename only
          if (empty($title)) {
             $title = $app->getCfg('sitename');
          }
          // else add the title before or after the sitename
          elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
             $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
          }
          elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
             $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
          }
          $this->document->setTitle($title);

		
		if ( $this->p->get('menu-meta_description', '')) {
			$this->document->setDescription( $this->p->get('menu-meta_description', ''));
		} 

		if ( $this->p->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords',  $this->p->get('menu-meta_keywords', ''));
		}

		if ($app->getCfg('MetaTitle') == '1' &  $this->p->get('menupage_title', '')) {
			$this->document->setMetaData('title',  $this->p->get('page_title', ''));
		}
		
	
	
		
		$pathway->addItem(JText::_('COM_PHOCAEMAIL_NEWSLETTER'));
		
	}
}
?>