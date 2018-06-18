<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
 defined('_JEXEC') or die('Restricted access');
class PhocaEmailListsHelper
{
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_phocaemail';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	public static function options() {
		
		// Initialize variables.
	/*	$html = array();
		
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$maxLength	= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$readonly	= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled	= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$manager	= $this->element['manager'] ? $this->element['manager'] : '';
		$javascript	= NULL;*/
		
		$order 	= 'ordering ASC';
		$db 	= JFactory::getDBO();
		$query 	= 'SELECT a.id AS value, a.title AS text'
				.' FROM #__phocaemail_lists AS a'
				. ' ORDER BY '. $order;
		$db->setQuery($query);
		$lists = $db->loadObjectList();
		
		return $lists;
		
	/*	$activeArray 	= array();
		$id 			= (int) $this->form->getValue('id');
		if ((int)$id > 0) {
		
			switch($manager){
				case 'subscriber':
					$table 	= '#__phocaemail_subscriber_lists';
					$item	= 'a.id_subscriber';
				break;
				case 'newsletter':
				default:
					$table = '#__phocaemail_newsletter_lists';
					$item	= 'a.id_newsletter';
				break;
			}
			
			$query = ' SELECT a.id_list FROM '.$table.' AS a'
			    .' WHERE '.$item.' = '.(int) $id;
			$db->setQuery($query);
			$activeArray = $db->loadColumn();
		}*/
		
	
		
		//$html = JHTML::_('select.genericlist', $lists, $this->name.'[]', 'class="inputbox" size="4" multiple="multiple"'. $javascript, 'value', 'text', $activeArray, 'id');
		
	//	return $html;
	}
}
?>