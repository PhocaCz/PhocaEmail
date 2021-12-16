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
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$r 			=  $this->r;
$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', $this->t['o']);
$saveOrder	= $listOrder == 'a.ordering';
$saveOrderingUrl = '';
if ($saveOrder && !empty($this->items)) {
	$saveOrderingUrl = $r->saveOrder($this->t, $listDirn);
}
$sortFields = $this->getSortFields();

echo $r->jsJorderTable($listOrder);



echo $r->startForm($this->t['o'], $this->t['tasks'], 'adminForm');


echo $r->startMainContainer();


echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));



echo $r->startTable('categoryList');

echo $r->startTblHeader();

echo $r->firstColumnHeader($listDirn, $listOrder);
echo $r->secondColumnHeader($listDirn, $listOrder);
echo '<th class="ph-title">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_NAME', 'a.name', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-title">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_EMAIL', 'a.email', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_SIGN_UP_DATE', 'a.date_register', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_ACTIVATION_DATE', 'a.date_active', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-date">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_UNSUBSCRIPTION_DATE', 'a.date_unsubscribe', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-type">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_SIGN_UP_TYPE', 'a.type', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-active">'.HTMLHelper::_('searchtools.sort',  	$this->t['l'].'_ACTIVE_USER', 'a.active', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-registered">'.Text::_($this->t['l'].'_REGISTERED_USER' ).'</th>'."\n";
echo '<th class="ph-title">'.Text::_($this->t['l'].'_MAILING_LIST').'</th>'."\n";
echo '<th class="ph-hits">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_ATTEMPTS', 'a.hits', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-privacy">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_PRIVACY_CONFIRMATION', 'a.privacy', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-published">'.HTMLHelper::_('searchtools.sort',  $this->t['l'].'_PUBLISHED', 'a.published', $listDirn, $listOrder ).'</th>'."\n";
echo '<th class="ph-id">'.HTMLHelper::_('searchtools.sort',  		$this->t['l'].'_ID', 'a.id', $listDirn, $listOrder ).'</th>'."\n";

echo $r->endTblHeader();

echo $r->startTblBody($saveOrder, $saveOrderingUrl, $listDirn);

$originalOrders = array();
$parentsStr 	= "";
$j 				= 0;

if (is_array($this->items)) {
	foreach ($this->items as $i => $item) {
		//if ($i >= (int)$this->pagination->limitstart && $j < (int)$this->pagination->limit) {
		$j++;

		$urlEdit = 'index.php?option=' . $this->t['o'] . '&task=' . $this->t['task'] . '.edit&id=';
		$urlTask = 'index.php?option=' . $this->t['o'] . '&task=' . $this->t['task'];
		$orderkey = array_search($item->id, $this->ordering[0]);
		$ordering = ($listOrder == 'a.ordering');
		$canCreate = $user->authorise('core.create', $this->t['o']);
		$canEdit = $user->authorise('core.edit', $this->t['o']);
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
		$canChange = $user->authorise('core.edit.state', $this->t['o']) && $canCheckin;
		$linkEdit = Route::_($urlEdit . $item->id);


		echo $r->startTr($i, isset($item->catid) ? (int)$item->catid : 0);

		echo $r->firstColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);
		echo $r->secondColumn($i, $item->id, $canChange, $saveOrder, $orderkey, $item->ordering);


		$checkO = '';
		if ($item->checked_out) {
			$checkO .= HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->t['tasks'] . '.', $canCheckin);
		}

		// No name
		if ($item->name == '') {
			$item->name = Text::_('COM_PHOCAEMAIL_NAME_NOT_SET');
		}

		if ($canCreate || $canEdit) {
			$checkO .= '<a href="' . Route::_($linkEdit) . '">' . $this->escape($item->name) . '</a>';
		} else {
			$checkO .= $this->escape($item->name);
		}
		//$checkO .= ' <span class="smallsub">(<span>'.JText::_($this->t['l'].'_FIELD_ALIAS_LABEL').':</span>'. $this->escape($item->alias).')</span>';
		echo $r->td($checkO, "small ");

		echo $r->td($this->escape($item->email), "small ");

		echo $r->td($this->escape($item->date_register), "small ");
		echo $r->td($this->escape($item->date_active), "small ");

		echo $r->td($this->escape($item->date_unsubscribe), "small ");


		$type = '<span class="label label-success badge bg-success badge bg-success" style="background-color: #3366CC">PhocaEmail</span>';
		if ($item->type == 2) {
			$type = '<span class="label label-success badge bg-success" style="background-color: #129ED9">PhocaCart</span>';
		}
		echo $r->td($type, "small ");


		if ($item->active == 1) {
			echo $r->td('<span class="label label-success badge bg-success">' . Text::_('COM_PHOCAEMAIL_ACTIVE') . '</span>', "small ");
		} else if ($item->active == 2) {
			echo $r->td('<span class="label label-warning badge bg-warning">' . Text::_('COM_PHOCAEMAIL_UNSUBSCRIBED') . '</span>', "small ");
		} else {
			echo $r->td('<span class="label label-important label-danger badge bg-danger">' . Text::_('COM_PHOCAEMAIL_NOT_ACTIVED') . '</span>', "small ");
		}

		$userO = '';
		if (isset($item->userid) && $item->userid > 0) {
			if (isset($item->usernameno)) {
				$userO .= ' <span>' . $item->usernameno . '</span>';
			}

			if (isset($item->username)) {
				$userO .= ' <small>(' . $item->username . ')</small>';
			}
		}
		echo $r->td($userO, "small ");


		$mailingListTitle = '';
		if (isset($item->mailing_list_title)) {
			$mailingListTitleA = explode(',', $item->mailing_list_title);
			if (!empty($mailingListTitleA)) {
				foreach ($mailingListTitleA as $k => $v) {
					$mailingListTitle .= '<span class="label label-info badge bg-info">' . $this->escape($v) . '</span> ';
				}
			}
		}

		echo $r->td($mailingListTitle, "small ");

		echo $r->td($this->escape($item->hits), "small ");


		$privacy = $item->privacy == 1 ? Text::_('COM_PHOCAEMAIL_YES') : Text::_('COM_PHOCAEMAIL_NO');
		echo $r->td($this->escape($privacy), "small ");

		echo $r->td(HTMLHelper::_('jgrid.published', $item->published, $i, $this->t['tasks'] . '.', $canChange), "small ");

		echo $r->td($item->id, "small ");

		echo $r->endTr();

		//}
	}
}
echo $r->endTblBody();

echo $r->tblFoot($this->pagination->getListFooter(), 15);
echo $r->endTable();

echo $r->formInputsXML($listOrder, $listDirn, $originalOrders);
echo $r->endMainContainer();
echo $r->endForm();
?>
