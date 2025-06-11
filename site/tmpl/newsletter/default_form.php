<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->registerAndUseStyle('com_phocaemail.main');

$layout 	= new FileLayout('newsletter_form', null, array('component' => 'com_phocaemail'));

echo '<div id="ph-newsletter-box" class="ph-newsletter-view'.$this->p->get( 'pageclass_sfx' ).'" >';

if ( $this->p->get( 'show_page_heading' ) ) {

    if ($this->p->get('page_heading') != '') {
	    echo '<h1>'. $this->escape($this->p->get('page_heading')) . '</h1>';
    } else {
        echo '<h1>' . Text::_('COM_PHOCAEMAIL_NEWSLETTER') . '</h1>';
    }
}

echo $this->t['text'];

if ($this->t['display_subscription_form'] == 1) {
	$d						= array();
	$d['params']			= $this->p;
	$d['mailing_list']		= $this->t['mailing_list'];
	$d['link_subscribe']	= PhocaEmailHelperRoute::getNewsletterRoute(0, 'subscribe');
	$d['extension-type']	= 'com';
	$d['value_email']       = $this->t['email_value'];// checked in view
	echo $layout->render($d);
}

echo '<div>&nbsp;</div>';// end of box


echo '</div>';
?>
