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

echo "<br /><br />";
echo $r->selectFilterActived('COM_PHOCAEMAIL_SELECT_ACTIVE_STATUS', $this->state->get('filter.active'));
//echo $r->selectFilterLanguage('JOPTION_SELECT_LANGUAGE', $this->state->get('filter.language'));
require_once JPATH_COMPONENT.'/helpers/phocaemaillists.php';
echo $r->selectFilterMailingList(PhocaEmailListsHelper::options(), 'COM_PHOCAEMAIL_SELECT_MAILING_LIST', $this->state->get('filter.mailing_list'));
echo $r->endFilterBar();

echo $r->endFilterBar();		

echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->thOrdering('JGRID_HEADING_ORDERING', $listDirn, $listOrder);
echo $r->thCheck('JGLOBAL_CHECK_ALL');
echo '<th class="ph-title">'.JHTML::_('grid.sort',  	$this->t['l'].'_NAME', 'a.name', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-title">'.JHTML::_('grid.sort',  	$this->t['l'].'_EMAIL', 'a.email', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.JHTML::_('grid.sort',  	$this->t['l'].'_SIGN_UP_DATE', 'a.date_register', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.JHTML::_('grid.sort',  	$this->t['l'].'_ACTIVATION_DATE', 'a.date_active', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.JHTML::_('grid.sort',  	$this->t['l'].'_UNSUBSCRIPTION_DATE', 'a.date_unsubscribe', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-active">'.JHTML::_('grid.sort',  	$this->t['l'].'_ACTIVE_USER', 'a.active', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-registered">'.JText::_($this->t['l'].'_REGISTERED_USER' ).'</th>'."\n";
echo '<th class="ph-title">'.JHTML::_('grid.sort',  	$this->t['l'].'_MAILING_LIST', 'ml.title', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-hits">'.JHTML::_('grid.sort',  $this->t['l'].'_ATTEMPTS', 'a.hits', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-privacy">'.JHTML::_('grid.sort',  $this->t['l'].'_PRIVACY_CONFIRMATION', 'a.privacy', $listDirn, $listOrder ).'</th>'."\n";
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
echo $r->tdOrder($canChange, $saveOrder, $orderkey, $item->ordering);
echo $r->td(JHtml::_('grid.id', $i, $item->id), "small ");
					
$checkO = '';
if ($item->checked_out) {
	$checkO .= JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->t['tasks'].'.', $canCheckin);
}

// No name
if ($item->name == '') {
	$item->name = JText::_('COM_PHOCAEMAIL_NAME_NOT_SET');
}

if ($canCreate || $canEdit) {
	$checkO .= '<a href="'. JRoute::_($linkEdit).'">'. $this->escape($item->name).'</a>';
} else {
	$checkO .= $this->escape($item->name);
}
//$checkO .= ' <span class="smallsub">(<span>'.JText::_($this->t['l'].'_FIELD_ALIAS_LABEL').':</span>'. $this->escape($item->alias).')</span>';
echo $r->td($checkO, "small ");

echo $r->td( $this->escape($item->email), "small ");

echo $r->td( $this->escape($item->date_register), "small ");
echo $r->td( $this->escape($item->date_active), "small ");

echo $r->td( $this->escape($item->date_unsubscribe), "small ");


if ($item->active == 1) {
	echo $r->td( '<span class="label label-success">'.JText::_('COM_PHOCAEMAIL_YES').'</span>', "small ");
} else if ($item->active == 2) { 
	echo $r->td( '<span class="label label-warning">'.JText::_('COM_PHOCAEMAIL_UNSUBSCRIBED').'</span>', "small ");
} else {
	echo $r->td( '<span class="label label-important label-danger">'.JText::_('COM_PHOCAEMAIL_NOT_ACTIVED').'</span>', "small ");
}

$userO = '';
if (isset($item->userid) && $item->userid > 0) {
	if (isset($item->usernameno)) {
		$userO .= ' <span>'.$item->usernameno.'</span>';
	}

	if (isset($item->username)) {
		$userO .= ' <small>('.$item->username.')</small>';
	}
}
echo $r->td($userO, "small ");


$mailingListTitle = isset($item->mailing_list_title) && $item->mailing_list_title != '' ? $item->mailing_list_title : '';
echo $r->td( $this->escape($mailingListTitle), "small ");

echo $r->td( $this->escape($item->hits), "small ");


$privacy = $item->privacy == 1 ? JText::_('COM_PHOCAEMAIL_YES') : JText::_('COM_PHOCAEMAIL_NO');
echo $r->td( $this->escape($privacy), "small ");

echo $r->td(JHtml::_('jgrid.published', $item->published, $i, $this->t['tasks'].'.', $canChange), "small ");

echo $r->td($item->id, "small ");

echo '</tr>'. "\n";
						
		//}
	}
}
echo '</tbody>'. "\n";

echo $r->tblFoot($this->pagination->getListFooter(), 14);
echo $r->endTable();

echo $r->formInputs($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();
?>