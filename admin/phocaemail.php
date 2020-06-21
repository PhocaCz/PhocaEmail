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
defined( '_JEXEC' ) or die( 'Restricted access' );

// Require the base controller and helpers
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once( JPATH_COMPONENT.'/controller.php' );
require_once( JPATH_COMPONENT.'/helpers/phocaemailutils.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminview.php' );
require_once( JPATH_COMPONENT.'/helpers/renderadminviews.php' );
require_once( JPATH_COMPONENT.'/helpers/phocaemail.php' );
require_once( JPATH_COMPONENT.'/helpers/phocaemailcp.php' );
require_once( JPATH_COMPONENT.'/helpers/phocaemailsend.php' );
require_once( JPATH_COMPONENT.'/helpers/phocaemailsendnewsletteremail.php' );


jimport('joomla.application.component.controller');
$controller	= JControllerLegacy::getInstance('PhocaEmailCp');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
