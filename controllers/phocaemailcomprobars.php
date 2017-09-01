<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.controlleradmin');
//~ echo 'controler/comprobars';
class PhocaEmailCpControllerPhocaEmailComprobars extends JControllerAdmin
{
	protected	$option 		= 'com_phocaemail';
	
	public function __construct($config = array())
	{
		parent::__construct($config);	
	}
	
	public function &getModel($name = 'PhocaEmailcomprobars', $prefix = 'PhocaEmailCpModel', $config = array())
	{
		// Carga del modelo.
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	public function saveOrderAjax() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);
		$model = $this->getModel();
		$return = $model->saveorder($pks, $order);
		if ($return) { echo "1";}
		JFactory::getApplication()->close();
	}
	public function ComprobarUsuarios(){
			// Simplemente enviamos opcion de actualizarUsuarios	
			$this->setRedirect('index.php?option=com_phocaemail&view=phocaemailcomprobars&opcion=actualizarUsuariosEmail');
			
	}
		
	public function AnhadirUsuariosJooomla(){
		// Simplemente enviamos opcion de actualizarUsuarios	
		$this->setRedirect('index.php?option=com_phocaemail&view=phocaemailcomprobars&opcion=anhadirUsuariosJoomla');
		//~ return $pp;
		}
	public function EliminaUsuariosLista(){
			// Simplemente enviamos opcion de actualizarUsuarios	
			$this->setRedirect('index.php?option=com_phocaemail&view=phocaemailcomprobars&opcion=eliminaUsuariosLista');
		
		}
	
}
?>
