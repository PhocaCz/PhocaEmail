<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$r = $this->r;

?>

<?php echo $r->startCp(); ?>

	<div class="ph-box-cp">
		<div class="ph-left-cp">

			<div class="ph-cp-item-box">
				<?php $link	= 'index.php?option='.$this->t['o'].'&view=';
				foreach ($this->views as $k => $v) {
					$linkV	= $link . /*$this->t['c'] . */ $k;
					echo $r->quickIconButton( $linkV, Text::_($v[0]), $v[1], $v[2]);
				} ?>
			</div>
		</div>

		<div class="ph-right-cp">

			<div class="ph-extension-info-box">
				<div class="ph-cpanel-logo"><?php echo HTMLHelper::_('image', $this->t['i'] . 'logo-'.str_replace('phoca', 'phoca-', $this->t['c']).'.png', 'Phoca.cz'); ?></div>
				<div style="float:right;margin:10px;"><?php echo HTMLHelper::_('image', $this->t['i'] . 'logo-phoca.png', 'Phoca.cz' ); ?></div>

				<h3><?php echo Text::_($this->t['l'] . '_VERSION'); ?></h3>
				<p><?php echo  $this->t['version']; ?></p>

				<h3><?php echo Text::_($this->t['l'] . '_COPYRIGHT'); ?></h3>
				<p>© 2007 - <?php echo date("Y"); ?> Jan Pavelka</p>
				<p><a href="https://www.phoca.cz/" target="_blank">www.phoca.cz</a></p>

				<h3><?php echo Text::_($this->t['l'] . '_LICENSE'); ?></h3>
				<p><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></p>

				<h3><?php echo Text::_($this->t['l'] . '_TRANSLATION') . ': ' . Text::_($this->t['l'] . '_TRANSLATION_LANGUAGE_TAG'); ?></h3>
				<p>© 2007 - <?php echo date("Y") . ' ' . Text::_($this->t['l'] . '_TRANSLATER'); ?></p>
				<p><?php echo Text::_($this->t['l'] . '_TRANSLATION_SUPPORT_URL'); ?></p>

				<div class="ph-cp-hr"></div>
				<div class="btn-group ph-cp-btn-update">
					<a class="btn btn-large btn-primary" 
					   href="https://www.phoca.cz/version/index.php?<?php echo $this->t['c'] . '=' . $this->t['version']; ?>"
					   target="_blank"><i class="icon-loop icon-white"></i>&nbsp;&nbsp;<?php echo Text::_($this->t['l'] . '_CHECK_FOR_UPDATE'); ?>
					</a>
				</div>

				<div class="ph-cp-logo-footer">
					<a href="https://www.phoca.cz/" target="_blank">
						<?php echo HtmlHelper::_('image', $this->t['i'] . 'logo.png', 'Phoca.cz' ); ?>
					</a>
				</div>
				<div class="ph-cb"></div>
			</div>

			<div class="ph-extension-links-box">
				<?php echo $r->getLinks(); ?>
			</div>

		</div>

	</div>
<?php echo $r->endCp();
