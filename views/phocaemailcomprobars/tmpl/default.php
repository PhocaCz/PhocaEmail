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
$class		= $this->t['n'] . 'RenderAdminViews';
$r 			=  new $class();
$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $this->t['o']);
$saveOrder	= $listOrder == 'a.ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option='.$this->t['o'].'&task='.$this->t['tasks'].'.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
$sortFields = $this->getSortFields();

echo '<pre>';
print_r($resumen);
echo '</pre>';
echo $r->jsJorderTable($listOrder);



echo $r->startForm($this->t['o'], $this->t['tasks'], 'adminForm');
echo $r->startFilter();
echo $r->endFilter();

echo $r->startMainContainer();
echo $r->startFilterBar();
echo $r->inputFilterSearch($this->t['l'].'_FILTER_SEARCH_LABEL', $this->t['l'].'_FILTER_SEARCH_DESC',
							$this->escape($this->state->get('filter.search')));
echo $r->inputFilterSearchClear('JSEARCH_FILTER_SUBMIT', 'JSEARCH_FILTER_CLEAR');
echo $r->inputFilterSearchLimit('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC', $this->pagination->getLimitBox());
echo $r->selectFilterDirection('JFIELD_ORDERING_DESC', 'JGLOBAL_ORDER_ASCENDING', 'JGLOBAL_ORDER_DESCENDING', $listDirn);
echo $r->selectFilterSortBy('JGLOBAL_SORT_BY', $sortFields, $listOrder);

echo $r->startFilterBar(2);
echo $r->selectFilterPublished('JOPTION_SELECT_PUBLISHED', $this->state->get('filter.state'));
//echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
//echo $r->selectFilterCategory(PhocaDownloadCategory::options($this->t['o']), 'JOPTION_SELECT_CATEGORY', $this->state->get('filter.category_id'));
echo $r->endFilterBar();

echo $r->endFilterBar();		

echo $r->startTable('categoryList');
echo $r->startTblHeader();

echo $r->thOrdering('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
echo $r->thCheck('JGLOBAL_CHECK_ALL');
echo '<th class="ph">'.JHTML::_('grid.sort',  	$this->t['l'].'_NAME', 'a.name', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph">'.JHTML::_('grid.sort',  	$this->t['l'].'_NAMEUSUARIO', 'a.username', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph">'.JHTML::_('grid.sort',  	$this->t['l'].'_NAMEVIRTUEMART', 'a.namevirtuemart', $listDirn, $listOrder ).'</th>'."\n";

echo '<th class="ph">'.JHTML::_('grid.sort',  	$this->t['l'].'_SEND_EMAIL', 'a.email', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph">'.JHTML::_('grid.sort',  	$this->t['l'].'_EMAILUSUARIO', 'a.emailusuario', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.JHTML::_('grid.sort',  	$this->t['l'].'_SIGN_UP_DATE', 'a.date', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.JHTML::_('grid.sort',  	$this->t['l'].'_UNSUBSCRIBE_DATE', 'a.date_unsubscribe', $listDirn, $listOrder ).'</th>'."\n";

echo '<th class="ph-active">'.JHTML::_('grid.sort',  	$this->t['l'].'_ACTIVE_USER', 'a.active', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-registered">'.JText::_($this->t['l'].'_ID_USUARIO' ).'</th>'."\n";

echo '<th class="ph-registered">'.JText::_($this->t['l'].'_ID_JOOMLA_Y_VIRTUEMART' ).'</th>'."\n";
echo '<th class="ph-hits">'.JHTML::_('grid.sort',  $this->t['l'].'_ATTEMPTS', 'a.hits', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.JHTML::_('grid.sort',  $this->t['l'].'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";	
echo '<th class="ph-id">'.JHTML::_('grid.sort',  		$this->t['l'].'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();
			
echo '<tbody>'. "\n";

$originalOrders = array();	
$parentsStr 	= "";		
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
		//if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
			$j++;

$urlEdit		= 'index.php?option='.$this->t['o'].'&task='.$this->t['task'].'.edit&id=';
$urlTask		= 'index.php?option='.$this->t['o'].'&task='.$this->t['task'];
$orderkey   	= array_search($item->id, $this->ordering[0]);		
$ordering		= ($listOrder == 'a.ordering');			
$canCreate		= $user->authorise('core.create', $this->t['o']);
$canEdit		= $user->authorise('core.edit', $this->t['o']);
$canCheckin		= $user->authorise('core.manage', 'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
$canChange		= $user->authorise('core.edit.state', $this->t['o']) && $canCheckin;
$linkEdit 		= JRoute::_( $urlEdit. $item->id );



$iD = $i % 2;
echo "\n\n";
//echo '<tr class="row'.$iD.'" sortable-group-id="0" item-id="'.$item->id.'" parents="0" level="0">'. "\n";
echo '<tr class="row'.$iD.'" sortable-group-id="0" >'. "\n";
echo $r->tdOrder($canChange, $saveOrder, $orderkey);
echo $r->td(JHtml::_('grid.id', $i, $item->id), "small ");
					
$checkO = '';
$NombreNewsletter = wordwrap($this->escape($item->name), 12, "<br/>", true);

if ($item->checked_out) {
	$checkO .= JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->t['tasks'].'.', $canCheckin);
}
if ($canCreate || $canEdit) {
	$checkO .= '<a href="'. JRoute::_($linkEdit).'">'. 
				//$this->escape($item->name)
				$NombreNewsletter.'</a>';
} else {
	//~ $checkO .= $this->escape($item->name);
	$checkO .= $NombreNewsletter;
}

//$checkO .= ' <span class="smallsub">(<span>'.JText::_($this->t['l'].'_FIELD_ALIAS_LABEL').':</span>'. $this->escape($item->alias).')</span>';
echo $r->td($checkO, "small ");

// Ahora montamos el nombre de usuario y nombre de virtuemart
$nombreUsuario = '';
if(isset($item->username)){
	//~ $nombreUsuario= $item->username;
	$nombreUsuario= wordwrap($item->username, 18, "<br/>", true);
}

echo $r->td($nombreUsuario, "small ");
$nombreVirtuemart = '';
if(isset($item->namevirtuemart)){
	//~ $nombreVirtuemart=$item->namevirtuemart;
	$nombreVirtuemart= wordwrap($item->namevirtuemart, 12, "<br/>", true);
}
echo $r->td($nombreVirtuemart, "small ");

//~ echo $r->td( $this->escape($item->email), "small ");
$emailEnvio = wordwrap($this->escape($item->email), 18, "<br/>", true);
echo $r->td($emailEnvio, "small");

$emailUsuario = '';
if (isset($item->emailusuario)){
	$emailUsuario= wordwrap($this->escape($item->emailusuario),18,"<br/>",true);
}

echo $r->td($emailUsuario, "small");

echo $r->td( $this->escape($item->date), "small ");

echo $r->td( $this->escape($item->date_unsubscribe), "small ");


if ($item->active == 1) {
	echo $r->td( '<span class="label label-success">'.JText::_('COM_PHOCAEMAIL_YES').'</span>', "small ");
} else if ($item->active == 2) { 
	echo $r->td( '<span class="label label-warning">'.JText::_('COM_PHOCAEMAIL_UNSUBSCRIBED').'</span>', "small ");
} else {
	echo $r->td( '<span class="label label-important label-danger">'.JText::_('COM_PHOCAEMAIL_NOT_ACTIVED').'</span>', "small ");
}

$idUsuarioPhoca = '<span class="icon-unpublish"></span>';
$userO = '';
if (isset($item->userid) && $item->userid > 0) {
	if (isset($item->usernameno)) {
		// El nombre Joomla .. 
		//~ $userO .= ' <span>'.$item->usernameno.'</span>';
	}

	if (isset($item->username)) {
		//~ $userO .= ' <span>('.$item->username.')</span>';
		// Numero id de usuario.
		$userO .= ' <span>('.$item->userid.')</span>';
		$userO .= ' <span>('.$item->idVirtuemart.')</span>';
		$idUsuarioPhoca = '<span class="icon-publish"></span>';
		}
		
}  else {
		// Ahora debería comprobar si existe en usuarios buscando por emailEnvio
		// Para ello tengo que hacer una consulta en que haga la busqueda de aquellos que falte y lo identifique en items.
		$idUsuarioPhoca = '<span class="icon-ban-circle"></span>';
	}
echo $r->td($idUsuarioPhoca,"small");

echo $r->td($userO, "small ");

echo $r->td( $this->escape($item->hits), "small ");

echo $r->td(JHtml::_('jgrid.published', $item->published, $i, $this->t['tasks'].'.', $canChange), "small ");

echo $r->td($item->id, "small ");

echo '</tr>'. "\n";
						
		//}
	}
}
echo '</tbody>'. "\n";

echo $r->tblFoot($this->pagination->getListFooter(), 11);
echo $r->endTable();

echo $r->formInputs($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();
?>
