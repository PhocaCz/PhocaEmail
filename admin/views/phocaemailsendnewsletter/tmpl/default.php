<?php defined('_JEXEC') or die('Restricted access');
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
?>
<?php //JHTML::_('behavior.tooltip');

$this->t['url'] = 'index.php?option=com_phocaemail&view=phocaemailsendnewslettera&format=json&tmpl=component&'. JSession::getFormToken().'=1';


JFactory::getDocument()->addScriptDeclaration(

"Joomla.submitbutton = function(task) {
	var form = document.adminForm;
	if (task == 'phocaemailsendnewsletter.send') {
		if (form.newsletter.value == ''){
			alert( '". JText::_('COM_PHOCAEMAIL_ERROR_FIELD_NEWSLETTER', true)."' );
		} else {

			var url 						= '". $this->t['url']."';
			var dataPost 					= {};
			var nId							= form.newsletter.value;
			dataPost['newsletterid']		= nId;

			var subscribers	= [];
			".'		' . $this->t['subscribersjs']."
			if (typeof subscribers[nId] !== 'undefined') {
				var slength = subscribers[nId].length;
			} else {
				var slength = 0;
			}

			var txtSending = '';
			var txtSendingFinished = '';
			if (slength == 0) {
				alert( '". JText::_('COM_PHOCAEMAIL_ERROR_THERE_ARE_NO_SUBSCRIBERS', true)."' );
			} else {
				jQuery(\"#phsendoutput\").empty();
				for (var i = 0; i < slength; i++) {

					var j = i + 1;
					txtSending = '<div class=\"ph-sending-msg\">". JText::_('COM_PHOCAEMAIL_SENDING_EMAIL_PLEASE_WAIT', true)." (' + j + '/' + slength + ') ...</div>';
					jQuery(\"#phsendoutput\").append(txtSending);

					dataPost['subscriberid']	= subscribers[nId][i];
					jQuery.ajax({
					   url: url,
					   type:'POST',
					   data:dataPost,
					   dataType:'JSON',
					   async: false,
					   success:function(data){
							if ( data.status == 1 ){
								jQuery(\"#phsendoutput\").append(data.message);
								//txtSending += data.message;
							} else {
								jQuery(\"#phsendoutput\").append(data.error);
								//txtSending += data.error;
							}
						}
					});
					//jQuery(\"#phsendoutput\").append(txtSending);
				}

				txtSendingFinished = '<div class=\"ph-sending-msg-finish\">". JText::_('COM_PHOCAEMAIL_SENDING_EMAIL_FINISHED', true)."</div>';
				jQuery(\"#phsendoutput\").append(txtSendingFinished);

			}

		}
	} else if (task == 'phocaemailsendnewsletter.cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}"


);

/*
?><script language="javascript" type="text/javascript">

Joomla.submitbutton = function(task) {
	var form = document.adminForm;
	if (task == 'phocaemailsendnewsletter.send') {
		if (form.newsletter.value == ""){
			alert( "<?php echo JText::_('COM_PHOCAEMAIL_ERROR_FIELD_NEWSLETTER', true) ?>" );
		} else {

			var url 						= '<?php echo $this->t['url']; ?>';
			var dataPost 					= {};
			var nId							= form.newsletter.value;
			dataPost['newsletterid']		= nId;

			var subscribers	= [];
			<?php echo '		' . $this->t['subscribersjs']; ?>
			if (typeof subscribers[nId] !== 'undefined') {
				var slength = subscribers[nId].length;
			} else {
				var slength = 0;
			}

			var txtSending = "";
			var txtSendingFinished = "";
			if (slength == 0) {
				alert( "<?php echo JText::_('COM_PHOCAEMAIL_ERROR_THERE_ARE_NO_SUBSCRIBERS', true) ?>" );
			} else {
				jQuery("#phsendoutput").empty();
				for (var i = 0; i < slength; i++) {

					var j = i + 1;
					txtSending = '<div class="ph-sending-msg"><?php echo JText::_('COM_PHOCAEMAIL_SENDING_EMAIL_PLEASE_WAIT', true) ?> (' + j + '/' + slength + ') ...</div>';
					jQuery("#phsendoutput").append(txtSending);

					dataPost['subscriberid']	= subscribers[nId][i];
					jQuery.ajax({
					   url: url,
					   type:'POST',
					   data:dataPost,
					   dataType:'JSON',
					   async: false,
					   success:function(data){
							if ( data.status == 1 ){
								jQuery("#phsendoutput").append(data.message);
								//txtSending += data.message;
							} else {
								jQuery("#phsendoutput").append(data.error);
								//txtSending += data.error;
							}
						}
					});
					//jQuery("#phsendoutput").append(txtSending);
				}

				txtSendingFinished = '<div class="ph-sending-msg-finish"><?php echo JText::_('COM_PHOCAEMAIL_SENDING_EMAIL_FINISHED', true) ?></div>';
				jQuery("#phsendoutput").append(txtSendingFinished);

			}

		}
	} else if (task == 'phocaemailsendnewsletter.cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
*/ ?>
<form action="index.php?option=com_phocaemail&view=phocaemailsendnewsletter" method="post" name="adminForm" id="adminForm">
<?php echo '<div class="span10 form-horizontal">';

echo '<div class="control-group">';
echo '<div class="control-label">'.JText::_('COM_PHOCAEMAIL_NEWSLETTER').'</div>';
echo '<div class="controls">' .$this->t['newsletterlist'] ;

echo '<button onclick="Joomla.submitbutton(\'phocaemailsendnewsletter.send\');return false;" class="btn btn-success">';
echo '<span class="icon-envelope"></span> '. JText::_('COM_PHOCAEMAIL_SEND')  .'</button>';

echo '</div>';
echo '</div>';

/*echo '<div class="control-group">';
echo '<div class="control-label"></div>';
echo '<div class="controls">';
echo '<div class="btn-wrapper" style="text-align:right;" >';
echo '<button onclick="Joomla.submitbutton(\'phocaemailwrite.send\');return false;" class="btn btn-success">';
echo '<span class="icon-envelope"></span> '. JText::_('COM_PHOCAEMAIL_SEND')  .'</button>';
echo '</div>';

echo '</div>';
echo '</div>';*/

echo '<div class="control-group">';
echo '<div class="control-label"></div>';
echo '<div class="controls">';
echo '<div class="ph-send-output" id="phsendoutput"></div>';
echo '</div>';
echo '</div>';


echo '</div>';//end span10
// Second Column
echo '<div class="span2">';
echo '<ul>';
echo '<li>'.JText::_('COM_PHOCAEMAIL_SEND_NEWSLETTER_INFO'). '</li>';
echo '<li>'.JText::_('COM_PHOCAEMAIL_SEND_NEWSLETTER_INFO_2'). '</li>';
echo '</ul>';
echo '</div>';//end span2 ?>

<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
