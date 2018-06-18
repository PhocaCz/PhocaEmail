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

 defined('_JEXEC') or die('Restricted access');

class PhocaEmailSend
{
	public static function send ($data, &$warning, &$error, &$redirect, $static = 0) {
	
	
		if ($static == 0) {
			JSession::checkToken() or jexit( 'Invalid Token' );
		} else {
			require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemail.php' );
		}
		$app								= JFactory::getApplication();
		$db									= JFactory::getDBO();
		$params 							= JComponentHelper::getParams('com_phocaemail') ;
		$param['html_message']				= $params->get('html_message', 1);
		$param['display_users_list']		= $params->get('display_users_list', 0);
		$param['display_groups_list']		= $params->get('display_groups_list', 0);
		$param['display_users_list_cc']		= $params->get('display_users_list_cc', 0);
		$param['display_users_list_bcc']	= $params->get('display_users_list_bcc', 0);
		$param['display_groups_list_cc']	= $params->get('display_groups_list_cc', 0);
		$param['display_groups_list_bcc']	= $params->get('display_groups_list_bcc', 0);
		


		// Attachment
		$attachmentArray 		= array();
		$tmpl['path']			= PhocaEmailHelper::getPath();
		if($data['ext']	== 'phocaemail') {
			
			$tmpl['attachment']		= JFolder::files ($tmpl['path']['path_abs_nods'], '.', false, false, array('index.html'));
			$tmpl['attachment_full']= JFolder::files ($tmpl['path']['path_abs_nods'], '.', false, true, array('index.html'));
			
			if (!empty($tmpl['attachment'])) {
				$i = 0;
				foreach ($tmpl['attachment'] as $key => $value) {
					if(isset($data['attachment'][$i]) && $data['attachment'][$i]) {
						if (JFile::exists($tmpl['attachment_full'][$i])) {
							$attachmentArray[] = $tmpl['attachment_full'][$i];
						} else {
							$warning[]	= JText::_('COM_PHOCAEMAIL_ERROR_FILE_NOT_EXISTS').': '. $tmpl['attachment_full'][$i];
						}
					}
					$i++;
				}
			}
		} else if ($data['ext']	== 'virtuemart') {
		
			if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfrender.php')) {
				require_once(JPATH_ADMINISTRATOR.'/components/com_phocapdf/helpers/phocapdfrender.php');
			} else {
				
				throw new Exception('Error - Phoca PDF Helper - Render PDF file could not be found in system', 500);
				return false;
			}
			
	
			if (!empty($data['attachmentfile'])) {
				if ($data['type'] == 'deliverynote') {
					$i = 1;
				} else {
					$i = 0;
				}
				
				foreach ($data['attachmentfile'] as $key => $value) {
					if(isset($data['attachment'][$i]) && $data['attachment'][$i]) {
						
						$pdfFile = $tmpl['path']['path_abs'].'vm/'. $data['attachmentfile'][$key];

						$staticData					= array();
						$staticData['option']		= 'com_virtuemart';
						$staticData['file']			= $pdfFile;
						$staticData['filename']		= $data['attachmentfile'][$key];
						$staticData['ext']			= $data['ext'];
						$staticData['type']			= $data['type'];
						$staticData['order_id']		= $data['order_id'];
						$staticData['delivery_id']	= $data['delivery_id'];
						// In  case we send invoice or receipt and delivery note was attached too)
						// Delivery Note is in such case $key == 1
						if(($data['type'] == 'invoice' || $data['type'] == 'receipt') && $key == 1) {
							$staticData['type']	= 'deliverynote';
						}
					
						$pdfCreated = PhocaPDFRender::renderPDF('', $staticData);
						
						if (JFile::exists($staticData['file'])) {
							$attachmentArray[] = $staticData['file'];
						} else {
							$warning[]	= JText::_('COM_PHOCAEMAIL_ERROR_FILE_NOT_EXISTS').': '. $staticData['file'];
						}
					}
					$i++;
				}
			}
		}
		
		$articleText = '';
		if($data['ext']	== 'phocaemail') {
			if ((int)$data['article_id'] > 0) {
				$query = 'SELECT *'
					. ' FROM #__content'
					. ' WHERE id = '.(int)$data['article_id'];
			
					$db->setQuery( (string)$query );

					
					$articleText = $db->loadObject();
					$articleText = $articleText->introtext . $articleText->fulltext;
			}
		}

		// FROM
		if (isset($data['from']) && $data['from'] != '') {
			$from 		= $data['from'];
		} else {
			$from 		= '';
			$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_FROM_EMPTY');
		}
		
		// FROM NAME
		if (isset($data['fromname']) && $data['fromname'] != '') {
			$fromname 	= $data['fromname'];
		} else {
			$fromname 	= '';
			$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_FROM_NAME_EMPTY');
		}
		
		
		// ----------
		// TO USERS
		// ----------
		if ($data['ext']	== 'phocaemail') {
			$toEmpty1 =	$toEmpty2 = $toEmpty3 =  0;
			
			// TO (GROUPS)
			if (isset($data['togroups']) && is_array($data['togroups']) && !empty($data['togroups']) && $param['display_groups_list'] == 1 ) {
				
				
				$groups	= implode(',', $data['togroups']);
				
				
				$query = 'SELECT a.id, a.email'
				. ' FROM #__users as a'
				. ' LEFT JOIN #__user_usergroup_map AS b ON a.id = b.user_id'
				. ' WHERE b.group_id IN ('.$groups.')'
				. ' AND a.block = 0'
				. ' GROUP BY a.id';
				
		
				$db->setQuery( (string)$query );

				$groupsDB = $db->loadObjectList();
				
				
				$to3 = array();
				foreach($groupsDB as $key => $value) {
					$to3[]	=	$value->email;
				}
			} else {
				$to3		= '';
				$toEmpty3 	= 1;
			}
			
			// TO (USERS)
			if (isset($data['tousers']) && is_array($data['tousers']) && !empty($data['tousers']) && $param['display_users_list'] == 1 ) {
				
				$users	= implode(',', $data['tousers']);
				$query = 'SELECT id, email'
				. ' FROM #__users'
				. ' WHERE id IN ('.$users.')';
		
				$db->setQuery( (string)$query );
				

				$usersDB = $db->loadObjectList();
				
				$to1 = array();
				foreach($usersDB as $key => $value) {
					$to1[]	=	$value->email;
				}
			} else {
				$to1		= '';
				$toEmpty1 	= 1;
			}
			
			// TO
			if (isset($data['to']) && $data['to'] != '') {
				$to2	= trim( $data['to'] );
				$to2 	= explode( ',', $to2);
			} else {
				$to2	= '';
				$toEmpty2 = 1;
			}
			
			if ($toEmpty1 == 1 && $toEmpty2 == 1 && $toEmpty3 == 1) {
				$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_OR_TO_USERS_EMPTY');
			} else {
				if (empty($to1)) {
					$to1 = array();
				}
				if (empty($to2)) {
					$to2 = array();
				}
				if (empty($to3)) {
					$to3 = array();
				}
				$to = array_merge($to1, $to2, $to3);
				if (empty($to)) {
					$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_OR_TO_USERS_EMPTY');
				}
			}
			
		} else {
			// TO
			if (isset($data['to']) && $data['to'] != '') {
				$to			= trim( $data['to'] );
				$to		 	= explode( ',', $to);
			} else {
				$to 		= array();
				$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_EMPTY');
			}
		
		}
		if (!empty($to) && is_array($to)) {
			$to = array_unique($to);
		}
		
		
		
		// ---------
		// CC USERS
		// ---------
		$cc = array();
		if ($data['ext']	== 'phocaemail') {
			$ccEmpty1 =	$ccEmpty2 = $ccEmpty3 =  0;
			
			// CC (GROUPS)
			if (isset($data['ccgroups']) && is_array($data['ccgroups']) && !empty($data['ccgroups']) && $param['display_groups_list_cc'] == 1 ) {
				
				
				$groups	= implode(',', $data['ccgroups']);
				
				
				$query = 'SELECT a.id, a.email'
				. ' FROM #__users as a'
				. ' LEFT JOIN #__user_usergroup_map AS b ON a.id = b.user_id'
				. ' WHERE b.group_id IN ('.$groups.')'
				. ' AND a.block = 0'
				. ' GROUP BY a.id';
				
		
				$db->setQuery( (string)$query );

				$groupsDB = $db->loadObjectList();
				
				
				$cc3 = array();
				foreach($groupsDB as $key => $value) {
					$cc3[]	=	$value->email;
				}
			} else {
				$cc3		= '';
				$ccEmpty3 	= 1;
			}
			
			// CC (USERS)
			if (isset($data['ccusers']) && is_array($data['ccusers']) && !empty($data['ccusers']) && $param['display_users_list_cc'] == 1 ) {
				
				$users	= implode(',', $data['ccusers']);
				$query = 'SELECT id, email'
				. ' FROM #__users'
				. ' WHERE id IN ('.$users.')';
		
				$db->setQuery( (string)$query );
				

				$usersDB = $db->loadObjectList();
				
				$cc1 = array();
				foreach($usersDB as $key => $value) {
					$cc1[]	=	$value->email;
				}
			} else {
				$cc1		= '';
				$ccEmpty1 	= 1;
			}
			
			// cc
			if (isset($data['cc']) && $data['cc'] != '') {
				$cc2	= trim( $data['cc'] );
				$cc2 	= explode( ',', $cc2);
			} else {
				$cc2	= '';
				$ccEmpty2 = 1;
			}
			
			if ($ccEmpty1 == 1 && $ccEmpty2 == 1 && $ccEmpty3 == 1) {
				//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_OR_CC_USERS_OR_CC_GROUPS_EMPTY');
			} else {
				if (empty($cc1)) {
					$cc1 = array();
				}
				if (empty($cc2)) {
					$cc2 = array();
				}
				if (empty($cc3)) {
					$cc3 = array();
				}
				$cc = array_merge($cc1, $cc2, $cc3);
				if (empty($cc)) {
					//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_OR_CC_USERS_OR_CC_GROUPS_EMPTY');
				}
			}
			
		} else {
			// cc
			if (isset($data['cc']) && $data['cc'] != '') {
				$cc			= trim( $data['cc'] );
				$cc		 	= explode( ',', $cc);
			} else {
				$cc			= array();
				//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_EMPTY');
			}
		
		}
		
		if (!empty($cc) && is_array($cc)) {
			$cc = array_unique($cc);
		}
		
		
		
		// -----------
		// BCC USERS
		// -----------
		$bcc = array();
		if ($data['ext']	== 'phocaemail') {
			$bccEmpty1 =	$bccEmpty2 = $bccEmpty3 =  0;
			
			// TO (GROUPS)
			if (isset($data['bccgroups']) && is_array($data['bccgroups']) && !empty($data['bccgroups']) && $param['display_groups_list_bcc'] == 1 ) {
				
				
				$groups	= implode(',', $data['bccgroups']);
				
				
				$query = 'SELECT a.id, a.email'
				. ' FROM #__users as a'
				. ' LEFT JOIN #__user_usergroup_map AS b ON a.id = b.user_id'
				. ' WHERE b.group_id IN ('.$groups.')'
				. ' AND a.block = 0'
				. ' GROUP BY a.id';
				
		
				$db->setQuery( (string)$query );

				$groupsDB = $db->loadObjectList();
				
				
				$bcc3 = array();
				foreach($groupsDB as $key => $value) {
					$bcc3[]	=	$value->email;
				}
				
			} else {
				$bcc3		= '';
				$bccEmpty3 	= 1;
			}
			
			// BCC (USERS)
			if (isset($data['bccusers']) && is_array($data['bccusers']) && !empty($data['bccusers']) && $param['display_users_list_bcc'] == 1 ) {
				
				$users	= implode(',', $data['bccusers']);
				$query = 'SELECT id, email'
				. ' FROM #__users'
				. ' WHERE id IN ('.$users.')';
		
				$db->setQuery( (string)$query );
				

				$usersDB = $db->loadObjectList();
				
				$bcc1 = array();
				foreach($usersDB as $key => $value) {
					$bcc1[]	=	$value->email;
				}
			} else {
				$bcc1		= '';
				$bccEmpty1 	= 1;
			}
			
			// bcc
			if (isset($data['bcc']) && $data['bcc'] != '') {
				$bcc2	= trim( $data['bcc'] );
				$bcc2 	= explode( ',', $bcc2);
			} else {
				$bcc2	= '';
				$bccEmpty2 = 1;
			}
			
			if ($bccEmpty1 == 1 && $bccEmpty2 == 1 && $bccEmpty3 == 1) {
				//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_OR_CC_USERS_OR_CC_GROUPS_EMPTY');
			} else {
				if (empty($bcc1)) {
					$bcc1 = array();
				}
				if (empty($bcc2)) {
					$bcc2 = array();
				}
				if (empty($bcc3)) {
					$bcc3 = array();
				}
				
				$bcc = array_merge($bcc1, $bcc2, $bcc3);
				if (empty($bcc)) {
					//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_OR_CC_USERS_OR_CC_GROUPS_EMPTY');
				}
			}
			
			
			
		} else {
			// bcc
			if (isset($data['bcc']) && $data['bcc'] != '') {
				$bcc			= trim( $data['bcc'] );
				$bcc		 	= explode( ',', $bcc);
			} else {
				$bcc			= array();
				//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_EMPTY');
			}
		
		}
		
		if (!empty($bcc) && is_array($bcc)) {
			$bcc = array_unique($bcc);
		}
		
		
		
		
		
		
		
		
		
		/*
		
		// CC USERS
		$cc = array();
		if ($data['ext']	== 'phocaemail' && $param['display_users_list_cc'] == 1) {
			$ccEmpty1 =	$ccEmpty2 =  0;
			if (isset($data['ccusers']) && is_array($data['ccusers']) && !empty($data['ccusers']) ) {
				
				$users	= implode(',', $data['ccusers']);
				$query = 'SELECT id, email'
				. ' FROM #__users'
				. ' WHERE id IN ('.$users.')';
		
				$db->setQuery( (string)$query );

				$usersDB = $db->loadObjectList();
				
				$cc1 = array();
				foreach($usersDB as $key => $value) {
					$cc1[]	=	$value->email;
				}
			} else {
				$cc1		= '';
				$ccEmpty1 	= 1;
			}
			
			if (isset($data['cc']) && $data['cc'] != '') {
				$cc2	= trim( $data['cc'] );
				$cc2 	= explode( ',', $cc2);
			} else {
				$cc2	= '';
				$ccEmpty2 = 1;
			}
			
			if ($ccEmpty1 == 1 && $ccEmpty2 == 1) {
				//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_OR_CC_USERS_EMPTY');
			} else {
				if (empty($cc1)) {
					$cc1 = array();
				}
				if (empty($cc2)) {
					$cc2 = array();
				}
				$cc = array_merge($cc1, $cc2);
				/*if (empty($cc)) {
					$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_CC_OR_CC_USERS_EMPTY');
				}*//*
			}
			
		} else {
			// CC
			if (isset($data['cc']) && $data['cc'] != '') {
				$cc	= trim( $data['cc'] );
				$cc = explode( ',', $cc);
			} else {
				$cc = array();
			}
		
		}
		
		// BCC USERS
		$bcc = array();
		if ($data['ext']	== 'phocaemail' && $param['display_users_list_bcc'] == 1) {
			$bccEmpty1 =	$bccEmpty2 =  0;
			if (isset($data['bccusers']) && is_array($data['bccusers']) && !empty($data['bccusers']) ) {
				
				$users	= implode(',', $data['bccusers']);
				$query = 'SELECT id, email'
				. ' FROM #__users'
				. ' WHERE id IN ('.$users.')';
		
				$db->setQuery( (string)$query );
				

				$usersDB = $db->loadObjectList();
				
				$bcc1 = array();
				foreach($usersDB as $key => $value) {
					$bcc1[]	=	$value->email;
				}
			} else {
				$bcc1		= '';
				$bccEmpty1 	= 1;
			}
			
			if (isset($data['bcc']) && $data['bcc'] != '') {
				$bcc2	= trim( $data['bcc'] );
				$bcc2 	= explode( ',', $bcc2);
			} else {
				$bcc2	= '';
				$bccEmpty2 = 1;
			}
			
			if ($bccEmpty1 == 1 && $bccEmpty2 == 1) {
				//$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_BCC_OR_BCC_USERS_EMPTY');
			} else {
				if (empty($bcc1)) {
					$bcc1 = array();
				}
				if (empty($bcc2)) {
					$bcc2 = array();
				}
				$bcc = array_merge($bcc1, $bcc2);
				/*if (empty($bcc)) {
					$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_BCC_OR_BCC_USERS_EMPTY');
				}*//*
			}
			
		} else {
			// BCC
			if (isset($data['bcc']) && $data['bcc'] != '') {
				$bcc	= trim( $data['bcc'] );
				$bcc 	= explode( ',', $bcc);
			} else {
				$bcc = array();
			}
		
		}
		*/
		
		
		
		
		
		if (isset($data['subject']) && $data['subject'] != '') {
			$subject	= $data['subject'];
		} else {
			$subject	= '';
			$error[]	= JText::_('COM_PHOCAEMAIL_ERROR_FIELD_SUBJECT_EMPTY');
		}
		
		if (isset($data['message']) && $data['message'] != '') {
			$message	= $data['message'];
		} else {
			$message	= '';
		}
		
		if (!empty($error)) {
			return false;
			exit;
		}
		
		
		$mainMessage 	= $articleText . $message;
		$mainMessage	= PhocaEmailUtils::fixImagesPath($mainMessage);
		$mainMessage	= PhocaEmailUtils::fixLinksPath($mainMessage);
		
		
		$htmlMessage 	 = '<html><head><title>'.$subject.'</title></head><body>';
		$htmlMessage 	.= $mainMessage ;
		$htmlMessage 	.= '</body></html>';
		
	
		foreach ($to as $kt => $vt) {
			if ( $vt =='' || $vt == ' ' || ctype_space($vt)) {
				unset ($to[$kt]);
			}
		}
		
		foreach ($cc as $kt => $vt) {
			if ($vt =='' || $vt == ' ' || ctype_space($vt)) {
				unset ($cc[$kt]);
			}
		}
		
		foreach ($bcc as $kt => $vt) {
			if ($vt =='' || $vt == ' ' || ctype_space($vt)) {
				unset ($bcc[$kt]);
			}
		}
		
		$replyto 		= $from;
		$replytoname	= $fromname;

	/*			
		echo	'<html><head><title>'.$subject.'</title></head><body>';
		echo	'<div style="font-family: sans-serif, Arial;font-size:12px">';
		echo	'<p>' .JText::_('COM_PHOCAEMAIL_ERROR_POSSIBLE_EMAIL_PROBLEM'). '</p>';
		echo 	'<a href="index.php?option=com_phocaemail" >'.JText::_('COM_PHOCAEMAIL_BACK_TO_PE_CONTROL_PANEL').'</a>';
		echo	'</div>';
		echo 	'</body></html>';*/
		
		// - - - - - - - - - - 
		// Change the time
		$changedMaxExecTime		= 0;
		$standardMaxExecTime 	= ini_get('max_execution_time');
		if ($standardMaxExecTime < 120) {
			set_time_limit(120);
			$changedMaxExecTime	= 1;
		}
		// - - - - - - - - - - 

		

	
		if ($param['html_message'] == 0) {
			$rawMessage		=	strip_tags($htmlMessage);
			$sendMail  		= JFactory::getMailer()->sendMail($from, $fromname, $to, $subject, $rawMessage, false, $cc, $bcc, $attachmentArray, $replyto, $replytoname);
		//	$sendMail  		= PhocaemailEmail::sendEmail($from, $fromname, $to, $subject, $rawMessage, true, $cc, $bcc, $attachmentArray, '', $replyto, $replytoname);
		} else {
			$sendMail  		= JFactory::getMailer()->sendMail($from, $fromname, $to, $subject, $htmlMessage, true, $cc, $bcc, $attachmentArray, $replyto, $replytoname);
		//	$sendMail  		= PhocaemailEmail::sendEmail($from, $fromname, $to, $subject, $htmlMessage, true, $cc, $bcc, $attachmentArray, '', $replyto, $replytoname);
		}
		

				
				
		// Remove attachments
		if ($data['ext']	== 'virtuemart') {
			foreach ($attachmentArray as $key => $value) {
				if (JFile::exists($value)) {
					JFile::delete($value);
				}
			}
		}
		
		//$document  = JFactory::getDocument();
		//$renderer  = $document->loadRenderer('message');
		//print_r($renderer->render('message'));
		
		// - - - - - - - - - - 
		// Set back the time
		if ($changedMaxExecTime == 1) {
			set_time_limit($standardMaxExecTime);
		}
		// - - - - - - - - - - 
		
	/*	if (!$sendMail) {
			
			// message rendered by email function
			return false;
		} else {
			
			$warning[] = JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_SENT');
			return true;
		}*/
	
	
		
		if (isset($sendMail->message)) {
			$error[] = JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_NOT_SENT') . ': '  . $sendMail->message;
			return false;
		} else if ($sendMail) {
			$warning[] = JText::_('COM_PHOCAEMAIL_ERROR_EMAIL_SENT');
			return true;
		} else {
			$error[] = JText::_('COM_PHOCAEMAIL_ERROR_SENDING_EMAIL_SERVER_SENT_NO_ERROR_MESSAGE') . '. '. JText::_('COM_PHOCAEMAIL_EMAIL_FROM') . ': '.$from . ', ' . JText::_('COM_PHOCAEMAIL_EMAIL_TO'). ': ' .implode(',', $to);
			$error[] = JText::_('COM_PHOCAEMAIL_EMAIL_NOT_SENT');
			return false;
		}
	}
}