<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

class JFormFieldPhocaEmailMailingList extends JFormField
{
	protected $type 		= 'PhocaEmailMailingList';

	protected function getInput() {


		// Initialize variables.
		$html = array();

		// Initialize some field attributes.
		$attr 		= '';
		$attr		.= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr		.= $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
		$attr		.= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr		.= ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$attr		.= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr 		.= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'" ' : ' ';
		$multiple	= ((string) $this->element['multiple'] == 'true') ? TRUE : FALSE;
		$manager	= $this->element['manager'] ? $this->element['manager'] : '';


		$order 	= 'ordering ASC';
		$db 	= JFactory::getDBO();
		$query 	= 'SELECT a.id AS value, a.title AS text'
				.' FROM #__phocaemail_lists AS a'
				. ' ORDER BY '. $order;
		$db->setQuery($query);
		$lists = $db->loadObjectList();


		if ($manager == 'filter') {

		    // 1) FILTER FUNCTION - e.g. in subscribers list (on list is filtered)
            $name = $this->name;
            $value= $this->value;
            array_unshift($lists, JHtml::_('select.option', '', '- ' . JText::_('COM_PHOCAEMAIL_SELECT_MAILING_LIST') . ' -', 'value', 'text'));
        } else {

            // 2) SELECT FUNCTION - e.g. in subscriber edit (more lists can be selected)
            $activeArray = array();

            $id = (int)$this->form->getValue('id');

            if ((int)$id > 0) {

                switch ($manager) {
                    case 'subscriber':
                        $table = '#__phocaemail_subscriber_lists';
                        $item = 'a.id_subscriber';
                    break;
                    case 'newsletter':
                    default:
                        $table = '#__phocaemail_newsletter_lists';
                        $item = 'a.id_newsletter';
                    break;
                }

                $query = ' SELECT a.id_list FROM ' . $table . ' AS a'
                    . ' WHERE ' . $item . ' = ' . (int)$id;
                $db->setQuery($query);
                $activeArray = $db->loadColumn();
            }

            $value = $activeArray;
            if ($multiple) {
                $name = $this->name;
                $attr .= ' multiple="multiple"';
            } else {
                $name = $this->name;

            }


        }

		$html = JHTML::_('select.genericlist', $lists, $name, $attr, 'value', 'text', $value, 'id');

		return $html;

	}
}
?>
