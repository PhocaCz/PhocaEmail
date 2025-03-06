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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$js = array();
$js[] =  ''
		.'Joomla.submitbutton = function(task) {'. "\n"
		.' var form = document.adminForm;'. "\n"
		.' if (task == \'phocaemailwrite.send\') {'. "\n"
		.'  if (form.from.value == ""){'. "\n"
		.'	 alert( "'. Text::_('COM_PHOCAEMAIL_ERROR_FIELD_FROM').'" );'. "\n"
		.'	} else if (form.fromname.value == ""){'. "\n"
		.'	 alert( "'. Text::_('COM_PHOCAEMAIL_ERROR_FIELD_FROMNAME').'" );'. "\n"
		.'	} ' . "\n";

if ($this->p['display_users_list'] == 0 && $this->p['display_groups_list'] == 0) {

	$js[] =  '      else if (form.to.value == ""){'. "\n";
	$js[] =  '	 alert( "'. Text::_('COM_PHOCAEMAIL_ERROR_FIELD_TO').'" );'. "\n";
	$js[] =  '	}' . "\n";

} else if ($this->p['display_users_list'] == 1 && $this->p['display_groups_list'] == 0) {

	$js[] =  '      else if (form.tousers.value == "" && form.to.value == "") { ' . "\n";
	$js[] =  '	 alert( "'. Text::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_USERS_EMPTY').'" );'. "\n";
	$js[] =  '  }' . "\n";

} else if ($this->p['display_users_list'] == 0 && $this->p['display_groups_list'] == 1) {

	$js[] =  '      else if (form.togroups.value == "" && form.to.value == "") { ' . "\n";
	$js[] =  '	 alert( "'. Text::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_GROUPS_EMPTY').'" );'. "\n";
	$js[] =  '  }' . "\n";

} else {

	// all displayed to, to users, to groups
	$js[] =  '      else if (form.tousers.value == "" && form.togroups.value == "" && form.to.value == "") { ' . "\n";
	$js[] =  '	 alert( "'. Text::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_USERS_GROUPS_EMPTY').'" );'. "\n";
	$js[] =  '  }' . "\n";

}

$js[] =  ' else if (form.subject.value == ""){'. "\n"
		.'   alert( "'. Text::_('COM_PHOCAEMAIL_ERROR_FIELD_SUBJECT').'" );'. "\n"
		.'	} else {'. "\n"
		.'	 Joomla.submitform(task);'. "\n"
		.'   document.getElementById(\'ph-op-bg\').style.display=\'block\';'. "\n"
		.'   document.getElementById(\'ph-op-bg-msg\').style.display=\'block\';'. "\n"
		.'	}'. "\n"
		.' } else {'. "\n"
		.'   Joomla.submitform(task);'. "\n"
		//.'      document.getElementById(\'sending-email\').style.display=\'block\';'. "\n"
		.' }'. "\n"
		.'}'. "\n";

Factory::getDocument()->addScriptDeclaration(implode('', $js));

?>

<form action="index.php?option=com_phocaemail&view=emailwrite" method="post" name="adminForm" id="adminForm">
	<table class="ph-table-form">
		<tr>
			<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_FROMNAME'); ?>:</label></td>
			<td class="ph-inputbox"><input class="form-control inputbox" type="text" name="fromname" id="fromname" style="width:300px" maxlength="100" value="<?php echo $this->re['fromname']; ?>" /></td>
		</tr>

		<tr>
			<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_FROM'); ?>:</label></td>
			<td class="ph-inputbox"><input class="form-control inputbox" type="text" name="from" id="from" style="width:300px" maxlength="100" value="<?php echo $this->re['from']; ?>" /></td>
		</tr>

		<tr>
			<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_TO'); ?>:</label></td>
			<td class="ph-inputbox"><input class="form-control inputbox" type="text" name="to" id="to" style="width:300px" maxlength="250" value="<?php echo $this->re['to']; ?>" /></td>
		</tr>

		<?php if ($this->p['display_users_list'] == 1) { ?>
			<tr>
				<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_TO_USERS'); ?>:</label></td>
				<td><?php echo $this->t['userlist']; ?></td>
			</tr>
		<?php } ?>

		<?php if ($this->p['display_groups_list'] == 1) { ?>
			<tr>
				<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_TO_GROUPS'); ?>:</label></td>
				<td><?php echo $this->t['grouplist']; ?></td>
			</tr>
		<?php } ?>

		<tr>
			<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_CC'); ?>:</label></td>
			<td class="ph-inputbox"><input class="form-control inputbox" type="text" name="cc" id="cc" style="width:300px" maxlength="250" value="<?php echo $this->re['cc']; ?>" /></td>
		</tr>

		<?php if ($this->p['display_users_list_cc'] == 1) { ?>
			<tr>
				<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_CC_USERS'); ?>:</label></td>
				<td><?php echo $this->t['ccuserlist']; ?></td>
			</tr>
		<?php } ?>

		<?php if ($this->p['display_groups_list_cc'] == 1) { ?>
			<tr>
				<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_CC_GROUPS'); ?>:</label></td>
				<td><?php echo $this->t['ccgrouplist']; ?></td>
			</tr>
		<?php } ?>

		<tr>
			<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_BCC'); ?>:</label></td>
			<td class="ph-inputbox"><input class="form-control inputbox" type="text" name="bcc" id="bcc" style="width:300px" maxlength="250" value="<?php echo $this->re['bcc']; ?>" /></td>
		</tr>

		<?php if ($this->p['display_users_list_bcc'] == 1) { ?>
			<tr>
				<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_BCC_USERS'); ?>:</label></td>
				<td><?php echo $this->t['bccuserlist']; ?></td>
			</tr>
		<?php } ?>

		<?php if ($this->p['display_groups_list_bcc'] == 1) { ?>
			<tr>
				<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_BCC_GROUPS'); ?>:</label></td>
				<td><?php echo $this->t['bccgrouplist']; ?></td>
			</tr>
		<?php } ?>

		<tr>
			<td class="ph-label"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_SUBJECT'); ?>:</label></td>
			<td class="ph-inputbox"><input class="form-control inputbox" type="text" name="subject" id="subject" style="width:634px" maxlength="250" value="<?php echo $this->re['subject']; ?>" /></td>
		</tr>

		<?php if ($this->p['display_select_article'] == 1) { ?>
			<tr>
				<td class="ph-label"><?php echo $this->form->getLabel('article_id') ?></td>
				<td class="ph-inputbox"><?php echo $this->form->getInput('article_id') ?></td>
			</tr>
		<?php } ?>

		<tr>
			<td class="ph-label" valign="top"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_MESSAGE');  ?>:</label></td>
			<td>
			<?php // parameters : areaname, content, width, height, cols, rows, show xtd buttons
			echo $this->t['editor']->display( 'message',  $this->re['message'], '648', '250', '80', '30', array('pagebreak', 'readmore', 'article') ) ;
			?></td>
		</tr>

		<tr>
			<td class="ph-label" valign="top"><label for="title"><?php echo Text::_('COM_PHOCAEMAIL_FIELD_ATTACHMENT');  ?>:</label></td>
			<?php
			if (!empty($this->t['attachment'])) {
				echo '<td>';
				foreach ($this->t['attachment'] as $key => $value) {

					echo '<input type="checkbox" '.$this->t['attachment'][$key]['checked'].' name="attachment['.$key.']" id="attachment-'.$key.'" >';
					echo '<input type="hidden" name="attachmentfile['.$key.']" id="attachmentfile-'.$key.'" value="'.$this->t['attachment'][$key]['file'].'" >';
					$attIco = 'icon-16-attachment.png';
					if (isset($this->t['attachment'][$key]['pdf']) && $this->t['attachment'][$key]['pdf'] == 1) {
						$attIco = 'icon-16-pdf.png';
					}
					echo HTMLHelper::_('image', 'media/com_phocaemail/images/administrator/'.$attIco, '')
					. ' ' .$this->t['attachment'][$key]['file'] . '<br />';
				}
				echo '</td>';
			}
			?>
		</tr>

		<tr>
			<td class="ph-label" valign="top" colspan="2">
				<div class="btn-wrapper" >
					<button onclick="Joomla.submitbutton('phocaemailwrite.send');return false;" class="btn btn-success">
					<span class="icon-envelope"></span> <?php echo Text::_('COM_PHOCAEMAIL_SEND'); ?></button>
				</div>
			</td>
		</tr>

	</table>

	<?php if ($this->re['ext'] == 'virtuemart') { ?>
		<input type="hidden" name="order_id" value="<?php echo $this->re['order_id']; ?>" />
		<input type="hidden" name="delivery_id" value=<?php echo $this->re['delivery_id']; ?>" />
		<input type="hidden" name="type" value="<?php echo $this->re['type']; ?>" />
		<input type="hidden" name="ext" value="<?php echo $this->re['ext']; ?>" />
	<?php } ?>

	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>

<div id="ph-op-bg"></div>
<div id="ph-op-bg-msg">
	<?php echo HTMLHelper::_('image', 'media/com_phocaemail/images/administrator/icon-sending.gif', '' )
				. ' &nbsp; &nbsp; ' . Text::_('COM_PHOCAEMAIL_SENDING_MESSAGE'); ?>
</div>
