<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Phoca\Component\Phocaemail\Administrator\Helper\UtilsHelper;



$d 		= $displayData;

$display_name_form				= $d['params']->get( 'display_name_form', 2 );
$display_privacy_checkbox_form	= $d['params']->get( 'display_privacy_checkbox_form', 0 );
$privacy_checkbox_text			= $d['params']->get( 'privacy_checkbox_text', '' );
$enable_captcha					= $d['params']->get( 'enable_captcha', 0 );
$session_suffix					= $d['params']->get( 'session_suffix', '' );


// Sent by module and checked in newsletter view for correctness
$valueEmail = isset($d['value_email']) && $d['value_email'] != '' ? $d['value_email'] : '';

// Security
$session 	= Factory::getSession();
$namespace  = 'pheml' . $session_suffix . $d['extension-type'];
$string 	= bin2hex(openssl_random_pseudo_bytes(10));
$session->set('form_id_'.$d['extension-type'], $string, $namespace);



echo '<form action="'.Route::_($d['link_subscribe']).'" method="post" id="ph-subscribe-form-'.$d['extension-type'].'" class="form-inline">';

echo '<div class="userdata">';


if ((int)$display_name_form > 0) {

	$required 	= $display_name_form == 2 ? 'aria-required="true" required' : '';
	$requiredS	= $display_name_form == 2 ? '<span class="star">&nbsp;*</span>' : '';
	$requiredC	= $display_name_form == 2 ? 'required' : '';

	echo '<div id="ph-form-subscribe-name" class="input-group">';
	echo '<span class="input-group-text" title="'. Text::_('COM_PHOCAEMAIL_NEWSLETTER_NAME').'">';
	echo '<span class="icon-user hasTooltip"></span>';
	echo '</span>';
	echo '<input id="ph-mod-name" type="text" name="name" class="form-control '.$requiredC.'" tabindex="0" size="18" placeholder="'. Text::_('COM_PHOCAEMAIL_NEWSLETTER_NAME') .'" value="" '.$required.' />';
	echo '</div>';
}

$required 	= 'aria-required="true" required';
$requiredS	= '<span class="star">&nbsp;*</span>';
$requiredC	= 'required';

echo '<div id="ph-form-subscribe-email" class="input-group">';
echo '<span class="input-group-text" title="'. Text::_('COM_PHOCAEMAIL_NEWSLETTER_EMAIL').'">';
echo '<span class="icon-mail hasTooltip"></span>';
echo '</span>';
echo '<input id="ph-mod-email" type="email" name="email" class="form-control '.$requiredC.'" tabindex="0" size="18" placeholder="'. Text::_('COM_PHOCAEMAIL_NEWSLETTER_EMAIL') .'" value="'.$valueEmail.'" '.$required.' />';
echo '</div>';




if (!empty($d['mailing_list'])) {
	echo '<div id="ph-form-subscribe-mailinglist" class="control-group">';
	echo '<div class="controls" style="margin:10px;">';

	foreach($d['mailing_list'] as $k => $v) {
		echo '<div class="checkbox">';
		echo '<label>';
		echo '<input type="checkbox" name="mailinglist[]" value="'.(int)$v->value.'"> '.$v->text;
		echo '</label>';
		echo '</div>';
		echo '<div style="clear:both"></div>';
	}
	echo '</div></div>';
}

if ($enable_captcha == 1) {

	$required 	= 'aria-required="true" required';
	$requiredS	= '<span class="star">&nbsp;*</span>';

	echo '<div class="control-group">';

	echo '<div class="control-label">';
	echo '<label id="phemailcaptcha-lbl" for="phemailcaptcha" class="hasPopover required" title="" data-content="'.Text::_('COM_PHOCAEMAIL_PLEASE_PROVE_THAT_YOU_ARE_HUMAN').'" data-original-title="'.Text::_('COM_PHOCAEMAIL_SECURITY_CHECK').'">'.Text::_('COM_PHOCAEMAIL_SECURITY_CHECK').$requiredS.'</label>';
	echo '</div>';

	echo '<div class="controls">';
	echo UtilsHelper::renderReCaptcha();
	echo '</div>';

	echo '</div>';// end control group

}

if ((int)$display_privacy_checkbox_form > 0) {

	$required 	= $display_privacy_checkbox_form == 2 ? 'aria-required="true" required' : '';
	$requiredS	= $display_privacy_checkbox_form == 2 ? '<span class="star">&nbsp;*</span>' : '';
	echo '<div id="ph-form-subscribe-privacy-'.$d['extension-type'].'" class="control-group">';
	echo '<div class="controls" style="margin:10px;">';


		echo '<div class="checkbox ph-email-privacy-checkbox">';
		echo '<label>';
		echo '<input type="checkbox" name="privacy" '.$required.'> '.$privacy_checkbox_text . $requiredS;
		echo '</label>';
		echo '</div>';
		echo '<div style="clear:both"></div>';

	echo '</div></div>';
}

echo '<div id="ph-form-subscribe-submit" class="control-group">';
echo '<div class="controls">';
echo '<button type="submit" tabindex="0" name="submit" class="btn btn-primary">'. Text::_('COM_PHOCAEMAIL_NEWSLETTER_SUBSCRIBE') .'</button>';
echo '</div>';
echo '</div>';

echo '</div>';


echo '<input type="hidden" name="option" value="com_phocaemail" />';
echo '<input type="hidden" name="view" value="newsletter" />';
echo '<input type="hidden" name="task" value="newsletter.subscribe" />';
echo  HTMLHelper::_('form.token');

echo '</form>';
