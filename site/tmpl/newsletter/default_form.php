<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Phoca\Component\phocaemail\Site\Helper\RouteHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Phoca\Component\phocaemail\Administrator\Helper\UtilsHelper;
use Joomla\CMS\HTML\HTMLHelper;

?>

<div id="ph-newsletter-box" class="ph-newsletter-view <?php $this->p->get( 'pageclass_sfx'); ?>">

	<?php if ( $this->p->get( 'show_page_heading' ) ) :
		if ($this->p->get('page_heading') != '') : ?>
			<h1><?php echo $this->escape($this->p->get('page_heading')); ?></h1>
		<?php else : ?>
			<h1><?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER'); ?></h1>
		<?php endif ?>
	<?php endif ?>

	<?php echo $this->t['text']; ?>

	<?php if ($this->t['display_subscription_form'] == 1) {
		$d						= array();
		$d['params']			= $this->p;
		$d['mailing_list']		= \array_key_exists('mailing_list', $this->t) ? $this->t['mailing_list'] : [];
		$d['link_subscribe']	= RouteHelper::getNewsletterRoute(0, 'subscribe');
		$d['extension-type']	= 'com';
		$d['value_email']       = $this->t['email_value'];	// checked in view

		$display_name_form				= \array_key_exists('display_name_form', $this->t) ? $this->t['display_name_form'] : 2;
		$display_privacy_checkbox_form	= \array_key_exists('display_privacy_checkbox_form', $this->t) ? $this->t['display_privacy_checkbox_form'] :  0;
		$privacy_checkbox_text			= \array_key_exists('privacy_checkbox_text', $this->t) ? $this->t['privacy_checkbox_text'] : '';
		$enable_captcha					= \array_key_exists('enable_captcha', $this->t) ? $this->t['enable_captcha'] : 0;
		$session_suffix					= \array_key_exists('session_suffix', $this->t) ? $this->t['session_suffix'] : '';

			// Sent by module and checked in newsletter view for correctness
		$valueEmail = isset($d['value_email']) && $d['value_email'] != '' ? $d['value_email'] : '';

			// Security
		$session 	= Factory::getSession();
		$namespace  = 'pheml' . $session_suffix . $d['extension-type'];
		$string 	= bin2hex(openssl_random_pseudo_bytes(10));
		$session->set('form_id_'.$d['extension-type'], $string, $namespace); ?>

		<form action="<?php echo Route::_($d['link_subscribe']); ?>" method="post" 
			  id="ph-subscribe-form-<?php echo $d['extension-type']; ?>" class="form-inline">

			<div class="userdata">
				<?php if ((int)$display_name_form > 0) {

					$required 	= $display_name_form == 2 ? 'aria-required="true" required' : '';
					$requiredS	= $display_name_form == 2 ? '<span class="star">&nbsp;*</span>' : '';
					$requiredC	= $display_name_form == 2 ? 'required' : ''; ?>

				<div id="ph-form-subscribe-name" class="input-group">
					<span class="input-group-text" title="<?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER_NAME'); ?>">
						<span class="icon-user hasTooltip"></span>
					</span>
					<input id="ph-mod-name" type="text" name="name" class="form-control <?php echo $requiredC; ?>" 
						   tabindex="0" size="18" placeholder="<?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER_NAME'); ?>" 
						   value="" <?php echo $required; ?>/>
				</div>
				<?php } ?>

				<?php 
					$required 	= 'aria-required="true" required';
					$requiredS	= '<span class="star">&nbsp;*</span>';
					$requiredC	= 'required';
				?>

				<div id="ph-form-subscribe-email" class="input-group">
					<span class="input-group-text" title="<?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER_EMAIL'); ?>">
						<span class="icon-mail hasTooltip"></span>;
					</span>
					<input id="ph-mod-email" type="email" name="email" class="form-control <?php echo $requiredC; ?>"
						   tabindex="0" size="18" placeholder="<?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER_EMAIL'); ?>"
						   value="<?php echo $valueEmail; ?>" <?php echo $required; ?>/>
				</div>

			<?php if (!empty($d['mailing_list'])) { ?>
				<div id="ph-form-subscribe-mailinglist" class="control-group">
					<div class="controls" style="margin:10px;">

						<?php foreach($d['mailing_list'] as $k => $v) { ?>
							<div class="checkbox">
								<label>
									<input type="checkbox" name="mailinglist[]" value="<?php echo (int)$v->value . '">' .$v->text; ?>"
								</label>
							</div>
							<div style="clear:both"></div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

		<?php if ($enable_captcha == 1) {
			$required 	= 'aria-required="true" required';
			$requiredS	= '<span class="star">&nbsp;*</span>'; ?>

			<div class="control-group">
				<div class="control-label">
					<label id="phemailcaptcha-lbl" for="phemailcaptcha" class="hasPopover required" title="" 
						   data-content="<?php echo Text::_('COM_PHOCAEMAIL_PLEASE_PROVE_THAT_YOU_ARE_HUMAN'); ?>"
						   data-original-title="<?php echo Text::_('COM_PHOCAEMAIL_SECURITY_CHECK'); ?>">
						   <?php echo Text::_('COM_PHOCAEMAIL_SECURITY_CHECK') . $requiredS ?>
						</label>
				</div>

				<div class="controls">
					<?php echo UtilsHelper::renderReCaptcha(); ?>
				</div>
			</div>
		<?php } ?>

		<?php if ((int)$display_privacy_checkbox_form > 0) {
			$required 	= $display_privacy_checkbox_form == 2 ? 'aria-required="true" required' : '';
			$requiredS	= $display_privacy_checkbox_form == 2 ? '<span class="star">&nbsp;*</span>' : ''; ?>

			<div id="ph-form-subscribe-privacy-'<?php echo $d['extension-type']; ?>" class="control-group">
				<div class="controls" style="margin:10px;">
					<div class="checkbox ph-email-privacy-checkbox">
						<label>
							<input type="checkbox" name="privacy" <?php echo $required;?>><?php echo $privacy_checkbox_text . $requiredS; ?>
						</label>
					</div>
					<div style="clear:both"></div>
				</div>
			</div>
		<?php } ?>

			<div id="ph-form-subscribe-submit" class="control-group">
				<div class="controls">
					<button type="submit" tabindex="0" name="submit" class="btn btn-primary">
						<?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER_SUBSCRIBE'); ?>
					</button>
				</div>
			</div>

		</div>

		<input type="hidden" name="option" value="com_phocaemail" />
		<input type="hidden" name="view" value="newsletter" />
		<input type="hidden" name="task" value="newsletter.subscribe" />
		<?php echo  HTMLHelper::_('form.token'); ?>
	</form>

	<?php } ?>

	<div>&nbsp;</div>	<?php // end of box ?>
</div>
