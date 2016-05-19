<?php
if (isset($_SESSION['user_id'])) {
    ?>
    <?= $this->element('searchbar', array('target' => ".searchable tbody", 'class' => 'searchNav')) ?>
    <p class='navbar-text vertical-align'>Welcome: <?= $this->Html->link(
            $this->Session->read('User.name'),
            array(
                'controller' => 'Passmanager',
                'action' => 'settings'
            ),
            array('title' => 'Settings')) ?></p>
    <?php if (isset($invites) && $invites > 0) { ?>
        <p class='navbar-text acceptInvites'>
            You have <?= $invites ?> Invites open.<br><a id="acceptInvites">Accept Invites</a>
        </p>
    <?php }
    if ($administration) { ?>
        <p class='navbar-text vertical-align'><?= $this->Html->link(
                'Administration',
                array(
                    'controller' => 'Passmanager',
                    'action' => 'administration'
                ),
                array('title' => 'Administration')) ?></p>
    <?php } ?>
    <p class="nav nav-pills navbar vertical-align right navbar-right logout">
        <?= $this->Html->link(
            'Logout',
            array(
                'controller' => 'Logout',
                'action' => 'index'
            ),
            array('class' => 'btn btn-danger', 'title' => 'Logout')
        ) ?>
    </p>
<?php }