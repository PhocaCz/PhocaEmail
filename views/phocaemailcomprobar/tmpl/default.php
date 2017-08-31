<?php defined('_JEXEC') or die;


echo '<form action="index.php" method="post" name="adminForm" id="'.$this->t['c'].'info-form">';

echo '<div id="j-sidebar-container" class="span2">'.JHtmlSidebar::render().'</div>';
echo '<div id="j-main-container" class="span9">';
echo ' Estoy en default de phocaemailcomprobar';
echo '<pre>';
print_r(get_declared_classes());
echo '</pre>';

echo '</div>';
echo '<div class="span1"></div>';

echo '</div>';

echo '</form>';
?>
