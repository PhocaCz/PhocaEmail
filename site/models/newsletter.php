<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

class PhocaEmailModelNewsletter extends JModelLegacy
{

	public function storeSubscriber( $name, $email, $privacy, $mailinglist = array()) {
		JSession::checkToken( 'request' ) or jexit(JText::_('JINVALID_TOKEN'));
		
		$params 	= JComponentHelper::getParams('com_phocaemail') ;
		$app		= JFactory::getApplication();
		$message	= '';
		
		$data['name'] 			= $name;
		$data['email']			= $email;
		$data['privacy']		= $privacy;
		$data['date'] 			= gmdate('Y-m-d H:i:s');
		$data['date_register'] 	= gmdate('Y-m-d H:i:s');
		$data['token'] 		= PhocaEmailHelper::getToken();
		$data['active'] 	= 0;
		$data['published'] 	= 1;
		$data['hits'] 		= 0;
		
		// Test - if there is active user, inactive user with many requests, 
		$query = 'SELECT a.id, a.active, a.hits'
			. ' FROM #__phocaemail_subscribers AS a'
			. ' WHERE a.email = '.$this->_db->quote($data['email'])
			. ' LIMIT 1';
		$this->_db->setQuery( $query );
		$user = $this->_db->loadObject();
		
		// X) ACTIVE USER
		if (isset($user->active) && $user->active == 1) {
			$message = JText::_('COM_PHOCAEMAIL_YOUR_SUBSCRIPTION_IS_ACTIVE');
			$app->enqueueMessage($message, 'message');
			return false;
		}
		
		// X) UPDATE HITS - ATTEMPTS
		if (isset($user->hits) && (int)$user->hits > 0) {
			$user->hits++;// This attempts must be counted
			$data['hits'] = (int)$user->hits;
		} else {
			$data['hits'] = 1;
		}
		
		// X) NOT ACTIVE BUT STORED IN DATABASE
		$allowedHits = (int)$params->get('count_subscription', 5);
		
		if (isset($user->hits) && (int)$user->hits > (int)$allowedHits) {
			$message = JText::_('COM_PHOCAEMAIL_YOUR_SUBSCRIPTION_IS_BLOCKED_PLEASE_CONTACT_ADMINISTRATOR');
			$app->enqueueMessage($message, 'error');
			return false;
		}
		
		// X) USER EXISTS BUT IS INACTIVE AND ALLOWED TO SUBSCRIBE
		if (isset($user->active) && (int)$user->active != 1 && isset($user->id) && (int)$user->id > 0) {
			$data['id'] = (int)$user->id;
		}
		
		// X) SEEMS LIKE USER IS NOT IN DATABASE, ADD IT - user id will be automatically created
		// ... ok

		// X) IF REGISTERED USER - ASSIGN AN ACCOUNT TO HIM/HER
		$query = 'SELECT u.id'
			. ' FROM #__users AS u'
			. ' WHERE u.email = '.$this->_db->quote($data['email'])
			. ' LIMIT 1';
		$this->_db->setQuery( $query );
		$registeredUser = $this->_db->loadObject();
		if (isset($registeredUser->id) && $registeredUser->id > 0) {
			$data['user'] = (int)$registeredUser->id;
		}
		
		$row = $this->getTable('phocaemailsubscriber');
	
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->check()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		if (!empty($mailinglist) && (int)$row->id > 0) {
			PhocaEmailSendNewsletterEmail::storeLists((int)$row->id, $mailinglist, '#__phocaemail_subscriber_lists', 'id_subscriber');
		}
	
		return true;
	
	}
	
	public function getNewsletter($nToken) {
		
		$query = 'SELECT a.message_html, a.token, a.url'
			. ' FROM #__phocaemail_newsletters AS a'
			. ' WHERE a.token = '.$this->_db->quote($nToken)
			. ' LIMIT 1';
		$this->_db->setQuery( $query );
		$newsletter = $this->_db->loadObject();
		return $newsletter;
	}
	
	public function getSubscriber($uToken) {
		
		$query = 'SELECT a.id, a.name, a.email, a.token'
			. ' FROM #__phocaemail_subscribers AS a'
			. ' WHERE a.token = '.$this->_db->quote($uToken)
			. ' LIMIT 1';
		$this->_db->setQuery( $query );
		$newsletter = $this->_db->loadObject();
		return $newsletter;
	}
}
?>