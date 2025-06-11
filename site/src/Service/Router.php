<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_phocagallery
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


namespace Phoca\Component\Phocaemail\Site\Service;

defined('_JEXEC') or die;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Factory;
use Phoca\Component\Phocaemail\Site\Helper\RouterrulesHelper;
//require_once( JPATH_SITE.'/components/com_phocaemail/helpers/routerrules.php');

class Router extends RouterView
{
	protected $noIDs = false;

	/**
	 * Content Component router constructor
	 *
	 * @param   JApplicationCms  $app   The application object
	 * @param   JMenu            $menu  The menu object to work with
	 */
	public function __construct($app = null, $menu = null)
	{

		$newsletter = new RouterViewConfiguration('newsletter');
        //$newsletter->setKey('id');
		$this->registerView($newsletter);



		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
        $this->attachRule(new RouterrulesHelper($this));
	}
}

?>

