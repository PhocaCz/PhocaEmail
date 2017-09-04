<?php
/*
 * @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @component Phoca Gallery
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
$resumen = $this->resumen;
$SinComprobar = (isset($resumen['SinComprobarUsuarios']) ?$resumen['SinComprobarUsuarios'] : 0);
$opcionRealizada =  $resumen['Realizado'];
$Respuesta = $resumen['Respuesta'];
$class		= $this->t['n'] . 'RenderAdminViews';
$r 			=  new $class();
$user		= JFactory::getUser();
$userId		= $user->get('id');

$canOrder	= $user->authorise('core.edit.state', $this->t['o']);

echo $r->startForm($this->t['o'], $this->t['tasks'], 'adminForm');
echo '<div id="j-sidebar-container" class="span2">'.JHtmlSidebar::render().'</div>';

echo $r->startMainContainer();
echo '<div>';
echo '<h2>Resumen de datos</h2>';
echo '<p>Mas info en ayuda...</p>';

echo '<div class="row-fluid">';
echo '<div class="span4">';
echo '<h3>Emails sin comprobar</h3>';
echo '<p>Hay '.$SinComprobar.' sin comprobar si es usuario de joomla o no existe, no esta registrado como usuario (comprador).</p>';
	echo $this->loadTemplate('emails');
echo '<ul>';
foreach ($resumen['EmailEnvioNoUsuarios'] as $email){
	echo '<li>'.$email.'</li>';
}
echo '</ul>';
echo '</div>';
echo '<div class="span4">';
echo '<h3>Usuarios de Joomla</h3>';
echo '<p>Numero usuarios que hay Joomla:<span>'.$resumen['UsuarioJoomla'].'</span></p>';
echo '<h3>Subcriptores en Newletter</h3>';
echo '<p>Total Subscriptores:<span>'.$resumen['totalSubscriptos'].'</span></p>';
echo '</div>';
echo '<div class="span4"><h3>En lista Iniciacion</h3>';
echo '<p>La lista iniciación es la que utilizamos para enviar el primer boletín informativo, donde le indicamos donde se puede dar de baja.</p>';
echo  'Numero Subscriptores lista(1):<span>'.$resumen['SuscriptoresLista'].'</span></p>';
echo '</div>';
echo '</div>';
echo '<pre>';
//~ print_r($resumen);


//~ print_r($this);
echo '</pre>';
echo '</div>';
echo $r->formInputs($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();

?>
