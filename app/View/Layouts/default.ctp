<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $this->fetch('title'); ?>
    </title>
    <?php
    echo $this->Html->meta('icon');

    //echo $this->Html->css('cake.generic');
    echo $this->Html->css(array('normalize', 'bootstrap.min', 'bootstrap-theme.min', 'sorterStyle', 'main'));
    echo $this->fetch('meta');
    echo $this->fetch('css');
    echo $this->fetch('script');
    ?>
</head>
<body>
<div id="wrapper">
    <nav class="navbar navbar-default row">
        <div class="container-fluid">
            <header class="navbar-header">
                <?php
                echo $this->Html->image('mixvoip.png', array('alt' => 'MixViop Logo', 'class' => 'navbar-text'));
                echo $this->Html->link(
                    '<h2 class="navbar-text vertical-align">Password Manager</h2>',
                    array(
                        'controller' => 'Passmanager',
                        'action' => 'index'
                    ),
                    array('escape' => false)
                );
                ?>
            </header>
            <?php if (Configure::read("QuickAccess.Enable")) { ?>
                <ul class="nav nav-pills navbar-nav vertical-align">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                           aria-expanded="false"><?= Configure::read("QuickAccess.Title") ?><span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <?php
                            $quickAccessArray = Configure::read("QuickAccess.Menu");
                            foreach ($quickAccessArray as $link => $item) {
                                if ($item == "---") {
                                    echo '<li role="separator" class="divider"></li>';
                                } else {
                                    echo "<li><a href=\"$link\">$item</a></li>";
                                }
                            } ?>
                        </ul>
                    </li>
                </ul>
            <?php }
            if (isset($administration)) echo $this->element('navBar', array($administration));
            ?>
        </div>
    </nav>
    <main class="container-fluid" id="content">
        <?= $this->fetch('content') ?>
    </main>
    <?php
    $flash = $this->Flash->render();
    if ($flash == '') {
        $display = 'block';
        $display_inv = 'none';
    } else {
        $display_inv = 'block';
        $display = 'none';
    }
    echo "<div class=\"message\" style=\"display: $display_inv;\">" . $flash . '</div>'
    ?>
    <footer>
        2016 &copy; MIXvoip S.a. Questions: <a href="mailto:claures@mixvoip.com">ask here</a>
    </footer>
</div>
<script type="application/javascript">
    var minPwLength = <?=Configure::read('MinPasswordLength')?>;
    var folderPrefix = '<?=Configure::read('UI.FolderSearchPrefix')?>';
    var folderJmpPrefix = 'f:';
    var baseUrl = '<?=FULL_BASE_URL . $this->webroot?>';
</script>
<?php
echo $this->Html->script(array('jquery.min', 'bootstrap.min', 'clipboard.min', 'ui/general', 'ui/folder', 'ui/password', 'ui/invites', 'ui/search', 'jquery.tablesorter.min'));
if (isset($administration) && $administration) {
    echo $this->Html->script(array('ui/admin'));
}
?>
</body>
</html>
