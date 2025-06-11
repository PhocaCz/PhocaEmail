<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\Phocaemail\Administrator\Model;

\defined( '_JEXEC' ) or die();

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

class EmaillistModel extends AdminModel
{
	
	protected function canDelete($record) {
		$user = Factory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.delete', 'com_phocaemail.phocaemaillist.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}
	
	protected function canEditState($record){
		$user = Factory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_phocaemail.phocaemaillist.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		$form 	= $this->loadForm('com_phocaemail.emaillist', 'emaillist', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocaemail.edit.emaillist.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
}
