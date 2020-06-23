<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die();
jimport('joomla.application.component.modeladmin');

class PhocaEmailCpModelPhocaEmailSubscriber extends JModelAdmin
{
	protected	$option 		= 'com_phocaemail';
	protected 	$text_prefix	= 'com_phocaemail';
	public 		$typeAlias 		= 'com_phocaemail.phocaemailsubscriber';

	protected function canDelete($record) {
		$user = JFactory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.delete', 'com_phocaemail.phocaemailsubscriber.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record){
		$user = JFactory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_phocaemail.phocaemailsubscriber.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}

	public function getTable($type = 'PhocaEmailSubscriber', $prefix = 'Table', $config = array()){
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$app	= JFactory::getApplication();
		$form 	= $this->loadForm('com_phocaemail.phocaemailsubscriber', 'phocaemailsubscriber', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_phocaemail.edit.phocaemailsubscriber.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{

		$table = $this->getTable();

		if ((!empty($data['tags']) && $data['tags'][0] != ''))
		{
			$table->newTags = $data['tags'];
		}

	/*	if ($data['active'] == 1 && !isset($data['date_register'])) {

			$date 			= gmdate('Y-m-d H:i:s');
			$db	= JFactory::getDBO();

			$query = 'SELECT a.active FROM #__phocaemail_subscribers AS a'
					. ' WHERE a.id = '.(int)$data['id']
					. ' LIMIT 1';
			$db->setQuery( (string)$query );
			$subscriber = $db->loadObject();
			if (isset($subscriber->active) && $subscriber->active == 1) {
				// Already active, don't set date for activation
			} else {
				$data['date_register'] = $date;
			}

		}*/

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;


		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

			if ($data['date_register'] == '') { $data['date_register'] = '0000-00-00 00:00:00';}
			if ($data['date_active'] == '') { $data['date_active'] = '0000-00-00 00:00:00';}
			if ($data['date_unsubscribe'] == '') { $data['date_unsubscribe'] = '0000-00-00 00:00:00';}

			// User is active but we didn't set the activation date yet
			if ($data['active'] == 1 && ($data['date_active'] == '' || $data['date_active'] == '0000-00-00 00:00:00')) {
				$data['date_active'] = gmdate('Y-m-d H:i:s');
			}

			// User is unsubscribed but we didn't set the unsubscribe date yet (e.g. unsubscribe per admin)
			if ($data['active'] == 2 && ($data['date_unsubscribe'] == '' || $data['date_unsubscribe'] == '0000-00-00 00:00:00')) {
				$data['date_unsubscribe'] = gmdate('Y-m-d H:i:s');
			}

			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());

				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = \JFactory::getApplication()->triggerEvent($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew, $data));

			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}




			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			//PHOCAEDIT
			if (empty($data['mailinglist'])) {
				$data['mailinglist'] = array();
			}


			PhocaEmailSendNewsletterEmail::storeLists($data['id'], $data['mailinglist'], '#__phocaemail_subscriber_lists', 'id_subscriber');

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			\JFactory::getApplication()->triggerEvent($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew, $data));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);

		return true;
	}

	public function delete(&$pks)
	{

		$pks = (array) $pks;
		$table = $this->getTable();

		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{

			if ($table->load($pk))
			{

				if ($this->canDelete($table))
				{

					$context = $this->option . '.' . $this->name;

					// Trigger the onContentBeforeDelete event.
					$result = \JFactory::getApplication()->triggerEvent($this->event_before_delete, array($context, $table));

					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					}

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}

					// Trigger the onContentAfterDelete event.
					\JFactory::getApplication()->triggerEvent($this->event_after_delete, array($context, $table));

				}
				else
				{

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						JLog::add($error, JLog::WARNING, 'jerror');
						return false;
					}
					else
					{
						JLog::add(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
						return false;
					}
				}

			}
			else
			{
				$this->setError($table->getError());
				return false;
			}

			//PHOCAEDIT
			if ((int)$pk > 0) {
				$db = JFactory::getDBO();
				$query = ' DELETE '
						.' FROM #__phocaemail_subscriber_lists'
						. ' WHERE id_subscriber = '. (int)$pk;
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Clear the component's cache
		$this->cleanCache();



		return true;
	}
}
?>
