<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
class PhocaEmailUtils
{
	public static function setVars( $task = '') {
	
		$a			= array();
		$app		= JFactory::getApplication();
		$a['o'] 	= htmlspecialchars(strip_tags($app->input->get('option')));
		$a['c'] 	= str_replace('com_', '', $a['o']);
		$a['n'] 	= 'Phoca' . ucfirst(str_replace('com_phoca', '', $a['o']));
		$a['l'] 	= strtoupper($a['o']);
		$a['i']		= 'media/'.$a['o'].'/images/administrator/';
		$a['ja']	= 'media/'.$a['o'].'/js/administrator/';
		$a['jf']	= 'media/'.$a['o'].'/js/';
		$a['s']		= 'media/'.$a['o'].'/css/administrator/'.$a['c'].'.css';
		$a['task']	= $a['c'] . htmlspecialchars(strip_tags($task));
		$a['tasks'] = $a['task']. 's';
		return $a;
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
		
		$app    		= JApplicationCms::getInstance('site');
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
	
	public static function fixImagesPath($text) {
		
		return str_replace('<img src="', '<img src="'. JURI::root(), $text);
	}
	
	public static function fixLinksPath($text){
		
		
		$dom = new DOMDocument('1.0', 'UTF-8');
		
		// set error level
		$internalErrors = libxml_use_internal_errors(true);
		//$dom->loadHTML('<?xml encoding="UTF-8">' . htmlspecialchars($text));
		$dom->loadHTML('<?xml encoding="UTF-8">' . $text);
		// Restore error level
		libxml_use_internal_errors($internalErrors);
		
		$links = $dom->getElementsByTagName('a');
		if (!empty($links)) {
			foreach($links as $k => $v) {
				$fixedLink = self::getRightPathLink($v->getAttribute('href'));
				$v->setAttribute('href', $fixedLink);
				
			}
			//$doc->encoding = 'UTF-8';
			$text = $dom->saveHTML();
		}
			
		return $text;
	}
	
	
	public static function renderReCaptcha() {
		
		$document	= JFactory::getDocument();
		$paramsC 	= JComponentHelper::getParams('com_phocaemail') ;
		$siteKey	= strip_tags(trim($paramsC->get( 'recaptcha_sitekey', 'no-key' )));
		
		$document->addScript('https://www.google.com/recaptcha/api.js');
		return '<div class="g-recaptcha" data-sitekey="'.$siteKey.'"></div>';
	}
	
	
	public static function isReCaptchaValid() {
		
		$app 		= JFactory::getApplication();
		$paramsC 	= JComponentHelper::getParams('com_phocaemail') ;
		$secretKey	= strip_tags(trim($paramsC->get( 'recaptcha_privatekey', '' )));
		//$response 	= $app->input->post->get('g-recaptcha-response', '', 'string');
		//$response	= $ POST['g-recaptcha-response'];
		$response 	= $app->input->post->get('g-recaptcha-response', '', 'string');
		$remoteIp	= $_SERVER['REMOTE_ADDR'];
		$urlVerify	= 'https://www.google.com/recaptcha/api/siteverify';
		
		$recaptchaMethod = $paramsC->get( 'recaptcha_request_method', 2 );//1 file_get_contents, 2 curl
		
		try {

			
			if ($recaptchaMethod == 1) {
				// FILE GET CONTENTS
				$data = ['secret'   => $secretKey,
						 'response' => $response,
						 'remoteip' => $remoteIp];

				$options = [
					'http' => [
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data) 
					]
				];

				$context  = stream_context_create($options);
				$result = file_get_contents($urlVerify, false, $context);
			} else {
			// CURL
				$ch = curl_init();

				curl_setopt_array($ch, [
					CURLOPT_URL => $urlVerify,
					CURLOPT_POST => true,
				//	CURLOPT_SSL_VERIFYPEER => false,
				//	CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_POSTFIELDS => [
						'secret' => $secretKey,
						'response' => $response,
						'remoteip' => $remoteIp],
					CURLOPT_RETURNTRANSFER => true
				]);
				
				$result = curl_exec($ch);
				curl_close($ch);
			}
			
			//$resultString = print r($result, true);
		
			
			return json_decode($result)->success;
				
		}
		catch (Exception $e) {
			return null;
		}
	}
	
}
?>