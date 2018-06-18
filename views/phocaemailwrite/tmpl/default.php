<?php defined('_JEXEC') or die('Restricted access');
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */ ?>
<?php JHTML::_('behavior.tooltip'); ?>

<script language="javascript" type="text/javascript">
<?php
echo ''
.'Joomla.submitbutton = function(task) {'. "\n"
.' var form = document.adminForm;'. "\n"
.' if (task == \'phocaemailwrite.send\') {'. "\n"
.'  if (form.from.value == ""){'. "\n"
.'	 alert( "'. JText::_('COM_PHOCAEMAIL_ERROR_FIELD_FROM').'" );'. "\n"
.'	} else if (form.fromname.value == ""){'. "\n"
.'	 alert( "'. JText::_('COM_PHOCAEMAIL_ERROR_FIELD_FROMNAME').'" );'. "\n"
.'	} ' . "\n";

if ($this->p['display_users_list'] == 0 && $this->p['display_groups_list'] == 0) {
	
	echo '      else if (form.to.value == ""){'. "\n";
	echo '	 alert( "'. JText::_('COM_PHOCAEMAIL_ERROR_FIELD_TO').'" );'. "\n";
	echo '	}' . "\n";
	
} else if ($this->p['display_users_list'] == 1 && $this->p['display_groups_list'] == 0) {
	
	echo '      else if (form.tousers.value == "" && form.to.value == "") { ' . "\n";
	echo '	 alert( "'. JText::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_USERS_EMPTY').'" );'. "\n";
	echo '  }' . "\n";
	
} else if ($this->p['display_users_list'] == 0 && $this->p['display_groups_list'] == 1) {
	
	echo '      else if (form.togroups.value == "" && form.to.value == "") { ' . "\n";
	echo '	 alert( "'. JText::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_GROUPS_EMPTY').'" );'. "\n";
	echo '  }' . "\n";
	
} else {
	
	// all displayed to, to users, to groups
	echo '      else if (form.tousers.value == "" && form.togroups.value == "" && form.to.value == "") { ' . "\n";
	echo '	 alert( "'. JText::_('COM_PHOCAEMAIL_ERROR_FIELD_TO_USERS_GROUPS_EMPTY').'" );'. "\n";
	echo '  }' . "\n";
	
}
echo ' else if (form.subject.value == ""){'. "\n"
.'   alert( "'. JText::_('COM_PHOCAEMAIL_ERROR_FIELD_SUBJECT').'" );'. "\n"
.'	} else {'. "\n"
.'	 Joomla.submitform(task);'. "\n"
.'   document.getElementById(\'ph-op-bg\').style.display=\'block\';'. "\n"
.'   document.getElementById(\'ph-op-bg-msg\').style.display=\'block\';'. "\n"
.'	}'. "\n"
.' } else {'. "\n"
.'   Joomla.submitform(task);'. "\n"
//.'      document.getElementById(\'sending-email\').style.display=\'block\';'. "\n"
.' }'. "\n"
.'}'. "\n"
?>
</script>

<form action="index.php?option=com_phocaemail&view=phocaemailwrite" method="post" name="adminForm" id="adminForm">
<table class="ph-table-form">
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_FROMNAME'); ?>:</label></td>
	<td class="ph-inputbox"><input class="inputbox" type="text" name="fromname" id="fromname" style="width:300px" maxlength="100" value="<?php echo $this->r['fromname']; ?>" /></td>
</tr>
	
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_FROM'); ?>:</label></td>
	<td class="ph-inputbox"><input class="inputbox" type="text" name="from" id="from" style="width:300px" maxlength="100" value="<?php echo $this->r['from']; ?>" /></td>
</tr>
	
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_TO'); ?>:</label></td>
	<td class="ph-inputbox"><input class="inputbox" type="text" name="to" id="to" style="width:300px" maxlength="250" value="<?php echo $this->r['to']; ?>" /></td>
</tr>

<?php if ($this->p['display_users_list'] == 1) { ?>
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_TO_USERS'); ?>:</label></td>
	<td><?php echo $this->t['userlist']; ?></td>
</tr>
<?php } ?>

<?php if ($this->p['display_groups_list'] == 1) { ?>
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_TO_GROUPS'); ?>:</label></td>
	<td><?php echo $this->t['grouplist']; ?></td>
</tr>
<?php } ?>

<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_CC'); ?>:</label></td>
	<td class="ph-inputbox"><input class="inputbox" type="text" name="cc" id="cc" style="width:300px" maxlength="250" value="<?php echo $this->r['cc']; ?>" /></td>
</tr>

<?php if ($this->p['display_users_list_cc'] == 1) { ?>
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_CC_USERS'); ?>:</label></td>
	<td><?php echo $this->t['ccuserlist']; ?></td>
</tr>
<?php } ?>

<?php if ($this->p['display_groups_list_cc'] == 1) { ?>
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_CC_GROUPS'); ?>:</label></td>
	<td><?php echo $this->t['ccgrouplist']; ?></td>
</tr>
<?php } ?>

<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_BCC'); ?>:</label></td>
	<td class="ph-inputbox"><input class="inputbox" type="text" name="bcc" id="bcc" style="width:300px" maxlength="250" value="<?php echo $this->r['bcc']; ?>" /></td>
</tr>

<?php if ($this->p['display_users_list_bcc'] == 1) { ?>
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_BCC_USERS'); ?>:</label></td>
	<td><?php echo $this->t['bccuserlist']; ?></td>
</tr>
<?php } ?>

<?php if ($this->p['display_groups_list_bcc'] == 1) { ?>
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_BCC_GROUPS'); ?>:</label></td>
	<td><?php echo $this->t['bccgrouplist']; ?></td>
</tr>
<?php } ?>
	
<tr>	
	<td class="ph-label"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_SUBJECT'); ?>:</label></td>
	<td class="ph-inputbox"><input class="inputbox" type="text" name="subject" id="subject" style="width:634px" maxlength="250" value="<?php echo $this->r['subject']; ?>" /></td>
</tr>

<?php if ($this->p['display_select_article'] == 1) { 

	JHtml::_('behavior.modal', 'a.modal_jform_request_id');
	
	$idA 	= 'phSelectArticle';
	$id		= 'jform_request_id';
	$link 	= 'index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$id.'&amp;'.JSession::getFormToken().'=1';
	
	$s 		= array();
	$s[] 	= '	function jSelectArticle_'.$id.'(id, title, catid, object) {';
	$s[] 	= '		document.id("article_id").value = id;';
	$s[] 	= '		document.id("article_name").value = title;';
	$s[] 	= '   		jQuery(\'.modal\').modal(\'hide\');';
	$s[] 	= '	}';
	JFactory::getDocument()->addScriptDeclaration(implode("\n", $s));
	
	$html 	= array(); 
	$html[] = '<div class="input-append">';
	$html[] = '<span class="input-append"><input type="text" id="article_name" name="article_name"'
			. ' value="' .  $this->r['article_name'] . '" class="input-medium"  />';
	$html[] = '<a href="#'.$idA.'" role="button" class="btn " data-toggle="modal" title="' . JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE') . '">'
			. '<span class="icon-list icon-white"></span> '
			. JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE') . '</a></span>';
	$html[] = '</div>'. "\n";
	$html[] = '<input required="required" aria-required="true" type="hidden" id="article_id" name="article_id" value="'.$this->r['article_id'].'" />';
	
	$html[] = JHtml::_(
		'bootstrap.renderModal',
		$idA,
		array(
			'url'    => $link,
			'title'  => JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE'),
			'width'  => '780px',
			'height' => '580px',
			'modalWidth' => '50',
			'bodyHeight' => '70',
			'footer' => '<button type="button" class="btn" data-dismiss="modal" aria-hidden="true">'
				. JText::_('COM_PHOCAEMAIL_CLOSE') . '</button>'
		)
	);

?><tr>
	<td class="ph-label"><label data-original-title="<?php echo JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE'); ?>" id="jform_request_id-lbl" for="jform_request_id" class="hasTooltip required" title=""><?php echo JText::_('COM_PHOCAEMAIL_SELECT_ARTICLE'); ?>:</label>
	</td>
	<td class="ph-inputbox"><?php echo implode("\n", $html); ?></td>
</tr>
<?php } ?>
	
	
<tr>	
	<td class="ph-label" valign="top"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_MESSAGE');  ?>:</label></td><td>
	<?php // parameters : areaname, content, width, height, cols, rows, show xtd buttons
	echo $this->t['editor']->display( 'message',  $this->r['message'], '648', '250', '80', '30', array('pagebreak', 'readmore', 'article') ) ;
	?></td>
</tr>

<tr>	
	<td class="ph-label" valign="top"><label for="title"><?php echo JText::_('COM_PHOCAEMAIL_FIELD_ATTACHMENT');  ?>:</label></td>
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
			echo JHtml::_('image', 'media/com_phocaemail/images/administrator/'.$attIco, '')
			. ' ' .$this->t['attachment'][$key]['file'] . '<br />';
		}
		echo '</td>';
	}
	?>
</tr>
<?php /*
	<td><input type="checkbox" name="attachment[0]" id="attachment[0]" <?php echo $attachmentInvoiceChecked ;?> ><img src="<?php echo $mosConfig_live_site ?>/images/M_images/pdf_button.png" border="0" /><?php echo $attachmentInvoice; ?><br />
				<?php
			} else { // only invoice or receipt - not both - RECEIPT
				?><input type="checkbox" name="attachment[1]" id="attachment[1]" <?php echo  $attachmentReceiptChecked ;?> ><img src="<?php echo $mosConfig_live_site ?>/images/M_images/pdf_button.png" border="0" /><?php echo $attachmentReceipt; ?><br />
				<?php
			} ?>
			<input type="checkbox" name="attachment[2]" id="attachment[2]" <?php echo $attachmentDeliveryNoteChecked ;?> ><img src="<?php echo $mosConfig_live_site ?>/images/M_images/pdf_button.png" border="0" /><?php echo $attachmentDeliveryNote; ?><br />
		</td>
	</tr> */ ?>
	
<tr>
	<td class="ph-label" valign="top" colspan="2">
		<div class="btn-wrapper" >
			<button onclick="Joomla.submitbutton('phocaemailwrite.send');return false;" class="btn btn-success">
			<span class="icon-envelope"></span> <?php echo JText::_('COM_PHOCAEMAIL_SEND')  ?></button>
		</div>
	</td>
</tr>
						
</table>

<?php 
if ($this->r['ext'] == 'virtuemart') {
	echo '<input type="hidden" name="order_id" value="'.$this->r['order_id'].'" />'. "\n";
	echo '<input type="hidden" name="delivery_id" value="'.$this->r['delivery_id'].'" />' . "\n";
	echo '<input type="hidden" name="type" value="'.$this->r['type'].'" />' . "\n";
	echo '<input type="hidden" name="ext" value="'.$this->r['ext'].'" />' . "\n";
}
/*
<input type="hidden" name="order_id" value="<?php echo $order_id ?>" />
<input type="hidden" name="delivery_id" value="<?php echo $delivery_id ?>" />
<input type="hidden" name="gen" value="<?php echo $gen ?>" />
<input type="hidden" name="vmtoken" value="<?php echo vmSpoofValue($sess->getSessionId()) ?>" />
<input type="hidden" name="receiptChecked" value="<?php echo $attachmentReceipt ?>" />
<input type="hidden" name="invoiceChecked" value="<?php echo $attachmentInvoice ?>" />
<input type="hidden" name="deliveryNoteChecked" value="<?php echo $attachmentDeliveryNote ?>" />*/ 
?>

<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>

<div id="ph-op-bg"></div>
<div id="ph-op-bg-msg"><?php echo JHTML::_('image', 'media/com_phocaemail/images/administrator/icon-sending.gif', '' ) . ' &nbsp; &nbsp; '. JText::_('COM_PHOCAEMAIL_SENDING_MESSAGE'); ?></div>