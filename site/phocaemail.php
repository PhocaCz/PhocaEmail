<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemail.php');
require_once( JPATH_ADMINISTRATOR.'/components/com_phocaemail/helpers/phocaemailsendnewsletteremail.php');

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
    $path = JPATH_COMPONENT.'/controllers/'.$controller.'.php';
    if (file_exists($path)) {
        require_once $path;
    } else {
        $controller = '';
    }
}

$classname    = 'PhocaEmailController'.ucfirst($controller);
$controller   = new $classname( );
$controller->execute( JFactory::getApplication()->input->get('task') );
$controller->redirect();
?>