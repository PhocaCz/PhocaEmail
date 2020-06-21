<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 defined('_JEXEC') or die('Restricted access');
require_once( JPATH_SITE.'/components/com_phocaemail/helpers/route.php');

class PhocaEmailSendNewsletterEmail
{
	public static function sendNewsLetterEmail($name, $email, $type = 'activate') {

		$params 		= JComponentHelper::getParams('com_phocaemail') ;
		$recipient 		= $email;
		$activationLink	= '';
		$items			= self::getItems();

		$replace['sitename'] 		= $items['sitename'];
		$replace['subscriptionname']= $items['subscriptionname'];



		switch($type) {
			case 'activate':
				$subject 	= JText::_('COM_PHOCAEMAIL_ACTIVATE_YOUR_EMAIL_SUBSCRIPTION') . ' '. $items['subscriptionname'];
				$body = $params->get('activation_email', '<div>Hello,<br /> <br /> You recently requested an email subscription to {subscriptionname}.<br /> Please confirm your subscription.</div>
<p> </p>
<div><a style="background: #0044cc; color: #fff; padding: 5px; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;" href="{activationlink}">Click here to confirm your subscription to our newsletter</a>
</div><br />
<div>If the link above does not appear clickable or does not open a browser window when you click it,<br /> copy and paste following link into your web browser\'s Location bar:<br /><br /> {activationlink}</div>
<br />
<div style="color: #777;border-top:1px solid #777">
<div>This message was sent to you by {sitename}.</div>
<div>You received this message because you requested a subscription to the {subscriptionname}.</div>
<div>If you received this in error, please disregard. Do not reply directly to this email.</div>
</div>');
				$activationLink = self::getEmailLink($email, 'activate');
			break;

			case 'unsubscribe':
			default:
				$subject 	= $replace['subscriptionname'] . ': '.JText::_('COM_PHOCAEMAIL_SUBSCRIPTION_CANCELED');

				$body = $params->get('unsubscribe_email', '<div>Hello,<br /> <br /> You recently requested an unsubscription from our newsletter.<br />This email confirms that you have been successfully unsubscribed from our newsletter.</div>');
			break;
		}
		$replace['activationlink']	= $activationLink;
		$body 						= self::completeMail($body, $replace);
		$subject 					= self::completeMail($subject, $replace);



		$send  		= JFactory::getMailer()->sendMail($items['from'], $items['fromname'], $recipient, $subject, $body, true, null, null, null, null, null);

		if ($send) {
			return true;
		} else {
			return false;
		}
	}

	public static function getItems() {

		$app						= JFactory::getApplication();
		$params 					= JComponentHelper::getParams('com_phocaemail') ;

		$i['sitename']	= $params->get('site_name', '');
		if ($i['sitename'] == '') {
			$i['sitename'] = $app->get('sitename');
		}
		if ($i['sitename'] == '') {
			$i['sitename'] = $app->get('fromname');
		}

		$i['subscriptionname']		= $params->get('subscription_name', '');
		if ($i['subscriptionname'] == '') {
			$i['subscriptionname'] = $i['sitename'];
		}

		$i['from'] = $params->get('email_from', '');
		if ($i['from'] == '') {
			$i['from'] = $app->get('mailfrom');
		}

		$i['fromname'] = $params->get('from_name', '');

		if ($i['fromname'] == '') {
			$i['fromname'] = $i['sitename'];
		}

		if ($i['fromname'] == '') {
			$i['fromname'] = $app->get('mailfrom');
		}

		return $i;
	}

	public static function completeMail($body, $replace) {

		if (isset($replace['sitename'])) {
			$body = str_replace('{sitename}', $replace['sitename'], $body);
		}
		if (isset($replace['subscriptionname'])) {
			$body = str_replace('{subscriptionname}', $replace['subscriptionname'], $body);
		}
		if (isset($replace['activationlink'])) {
			$body = str_replace('{activationlink}', $replace['activationlink'], $body);
		}
		if (isset($replace['unsubscribelink'])) {
			$body = str_replace('{unsubscribelink}', $replace['unsubscribelink'], $body);
		}
		if (isset($replace['name'])) {
			$body = str_replace('{name}', $replace['name'], $body);
		}
		if (isset($replace['email'])) {
			$body = str_replace('{email}', $replace['email'], $body);
		}
		if (isset($replace['articlelink'])) {
			$body = str_replace('{articlelink}', $replace['articlelink'], $body);
		}

		if (isset($replace['readonlinelink'])) {
			$body = str_replace('{readonlinelink}', $replace['readonlinelink'], $body);
		}

		return $body;
	}

	public static function getEmailLink($email, $type = 'activate') {

		$db				= JFactory::getDBO();
		$token 			= '';

		$query = 'SELECT a.token FROM #__phocaemail_subscribers AS a'
				. ' WHERE a.email = '.$db->quote($email)
				. ' LIMIT 1';
		$db->setQuery( (string)$query );
		$user = $db->loadObject();
		if (isset($user->token) && $user->token != '') {
			$token = $user->token;
		}
		$link = PhocaEmailHelperRoute::getNewsletterRoute(0, $type, $token);
		//return JURI::base(true) . JRoute::_($link);

		$formatLink = PhocaEmailUtils::getRightPathLink($link);
		return $formatLink;
	}

	public static function getRightPathLink($link) {

		// Test if this link is absolute http:// then do not change it
		$pos1 			= strpos($link, 'http://');
		if ($pos1 === false) {
		} else {
			return $link;
		}

		// Test if this link is absolute https:// then do not change it
		$pos2 			= strpos($link, 'https://');
		if ($pos2 === false) {
		} else {
			return $link;
		}

		$app    		= JApplication::getInstance('site');
		$router 		= $app->getRouter();
		$uri 			= $router->build($link);
		$uriS			= $uri->toString();

		// Test if administrator is included in URL - to remove it
		$pos 			= strpos($uriS, 'administrator');

		if ($pos === false) {

			$uriL = self::ph_str_replace_first(JURI::root(true), '', $uriS);


			$uriL = ltrim($uriL, '/');
			$formatLink = JURI::root(false). $uriL;
			//$formatLink = $uriS;
		} else {
			$formatLink = JURI::root(false). str_replace(JURI::root(true).'/administrator/', '', $uri->toString());
		}

		return $formatLink;
	}

	public static function ph_str_replace_first($from, $to, $subject) {
		$from = '/'.preg_quote($from, '/').'/';
		return preg_replace($from, $to, $subject, 1);
	}

	public static function activateUser($uToken) {
		$db	= JFactory::getDBO();

		// Check if it is active yet
		$query = 'SELECT a.active FROM #__phocaemail_subscribers AS a'
				. ' WHERE a.token = '.$db->quote(htmlspecialchars($uToken))
				. ' LIMIT 1';
		$db->setQuery( (string)$query );

		$subscriber = $db->loadObject();
		if (isset($subscriber->active) && $subscriber->active == 1) {
			return 2; // Already active
		}

		$date 			= gmdate('Y-m-d H:i:s');

		$query = 'UPDATE #__phocaemail_subscribers AS a SET a.active = 1, a.date_active = '.$db->quote($date)
				. ' WHERE a.token = '.$db->quote(htmlspecialchars($uToken))
				. ' LIMIT 1';
		$db->setQuery( (string)$query );
		$db->execute();
		$rows = $db->getAffectedRows();
		if ($rows > 0) {
			return 1; // activated
		} else {
			return false; // not activated
		}
	}

	public static function unsubscribeUser($uToken) {

		$params 							= JComponentHelper::getParams('com_phocaemail') ;
		$unsubscribing_automatic_deletion	= $params->get('unsubscribing_automatic_deletion', 0);

		$db	= JFactory::getDBO();
		// Check if it is active yet
		$query = 'SELECT a.active, a.name, a.email FROM #__phocaemail_subscribers AS a'
				. ' WHERE a.token = '.$db->quote(htmlspecialchars($uToken))
				. ' LIMIT 1';
		$db->setQuery( (string)$query );

		$subscriber = $db->loadObject();
		if (isset($subscriber->active) && ($subscriber->active == 0 || $subscriber->active == 2)) {
			//0 ... not active
			//1 ... active
			//2 ... unsubscribed
			return 3; // Not active, cannot be unsubscribed
		}

		if ($unsubscribing_automatic_deletion == 1) {
			$query = 'DELETE FROM #__phocaemail_subscribers'
				. ' WHERE token = '.$db->quote(htmlspecialchars($uToken))
				. ' LIMIT 1';
			$db->setQuery( (string)$query );
			$db->execute();
			return 4;
		}


		$date = gmdate('Y-m-d H:i:s');
		$query = 'UPDATE #__phocaemail_subscribers AS a SET a.active = 2, a.date_unsubscribe = '.$db->quote($date)
				. ' WHERE a.token = '.$db->quote(htmlspecialchars($uToken))
				. ' LIMIT 1';
		$db->setQuery( (string)$query );
		$db->execute();
		$rows = $db->getAffectedRows();
		//if ($rows > 0 && isset($subscriber->name) && isset($subscriber->email) && $subscriber->name != '' && $subscriber->email != '')  {
        if ($rows > 0 && isset($subscriber->name) && isset($subscriber->email) && $subscriber->email != '')  {
			$params 				= JComponentHelper::getParams('com_phocaemail') ;
			$sendUnsubscribeEmail	= $params->get('send_unsubscribe_email', 1);
			if ($sendUnsubscribeEmail == 1) {
				self::sendNewsLetterEmail($subscriber->name, $subscriber->email, 'unsubscribe');
				return 2; // unsubscribed, mail sent
			} else {
				return 1; // usubscribed
			}
		} else {
			return false;
		}
	}

	public static function storeLists($id, $listArray, $table = '#__phocaemail_subscriber_lists', $item = 'id_subscriber') {
		if ((int)$id > 0) {
			$db = JFactory::getDBO();
			$query = ' DELETE '
					.' FROM '.$table
					. ' WHERE '.$item.' = '. (int)$id;
			$db->setQuery($query);
			$db->execute();


			if (!empty($listArray)) {

				$values 		= array();
				$valuesString 	= '';


				foreach($listArray as $k => $v) {
					$values[] = ' ('.(int)$id.', '.(int)$v.')';
				}


				if (!empty($values)) {
					$valuesString = implode(',', $values);

					$query = ' INSERT INTO '.$table.' ('.$item.', id_list)'
								.' VALUES '.(string)$valuesString;


					$db->setQuery($query);
					$db->execute();
				}
			}
			return true;
		}
	}
}
