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

namespace Phoca\Component\Phocaemail\Administrator\Model;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

class EmailwriteModel extends AdminModel
{
	protected	$option 		= 'com_phocaemail';
	protected 	$text_prefix	= 'com_phocaemail';
	public 		$typeAlias 		= 'com_phocaemail.phocaemailwrite';

	public function getTable($type = 'phocaemail', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$form 	= $this->loadForm('com_phocaemail.emailwrite', 'emailwrite', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form)) {
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_phocaemail.edit.phocaemail.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null)
	{
		$item = new \stdClass();
		$app				= Factory::getApplication();
		$context			= 'com_phocaemail.write.';
		$item->article_id	= $app->getUserStateFromRequest( $context.'article_id', 'article_id', '', 'int' );

		return $item;
	}
}
