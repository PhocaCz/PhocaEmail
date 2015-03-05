<?php
/*
defined('_JEXEC') or die('Restricted access');
*/
/*********** XML PARAMETERS AND VALUES ************/
$xml_item = "component";// component | template
$xml_file = "phocaemail.xml";		
$xml_name = "com_phocaemail";
$xml_creation_date = "16/08/2014";
$xml_author = "Jan Pavelka (www.phoca.cz)";
$xml_author_email = "";
$xml_author_url = "www.phoca.cz";
$xml_copyright = "Jan Pavelka";
$xml_license = "GNU/GPL";
$xml_version = "3.0.3";
$xml_description = "Phoca Email";
$xml_copy_file = 1;//Copy other files in to administration area (only for development), ./front, ./language, ./other
$xml_script_file = 'install/script.php';
/*
$xml_menu = array (0 => "COM_PHOCAEMAIL", 1 => "option=com_phocaemail", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu.png");
$xml_submenu[0] = array (0 => "COM_PHOCAEMAIL_CONTROLPANEL", 1 => "option=com_phocaemail", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-cp.png");
$xml_submenu[1] = array (0 => "COM_PHOCAEMAIL_WRITE", 1 => "option=com_phocaemail&view=phocaemailwrite", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-write.png");
$xml_submenu[2] = array (0 => "COM_PHOCAEMAIL_INFO", 1 => "option=com_phocaemail&view=phocaemailinfo", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-info.png");*/

$xml_menu = array (0 => "COM_PHOCAEMAIL", 1 => "option=com_phocaemail", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu.png", 3 => 'COM_PHOCAEMAIL', 4 => 'phocaemailcp');
$xml_submenu[0] = array (0 => "COM_PHOCAEMAIL_CONTROL_PANEL", 1 => "option=com_phocaemail", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-cp.png", 3 => 'COM_PHOCAEMAIL_CONTROL_PANEL', 4 => 'phocaemailcp');
$xml_submenu[1] = array (0 => "COM_PHOCAEMAIL_WRITE", 1 => "option=com_phocaemail&view=phocaemailwrite", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-write.png", 3 => 'COM_PHOCAEMAIL_WRITE', 4 => 'phocaemailwrite');
$xml_submenu[2] = array (0 => "COM_PHOCAEMAIL_NEWSLETTERS", 1 => "option=com_phocaemail&view=phocaemailnewsletters", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-newsletter.png", 3 => 'COM_PHOCAEMAIL_NEWSLETTERS', 4 => 'phocaemailnewsletters');
$xml_submenu[3] = array (0 => "COM_PHOCAEMAIL_SUBSCRIBERS", 1 => "option=com_phocaemail&view=phocaemailsubscribers", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-subscriber.png", 3 => 'COM_PHOCAEMAIL_SUBSCRIBERS', 4 => 'phocaemailsubscribers');
$xml_submenu[4] = array (0 => "COM_PHOCAEMAIL_MAILING_LISTS", 1 => "option=com_phocaemail&view=phocaemaillists", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-lists.png", 3 => 'COM_PHOCAEMAIL_MAILING_LISTS', 4 => 'phocaemaillists');
$xml_submenu[5] = array (0 => "COM_PHOCAEMAIL_INFO", 1 => "option=com_phocaemail&view=phocaemailinfo", 2 => "components/com_phocaemail/assets/images/icon-16-pe-menu-info.png", 3 => 'COM_PHOCAEMAIL_INFO', 4 => 'phocaemailinfo');

$xml_install_file = ''; 
$xml_uninstall_file = '';
/*********** XML PARAMETERS AND VALUES ************/
?>