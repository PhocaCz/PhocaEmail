<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Phoca\Component\phocaemail\Administrator\Controller;

\defined('_JEXEC') or die();

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;

class EmaillistController extends FormController
{
	
	protected function allowAdd($data = array()) {
		$user		= Factory::getUser();
		$allow	= $user->authorise('core.create', 'com_phocaemail');
		if ($allow === null) {
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id') {
		$user		= Factory::getUser();
		$allow	= $user->authorise('core.edit', 'com_phocaemail');
		if ($allow === null) {
			return parent::allowEdit($data, $key);
		} else {
			return $allow;
		}
	}
}