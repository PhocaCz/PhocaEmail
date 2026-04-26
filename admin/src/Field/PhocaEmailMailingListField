<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
namespace Phoca\Component\Phocaemail\Administrator\Field;

\defined('_JEXEC') or die();

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

class PhocaEmailMailingListField extends ListField
{
	protected $type = 'MailingList';

		/**
		 * @return	Array of JHtml options for selection box
		 */
	protected function getOptions() {
		$db = $this->getDatabase();
		$query = $db->createQuery();
		$query->select($db->qn(['id', 'title']))
			->from('#__phocaemail_lists')
			->order('title');
		$db->setQuery($query);
		$results = $db->loadAssocList();
		
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_PHOCAEMAIL_SELECT_MAILING_LIST'));
		foreach($results as $result) {
			$options[] = HTMLHelper::_('select.option', $result['id'], $result['title']);
		}

		return $options;
	}
}
