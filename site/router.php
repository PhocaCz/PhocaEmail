<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
function PhocaEmailBuildRoute(&$query)
{
	$segments = array();
	
	if(isset($query['view'])) {
		//$segments[] = $query['view'];
		unset($query['view']);
	}
	
	if(isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	
	if(isset($query['u'])) {
		$segments[] = $query['u'];
		unset($query['u']);
	}
	
	if(isset($query['n'])) {
		$segments[] = $query['n'];
		unset($query['n']);
	}
	
	if(isset($query['tmpl'])) {
		$segments[] = $query['tmpl'];
		unset($query['tmpl']);
	}
	
	


	
	//unset($query['view']);

	return $segments;
}

function PhocaEmailParseRoute($segments)
{	
	
	/*
	 * Be aware see components/com_phocaemail/phoacaemail.php
	 * the view "newsletter" is forced in case no view is set
	 */

	$vars = array();
	
	//Get the active menu item

	$app 	= JFactory::getApplication('site');
	$menu  = $app->getMenu();
	$item	= $menu->getActive();
	// Count route segments
	$count = count($segments);


	
	//Handle View and Identifier
	if (isset($item->query['view'])) {
		switch($item->query['view']) {	
			case 'newsletter'   :
				$vars['view']	= 'newsletter';
			break;
		}
	} else {
		// For now only one view
		$vars['view']	= 'newsletter';
	}
	
	
	if ($count == 1) {
	//	$vars['view']	= $segments[$count-1];
	}
	
	if ($count == 1) {
	//	$vars['view']	= $segments[$count-2];
		$vars['task']	= $segments[$count-1];
	}
	
	if ($count == 2) {
	//	$vars['view']	= $segments[$count-3];
		$vars['task']	= $segments[$count-2];
		$vars['u']		= $segments[$count-1];// user
		
	}
	
	if ($count == 3) {
	//	$vars['view']	= $segments[$count-4];
		$vars['task']	= $segments[$count-3];
		$vars['u']		= $segments[$count-2];
		$vars['n']		= $segments[$count-1];
	}
	
	if ($count == 4) {
	//	$vars['view']	= $segments[$count-5];
		$vars['task']	= $segments[$count-4];
		$vars['u']		= $segments[$count-3];
		$vars['n']		= $segments[$count-2];
		$vars['tmpl']	= $segments[$count-1];
	}
	
	if ($count > 4) {
	//	$vars['view']	= $segments[0];
		$vars['task']	= $segments[1];
		$vars['u']		= $segments[2];
		$vars['n']		= $segments[3];
		$vars['tmpl']	= $segments[4];
	}
	

	return $vars;
}
?>