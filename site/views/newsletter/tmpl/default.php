<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

echo '<div id="ph-newsletter-box" class="ph-newsletter-view'.$this->p->get( 'pageclass_sfx' ).'" >';

if ( $this->p->get( 'show_page_heading' ) ) {

    if ($this->p->get('page_heading') != '') {
	    echo '<h1>'. $this->escape($this->p->get('page_heading')) . '</h1>';
    } else {
        echo '<h1>' . JText::_('COM_PHOCAEMAIL_NEWSLETTER') . '</h1>';
    }
}

echo $this->t['text'];
echo '<div>&nbsp;</div>';// end of box


echo '</div>';
?>
