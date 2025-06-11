<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\Phocaemail\Site\View\Newsletter;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\View\HtmlView AS BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
//use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\MailHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\SendnewsletteremailHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\UtilsHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\EmaillistsHelper;
use Phoca\Component\Phocaemail\Site\Helper\RouteHelper;

class HtmlView extends BaseHtmlView
{
	protected $t;
	protected $p;

	function display($tpl = null){

//		$lang = Factory::getLanguage();

		$app					= Factory::getApplication();
		$this->p 				= $app->getParams();
//		$uri 					= Uri::getInstance();
//		$document				= Factory::getDocument();
		$model					= $this->getModel();
//		$session				= Factory::getSession();
		$task					= $app->input->get('task', '', 'string');
		// SUBSCRIBE - controller
	/*	$email					= $app->input->get('email', '', 'string');
		$name					= $app->input->get('name', '', 'string');
		$privacy				= $app->input->post->get( 'privacy', false, 'string'  );

		$privacy 				= $privacy ? 1 : 0;

		$mailinglist			= $app->input->get('mailinglist', array(), 'array');*/
		// ACTIVATE, SUBSCRIBE
		$uToken					= $app->input->get('u', '', 'string');
		// READ ONLINE
		$nToken					= $app->input->get('n', '', 'string');

		//$this->t['display_form']					= $this->p->get('display_form', 0);
		$this->t['description']						= $this->p->get('description', '');
		$this->t['enable_subscription']				= $this->p->get('enable_subscription', 0);
		$this->t['display_mailing_list'] 			= $this->p->get('display_mailing_list', 0 );
		$this->t['display_subscription_form'] 		= $this->p->get('display_subscription_form', 0 );
		$this->t['display_name_form'] 				= $this->p->get('display_name_form', 1 );
		$this->t['enable_captcha'] 					= $this->p->get('enable_captcha', 0 );
		$this->t['session_suffix'] 					= $this->p->get('session_suffix', '' );
		$this->t['display_privacy_checkbox_form']	= $this->p->get('display_privacy_checkbox_form', 0 );

		// We got the email from module
		// In module we want to display only email field and subscribe button but we want to redirect this
		// to newsletter where we want to get more info like Name, or we want to use recaptcha
		// This is still no storing email, only displaying it in newsletter form view
		$this->t['email_value'] = '';
		if (Session::checkToken('request')) {

			$email = $app->input->get('email', '', 'string');
			if ($email != '' && MailHelper::isEmailAddress($email)) {
				$this->t['email_value'] = $email;
			}
		}

		$error = 0;
		$this->t['text'] = '';

		// ---------
		// SUBSCRIBE
		// ---------
		if ($task == 'subscribe') {

			// SET IN CONTROLLER
		/*	// SESSION
			if (!Session::checkToken( 'request' )) {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_WRONG_FORM_DATA').'</div>';
				$error = 1;
			}

			// ENABLED SUBSCRIPTION
			if ($this->t['enable_subscription'] == 0 && $error == 0) {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_SUBSCRIPTION_DISABLED').'</div>';
				$error = 1;
			}

			// CAPTCHA
			if ( $this->t['enable_captcha'] != 0 && $error == 0) {
				$validC  				= PhocaEmailUtils::isReCaptchaValid();
				if (!$validC) {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_WRONG_CAPTCHA_ADDED').'</div>';
					$error = 1;
				}
			}

			// FORM FIELDS NAME EMAIL PRIVACY
			if ($error == 0) {

				$this->t['text'] = '';
				if ( $name == '' && (int)$this->t['display_name_form'] == 2) {
					$this->t['text'] .= '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_NAME_NOT_SET').'</div>';
					$error = 1;
				}

				if ($email == '') {
					$this->t['text'] .= '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_EMAIL_NOT_SET').'</div>';
					$error = 1;
				} else {

					jimport('joomla.mail.helper');
					if ($email && $email != '' && !MailHelper::isEmailAddress($email)) {
						$this->t['text'] .= '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_EMAIL_ADDRESS_NOT_VALID').'</div>';
						$error = 1;
					}
				}

				if ($privacy == 0 && (int)$this->t['display_privacy_checkbox_form'] == 2) {
					$this->t['text'] .= '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_YOU_NEED_TO_AGREE_TO_PRIVACY_TERMS_AND_CONDITIONS').'</div>';
					$error = 1;

				}
			}

			if ($error == 0) {

				$subscribed = $model->storeSubscriber($name, $email, $privacy, $mailinglist);

				if ($subscribed) {
					// Send activation email
					$send = PhocaEmailSendNewsletterEmail::sendNewsLetterEmail($name, $email, 'activate');
					if ($send) {
						$this->t['text'] =  '<div class="alert alert-success" role="alert">'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_CREATED_ACTIVATION_LINK_SENT').'</div>';
					} else {
						$this->t['text'] =  '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_SENDING_EMAIL_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
					}
				} else {
					// Error set in model
				}

			}*/

		    // Display Messages below header
            $this->t['text'] = '';
		    $messages = $app->getMessageQueue(true);// True will remove all messages from queue

            if (!empty($messages)) {
                foreach($messages as $k => $v) {

                    $attributes = '';
                    if (isset($v['type']) && $v['type'] != '') {

                        switch($v['type']){
                            case 'error':
                                $attributes = 'class="alert alert-error alert-danger" role="alert"';
                            break;
                            case 'notice':
                                $attributes = 'class="alert alert-warning" role="alert"';
                            break;
                            case 'success':
                                $attributes = 'class="alert alert-success" role="alert"';
                            break;
                            default:
                                $attributes = 'class="alert alert-info" role="alert"';
                            break;
                        }

                    }
                    if (isset($v['type']) && $v['type'] != '') {
                        $this->t['text'] .= '<div '.$attributes.'>'.$v['message'].'</div>';
                    }
                }
            }
		}

		// ---------
		// ACTIVATE
		// ---------
		else if ($task == 'activate') {

			if ($this->t['enable_subscription'] == 0) {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_SUBSCRIPTION_DISABLED').'</div>';
				$error = 1;
			} else {

				if ($uToken != '') {
					$activate = SendnewsletteremailHelper::activateUser($uToken);

					if ($activate == 1) {
						$this->t['text'] =  '<div class="alert alert-success" role="alert"><h2>'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_CONFIRMED').'</h2><br />'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_CONFIRMED_THANK_YOU_FOR_SUBSCRIBING_TO_OUR_NEWSLETTER').'</div>';
					} else if ($activate == 2) {
						$this->t['text'] =  '<div class="alert alert-warning" role="alert"><h2>'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_CONFIRMED').'</h2><br />'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_ALREADY_CONFIRMED').'</div>';
					} else {
						$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_CONFIRMATION_YOUR_SUBSCRIPTION_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
					}
				} else {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_CONFIRMATION_YOUR_SUBSCRIPTION_PLEASE_CONTACT_ADMINISTRATOR').'</div>';
				}
			}
		}

		// ---------
		// UNSUBSCRIBE
		// ---------
		else if ($task == 'unsubscribe') {

			if ($this->t['enable_subscription'] == 0) {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_SUBSCRIPTION_DISABLED').'</div>';
				$error = 1;
			} else {

				if ($uToken != '') {
					$unsubscribe = SendnewsletteremailHelper::unsubscribeUser($uToken);
					if ($unsubscribe == 1) {

						$this->t['text'] =  '<div class="alert alert-success" role="alert"><h2>'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_CANCELED').'</h2><br />'.Text::_('COM_PHOCAEMAIL_YOU_HAVE_BEEN_UNSUBSCRIBED_FROM_OUR_NEWSLETTER').'</div>';

					} else if ($unsubscribe == 2) {

						$this->t['text'] =  '<div class="alert alert-success" role="alert"><h2>'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_CANCELED').'</h2><br />'.Text::_('COM_PHOCAEMAIL_YOU_HAVE_BEEN_UNSUBSCRIBED_FROM_OUR_NEWSLETTER_CONFIRMATION_SENT_TO_YOUR_EMAIL').'</div>';

					} else if ($unsubscribe == 3) {

						$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_EMAIL_SUBSCRIPTION_NOT_ACTIVE_IN_OUR_NEWSLETTER').'</div>';

					} else if ($unsubscribe == 4) {

						$this->t['text'] =  '<div class="alert alert-success" role="alert"><h2>'.Text::_('COM_PHOCAEMAIL_SUBSCRIPTION_CANCELED').'</h2><br />'.Text::_('COM_PHOCAEMAIL_YOU_HAVE_BEEN_UNSUBSCRIBED_FROM_OUR_NEWSLETTER').'<br />'.Text::_('COM_PHOCAEMAIL_ALL_YOUR_PERSONAL_DATA_HAS_BEEN_DELETED').'</div>';

					} else {
						$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_UNSUBSCRIBING_PLEASE_CONTACT_ADMINISTRATOR').'</div>';

					}
				} else {
					$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_OCCURRED_DURING_UNSUBSCRIBING_PLEASE_CONTACT_ADMINISTRATOR').'</div>';

				}
			}
		}
		// ---------
		// READONLINE
		// ---------
		else if ($task == 'readonline') {
			$newsletter = $model->getNewsletter($nToken);
			if (isset($newsletter->message_html) && $newsletter->message_html != '') {
				$subscriber 	= $model->getSubscriber($uToken);
				$items	= SendnewsletteremailHelper::getItems();
				$replace['sitename'] 			= $items['sitename'];
				$replace['subscriptionname'] 	= $items['subscriptionname'];
				if (isset($subscriber->name) && $subscriber->name != '') {
					$replace['name'] 	= $subscriber->name;
				}
				if (isset($subscriber->email) && $subscriber->email != '') {
					$replace['email'] 	= $subscriber->email;
				}

				if (isset($subscriber->token) && $subscriber->token != '') {
					$replace['activationlink']		= RouteHelper::getNewsletterRoute(0, 'activate', $subscriber->token);
					$replace['activationlink'] 		= UtilsHelper::getRightPathLink($replace['activationlink']);
					$replace['unsubscribelink']		= RouteHelper::getNewsletterRoute(0, 'unsubscribe', $subscriber->token);
					$replace['unsubscribelink'] 	= UtilsHelper::getRightPathLink($replace['unsubscribelink']);
				}

				if (isset($subscriber->token) && $subscriber->token != '' && isset($newsletter->token) && $newsletter->token != '') {

					$replace['readonlinelink']	= RouteHelper::getNewsletterRoute(0, 'readonline', $subscriber->token, $newsletter->token);
					$replace['readonlinelink'] 	= UtilsHelper::getRightPathLink($replace['readonlinelink']);
				}

				if (isset($newsletter->url) && $newsletter->url != '') {
					$replace['articlelink'] 	= $newsletter->url;
				}

				$this->t['text'] = SendnewsletteremailHelper::completeMail($newsletter->message_html, $replace);
			} else {
				// No message, no error, no info about it
			}

		}
		// ---------
		// NO TASK - DEFAULT VIEW - NEWSLETTER FORM
		// ---------
		else {

//			$tpl = 'form';

			if ($this->t['enable_subscription'] == 0) {
				$this->t['text'] = '<div class="alert alert-error alert-danger" role="alert">'.Text::_('COM_PHOCAEMAIL_ERROR_SUBSCRIPTION_DISABLED').'</div>';
				$error = 1;
			} else {
				$this->t['mailing_list'] = array();
				if ($this->t['display_mailing_list'] == 1) {
					$this->t['mailing_list'] = EmaillistsHelper::options();
				}
			}

			// Newsletter FORM is default view
            // ONLY active in form is active - not in tasks like subscribe, unsubscribe, activate, readonline
            if ($this->t['description'] != '') {
                $this->t['text'] .= '<div class="ph-desc" >'.HTMLHelper::_('content.prepare', $this->t['description']).'</div>';
            }

		}

		parent::display($tpl);
	}

	protected function _prepareDocument()
	{
		$app			= Factory::getApplication();
		$menus			= $app->getMenu();
		$menu 			= $menus->getActive();
		$pathway 		= $app->getPathway();

		if ($menu) {
			 $this->p->def('page_heading',  $this->p->get('page_title', $menu->title));
		} else {
			 $this->p->def('page_heading', Text::_('JGLOBAL_ARTICLES'));
		}

		// get page title
		$title =  $this->p->get('page_title', '');

		// if still is no title is set take the sitename only
		if (empty($title)) {
			$title = $app->get('sitename');
		}
		// else add the title before or after the sitename
		elseif ($app->get('sitename_pagetitles', 0) == 1) {
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2) {
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
		$this->document->setTitle($title);

		if ( $this->p->get('menu-meta_description', '')) {
			$this->document->setDescription( $this->p->get('menu-meta_description', ''));
		}

		if ( $this->p->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords',  $this->p->get('menu-meta_keywords', ''));
		}

		if ($app->get('MetaTitle') == '1' &  $this->p->get('menupage_title', '')) {
			$this->document->setMetaData('title',  $this->p->get('page_title', ''));
		}

		$pathway->addItem(Text::_('COM_PHOCAEMAIL_NEWSLETTER'));

	}
}
