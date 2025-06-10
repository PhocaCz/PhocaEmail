<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

\defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

$this->t['url'] = 'index.php?option=com_phocaemail&view=sendnewslettera&format=json&tmpl=component&'. Session::getFormToken().'=1';

$params		= ComponentHelper::getParams('com_phocaemail') ;
$emailRate	= $params->get('email_rate', 200);
$emailDelay = (3600 / $emailRate) * 1000;

Factory::getDocument()->addScriptDeclaration(

"Joomla.submitbutton = function(task) {
	var form = document.adminForm;
	if (task == 'sendnewsletter.send') {
		if (form.newsletter.value == ''){
			alert( '". Text::_('COM_PHOCAEMAIL_ERROR_FIELD_NEWSLETTER', true)."' );
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
			var txtSendingFinished = '';"
			//emario insert async function
			."async function delay(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }"
            //END of emario insert async function
			."if (slength == 0) {
				alert( '". Text::_('COM_PHOCAEMAIL_ERROR_THERE_ARE_NO_SUBSCRIBERS', true)."' );
			} else {
				jQuery(\"#phsendoutput\").empty();"
				//emario insert mainLoop function
                ."async function mainLoop(slength) {"
                //END of emario insert mainLoop function
					."for (var i = 0; i < slength; i++) {
                        //emario insert await delay
    					await delay($emailDelay);
    					//END of emario insert await delay
						var j = i + 1;
						txtSending = '<div class=\"ph-sending-msg\">". Text::_('COM_PHOCAEMAIL_SENDING_EMAIL_PLEASE_WAIT', true)." (' + j + '/' + slength + ') ...</div>';
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
					}"
                //emario insert call mainLoop
                ."const loopLimit = slength;
                mainLoop(loopLimit);"
                //END of emario insert call mainLoop

				."txtSendingFinished = '<div class=\"ph-sending-msg-finish\">". Text::_('COM_PHOCAEMAIL_SENDING_EMAIL_FINISHED', true)."</div>';
				jQuery(\"#phsendoutput\").append(txtSendingFinished);

			}

		}
	} else if (task == 'sendnewsletter.cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}");

/*
?><script language="javascript" type="text/javascript">

Joomla.submitbutton = function(task) {
	var form = document.adminForm;
	if (task == 'phocaemailsendnewsletter.send') {
		if (form.newsletter.value == ""){
			alert( "<?php echo Text::_('COM_PHOCAEMAIL_ERROR_FIELD_NEWSLETTER', true) ?>" );
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
				alert( "<?php echo Text::_('COM_PHOCAEMAIL_ERROR_THERE_ARE_NO_SUBSCRIBERS', true) ?>" );
			} else {
				jQuery("#phsendoutput").empty();
				for (var i = 0; i < slength; i++) {

					var j = i + 1;
					txtSending = '<div class="ph-sending-msg"><?php echo Text::_('COM_PHOCAEMAIL_SENDING_EMAIL_PLEASE_WAIT', true) ?> (' + j + '/' + slength + ') ...</div>';
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

				txtSendingFinished = '<div class="ph-sending-msg-finish"><?php echo Text::_('COM_PHOCAEMAIL_SENDING_EMAIL_FINISHED', true) ?></div>';
				jQuery("#phsendoutput").append(txtSendingFinished);

			}

		}
	} else if (task == 'phocaemailsendnewsletter.cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
</script>
*/ ?>

<form action="index.php?option=com_phocaemail&view=sendnewsletter" method="post" name="adminForm" id="adminForm">
	<div class="span10 form-horizontal">

		<div class="control-group">
			<div class="control-label"><?php echo Text::_('COM_PHOCAEMAIL_NEWSLETTER'); ?></div>
				<div class="controls"><?php echo $this->t['newsletterlist']; ?>
					<button onclick="Joomla.submitbutton(\'sendnewsletter.send\');return false;" class="btn btn-success">
					<span class="icon-envelope"></span><?php echo Text::_('COM_PHOCAEMAIL_SEND'); ?></button>

				</div>
			</div>

			<!-- div class="control-group">';
				<div class="control-label"></div>
				<div class="controls">
					<div class="btn-wrapper" style="text-align:right;" >
						<button onclick="Joomla.submitbutton(\'phocaemailwrite.send\');return false;" class="btn btn-success">
						<span class="icon-envelope"></span><?php // echo Text::_('COM_PHOCAEMAIL_SEND'); ?></button>
					</div>
				</div>
			</div -->

		<div class="control-group">
			<div class="control-label"></div>
			<div class="controls">
				<div class="ph-send-output" id="phsendoutput"></div>
			</div>
		</div>

	</div>

	<!-- Second Column -->
	<div class="span2">
		<ul>
			<li><?php echo Text::_('COM_PHOCAEMAIL_SEND_NEWSLETTER_INFO'); ?></li>
			<li><?php echo Text::_('COM_PHOCAEMAIL_SEND_NEWSLETTER_INFO_2'); ?></li>
		</ul>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>
