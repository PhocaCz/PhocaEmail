<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\phocaemail\Administrator\Model;

\defined( '_JEXEC' ) or die();

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Language\Text;
use Phoca\Component\phocaemail\Administrator\Helper\SendnewsletteremailHelper;

class NewsletterModel extends AdminModel
{

	protected function canDelete($record)
	{
		$user = Factory::getUser();

		if (!empty($record->catid)) {
			return $user->authorise('core.delete', 'com_phocaemail.phocaemailnewsletter.'.(int) $record->catid);
		} else {
			return parent::canDelete($record);
		}
	}

	protected function canEditState($record)
	{
		$user = Factory::getUser();

		if (!empty($record->catid))
		{
			return $user->authorise('core.edit.state', 'com_phocaemail.phocaemailnewsletter.'.(int) $record->catid);
		} else {
			return parent::canEditState($record);
		}
	}

	public function getForm($data = array(), $loadData = true)
	{
		$form 	= $this->loadForm('com_phocaemail.newsletter', 'newsletter', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocaemail.edit.newsletter.data', array());

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

		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		PluginHelper::importPlugin('content');

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
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
			$result = Factory::getApplication()->triggerEvent($this->event_before_save, array($this->option . '.' . $this->name, $table, $isNew, $data));

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
			SendnewsletteremailHelper::storeLists($data['id'], $data['mailinglist'], '#__phocaemail_newsletter_lists', 'id_newsletter');

			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			Factory::getApplication()->triggerEvent($this->event_after_save, array($this->option . '.' . $this->name, $table, $isNew, $data));
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
		PluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{

			if ($table->load($pk))
			{

				if ($this->canDelete($table))
				{

					$context = $this->option . '.' . $this->name;

					// Trigger the onContentBeforeDelete event.
					$result = Factory::getApplication()->triggerEvent($this->event_before_delete, array($context, $table));

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
					Factory::getApplication()->triggerEvent($this->event_after_delete, array($context, $table));

				}
				else
				{

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						Log::add($error, Log::WARNING, 'jerror');
						return false;
					}
					else
					{
						Log::add(Text::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'), Log::WARNING, 'jerror');
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
				$db = Factory::getDBO();
				$query = ' DELETE '
						.' FROM #__phocaemail_newsletter_lists'
						. ' WHERE id_newsletter = '. (int)$pk;
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}