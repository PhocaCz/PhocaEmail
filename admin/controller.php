<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');



$l['cp']	= array('COM_PHOCAEMAIL_CONTROL_PANEL', '');
$l['w']		= array('COM_PHOCAEMAIL_SEND_EMAIL', 'phocaemailwrite');
$l['sn']		= array('COM_PHOCAEMAIL_SEND_NEWSLETTER', 'phocaemailsendnewsletter');
$l['n']		= array('COM_PHOCAEMAIL_NEWSLETTERS', 'phocaemailnewsletters');
$l['s']		= array('COM_PHOCAEMAIL_SUBSCRIBERS', 'phocaemailsubscribers');
$l['l']		= array('COM_PHOCAEMAIL_MAILING_LISTS', 'phocaemaillists');
$l['in']	= array('COM_PHOCAEMAIL_INFO', 'phocaemailinfo');

$view	= JFactory::getApplication()->input->get('view');
$layout	= JFactory::getApplication()->input->get('layout');


if ($layout == 'edit') {
	
} else {

	foreach ($l as $k => $v) {
		
		if ($v[1] == '') {
			$link = 'index.php?option=com_phocaemail';
		} else {
			$link = 'index.php?option=com_phocaemail&view=';
		}

		if ($view == $v[1]) {
			JHtmlSidebar::addEntry(JText::_($v[0]), $link.$v[1], true );
		} else {
			JHtmlSidebar::addEntry(JText::_($v[0]), $link.$v[1]);
		}

	}
}

class PhocaEmailCpController extends JControllerLegacy {
	function display($cachable = false, $urlparams = array()) {
		parent::display($cachable, $urlparams);
	}
}
?>
