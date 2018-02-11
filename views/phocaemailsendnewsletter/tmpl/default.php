<?php defined('_JEXEC') or die('Restricted access'); 
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
?>
<?php JHTML::_('behavior.tooltip');

$this->t['url'] = 'index.php?option=com_phocaemail&view=phocaemailsendnewslettera&format=json&tmpl=component&'. JSession::getFormToken().'=1';

?><script language="javascript" type="text/javascript">
var contador = 0; // variable global para hacer bucle.
var dataPost = {};
var url;
var nId;
var subscribers	= [];
var subcriptor;
var slength = 0;
Joomla.submitbutton = function(task) {
	var form = document.adminForm;
	if (task == 'phocaemailsendnewsletter.send') {
		if (form.newsletter.value == ""){
			alert( "<?php echo JText::_('COM_PHOCAEMAIL_ERROR_FIELD_NEWSLETTER', true) ?>" );
		} else {
			
			url 						= '<?php echo $this->t['url']; ?>';	
			//~ var dataPost 					= {};
			nId							= form.newsletter.value;
			dataPost['newsletterid']		= nId;
			<?php echo '		' . $this->t['subscribersjs']; ?>
			if (typeof subscribers[nId] !== 'undefined') {
				slength = subscribers[nId].length;
			} 
			
			var txtSending = "";
			var txtSendingFinished = "";
			if (slength == 0) {
				alert( "<?php echo JText::_('COM_PHOCAEMAIL_ERROR_THERE_ARE_NO_SUBSCRIBERS', true) ?>" );
			} else {
				jQuery("#phsendoutput").empty();
				controladorEnvio(subscribers,slength);
				
				
				
			}
		
		}
	} else if (task == 'phocaemailsendnewsletter.cancel') {
		Joomla.submitform(task, document.getElementById('adminForm'));
	}
}
function envioEmail(){
	// Ejecutamos envio si contador es mejor al numero items.. 
		var i = contador;
		// Retraso la ejecucion durante 20 s
		var j = i + 1;
		txtSending = '<div class="ph-sending-msg"><?php echo JText::_('COM_PHOCAEMAIL_SENDING_EMAIL_PLEASE_WAIT', true) ?> (' + j + '/' + slength + ') ...</div>';
		jQuery("#phsendoutput").append(txtSending);
			
		dataPost['subscriberid']	= subcriptor;
			
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
			contador ++;
			}
		});
		//jQuery("#phsendoutput").append(txtSending);
		// Tiempo que esperamos para enviar un email, debería ser un parametro del componente, 
		setTimeout(controladorEnvio,10000);
	
	
}
function controladorEnvio(){
	if (contador < slength){
		subcriptor = subscribers[nId][contador];
		console.log(contador);
		console.log(typeof(subcriptor));
		envioEmail();
	} else {
		// Entonces termino
		txtSendingFinished = '<div class="ph-sending-msg-finish"><?php echo JText::_('COM_PHOCAEMAIL_SENDING_EMAIL_FINISHED', true) ?></div>';
		jQuery("#phsendoutput").append(txtSendingFinished);
	}
}
</script>

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
echo JText::_('COM_PHOCAEMAIL_SEND_NEWSLETTER_INFO');
echo '</div>';//end span2 ?>

<input type="hidden" name="task" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>


