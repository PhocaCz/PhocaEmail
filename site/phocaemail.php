<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );


require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemail.php');
require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemaillists.php');
require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemailsendnewsletteremail.php');

// Require specific controller if requested

if($controller = JFactory::getApplication()->input->get( 'controller')) {
    $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}


// Force newsletter view in case no menu link to phoca email is set
// and no task is set
$view = JFactory::getApplication()->input->get('view');
if (!$view) {
	JFactory::getApplication()->input->set('view', 'newsletter');
}


$controller = JControllerLegacy::getInstance('PhocaEmail');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>