<?php
//Variable pass-thru
$this->assign('title', $title);
$this->assign('administration', $administration);
echo '<div class="row" id="administration">';
echo $this->element('admin_tab', array('allUsers' => $allUsers, 'allTags' => $allTags));
echo '</div>';
echo $this->element('popups', array('mainView' => false));