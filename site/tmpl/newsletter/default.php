<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->registerAndUseStyle('com_phocaemail.main');

?>

<div id="ph-newsletter-box" class="ph-newsletter-view'<?php echo $this->p->get( 'pageclass_sfx' ); ?>" >

	<?php if ( $this->p->get( 'show_page_heading' ) ) {

			if ($this->p->get('page_heading') != '') { ?>
				<h1><?php echo $this->escape($this->p->get('page_heading')); ?></h1>
			<?php } else { ?>
				<h1><?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER'); ?></h1>
			<?php }
		} ?>

	<?php echo $this->t['text']; ?>

	<?php if(empty($this->task)) { 
		 echo $this->loadTemplate('form');
	} ?>

	<div>&nbsp;</div>

</div>
