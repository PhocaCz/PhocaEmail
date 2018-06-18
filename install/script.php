<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.filesystem.folder' );

class com_phocaemailInstallerScript
{
	function install($parent) {
		
		
		$folder[0][0]	=	'phocaemail/' ;
		$folder[0][1]	= 	JPATH_ROOT . '/'.  $folder[0][0];
		$folder[1][0]	=	'phocaemail' . '/vm/';
		$folder[1][1]	= 	JPATH_ROOT . '/' .  $folder[1][0];
		
		$message = '';
		$error	 = array();
		foreach ($folder as $key => $value)
		{
			if (!JFolder::exists( $value[1]))
			{
				if (JFolder::create( $value[1], 0755 ))
				{
					
					$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
					JFile::write($value[1]."/index.html", $data);
					$message .= '<div><b><span style="color:#009933">Folder</span> ' . $value[0] 
							   .' <span style="color:#009933">created!</span></b></div>';
					$error[] = 0;
				}	 
				else
				{
					$message .= '<div><b><span style="color:#CC0033">Folder</span> ' . $value[0]
							   .' <span style="color:#CC0033">creation failed!</span></b> Please create it manually.</div>';
					$error[] = 1;
				}
			}
			else//Folder exist
			{
				$message .= '<div><b><span style="color:#009933">Folder</span> ' . $value[0] 
							   .' <span style="color:#009933">exists!</span></b></div>';
				$error[] = 0;
			}
		}

		$app		= JFactory::getApplication();
		$app->enqueueMessage($message, 'message');
		$parent->getParent()->setRedirectURL('index.php?option=com_phocaemail');
	}
	function uninstall($parent) {
		//echo '<p>' . JText::_('COM_PHOCAEMAIL_UNINSTALL_TEXT') . '</p>';
	}

	function update($parent) {
		//echo '<p>' . JText::sprintf('COM_PHOCAEMAIL_UPDATE_TEXT', $parent->get('manifest')->version) . '</p>';
		
		$folder[0][0]	=	'phocaemail/' ;
		$folder[0][1]	= 	JPATH_ROOT . '/'.  $folder[0][0];
		$folder[1][0]	=	'phocaemail' . '/vm/';
		$folder[1][1]	= 	JPATH_ROOT . '/'. $folder[1][0];
		
		$message = '';
		$error	 = array();
		foreach ($folder as $key => $value)
		{
			if (!JFolder::exists( $value[1]))
			{
				if (JFolder::create( $value[1], 0755 ))
				{
					
					$data = "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>";
					JFile::write($value[1]."/index.html", $data);
					$message .= '<div><b><span style="color:#009933">Folder</span> ' . $value[0] 
							   .' <span style="color:#009933">created!</span></b></div>';
					$error[] = 0;
				}	 
				else
				{
					$message .= '<div><b><span style="color:#CC0033">Folder</span> ' . $value[0]
							   .' <span style="color:#CC0033">creation failed!</span></b> Please create it manually.</div>';
					$error[] = 1;
				}
			}
			else//Folder exist
			{
				$message .= '<div><b><span style="color:#009933">Folder</span> ' . $value[0] 
							   .' <span style="color:#009933">exists!</span></b></div>';
				$error[] = 0;
			}
		}

		$msg =  JText::_('COM_PHOCAEMAIL_UPDATE_TEXT');
		$msg .= ' (' . JText::_('COM_PHOCAEMAIL_VERSION'). ': ' . $parent->get('manifest')->version . ')';
		$msg .= '<br />'. $message;
		$app		= JFactory::getApplication();
		$app->enqueueMessage($msg, 'message');
		$app->redirect(JRoute::_('index.php?option=com_phocaemail'));
	}

	function preflight($type, $parent) {}

	function postflight($type, $parent)  {}
}