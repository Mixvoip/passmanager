<h3>Users</h3>
<?= $this->element('searchbar', array('target' => '#usersTable tbody', 'class' => 'row')) ?>
<div class="row">
    <table class="table table-hover tablesorter" id="usersTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Last login</th>
            <th>Locked</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?= $this->element('users_table', array('users' => $allUsers)) ?>
        </tbody>
    </table>
</div>
<h3>Tags</h3>
<?= $this->element('searchbar', array('target' => '#tagTable tbody', 'class' => 'row')) ?>
<div class="row">
    <table class="table table-hover tablesorter" id="tagTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tag Name</th>
            <th>Parent Tag ID</th>
            <th>Users in tag</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?= $this->element('tags_table', array('tags' => $allTags)) ?>
        </tbody>
    </table>
</div>
<h3 class="inline openFormHeader">Create new User</h3>
<div class="openForm inline" data-toggle="tooltip" title="Toggle Create User form" data-target="#createUser">
    <span class="glyphicon glyphicon-chevron-down"></span>
    <span class="glyphicon glyphicon-chevron-up hide"></span>
</div>
<form class="row" id="createUser" style="display: none">
    <div class="alert alert-danger formMessage"></div>
    <div class="col-md-12">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_userpass">Your Password</span>
            <input name="create_user_yourPassword" type="password" class="form-control clearInput"
                   placeholder="Your Password"
                   aria-describedby="descInput_Admin_userpass" required>
        </div>
    </div>
    <div class="col-md-12">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_mail">Email</span>
            <input name="create_user_mail" type="text" class="form-control clearInput" placeholder="Email"
                   aria-describedby="descInput_Admin_mail">
        </div>
    </div>
    <div class="col-md-12">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_username">Username</span>
            <input name="create_user_username" type="text" class="form-control clearInput" placeholder="Username"
                   aria-describedby="descInput_Admin_username"
                   required>
        </div>
    </div>
    <div class="col-md-12">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_password">Password</span>
            <input name="create_user_password" type="password" class="form-control clearInput" placeholder="Password"
                   aria-describedby="descInput_Admin_password"
                   required>
        </div>
    </div>
    <div class="col-md-2">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_accesslevel">Access Level</span>
            <input name="create_user_accessLevel" type="number" class="form-control clearInput"
                   placeholder="Access Level"
                   aria-describedby="descInput_Admin_accesslevel"
                   value="0" required>
        </div>
    </div>
    <div class="col-md-2">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_level">The Admin Level is</span>
            <input type="text" class="form-control" placeholder="Access Level"
                   aria-describedby="descInput_Admin_level"
                   readonly value="<?= Configure::read('AccessLevel.Administration') ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_level_user">The Default User Level is</span>
            <input type="text" class="form-control" placeholder="Access Level"
                   aria-describedby="descInput_Admin_level_user"
                   readonly value="0">
        </div>
    </div>
    <div class="col-md-5">
        <div class="input-group">
            <div class="input-group-btn selectTagDropdown">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">Select Tag<span class="caret"></span></button>
                <ul class="dropdown-menu">
                    <li><a class="tagSelect" data-tagID="-1" href="#">[No Tag]</a></li>
                    <li role="separator" class="divider"></li>
                    <?= $this->element('tag_selector_list', array('allTags' => $allTags)) ?>
                </ul>
            </div>
            <input type="text" class="form-control tagNameValue" value="[No Tag]" placeholder="" readonly>
            <input type="hidden" name="tagID" value="-1" class="tagIdValue">
        </div>
    </div>
    <div class="col-md-12">
        <div class="input-group">
            <button name="create_user_action" type="submit" class="btn btn-success center-block">Create new User
            </button>
        </div>
    </div>
    <input type="hidden" name="task" value="createUser">
</form>
<!-- <br> separate the 2 headers -->
<h3 class="inline openFormHeader">Create new Tag</h3>
<div class="openForm inline" data-toggle="tooltip" title="Toggle Create User form" data-target="#createTag">
    <span class="glyphicon glyphicon-chevron-down"></span>
    <span class="glyphicon glyphicon-chevron-up hide"></span>
</div>
<form class="row" id="createTag" style="display: none">
    <div class="col-md-12">
        <div class="input-group">
            <span class="input-group-addon" id="descInput_Admin_tagName">Tag Name</span>
            <input name="tagName" type="text" class="form-control" placeholder="Tag name"
                   aria-describedby="descInput_Admin_tagName"
                   required>
        </div>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <div class="input-group-btn selectTagDropdown">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">Select Parent Tag <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="tagSelect" data-tagID="-1" href="#">Create new Root Tag</a></li>
                    <li role="separator" class="divider"></li>
                    <?= $this->element('tag_selector_list', array('allTags' => $allTags)) ?>
                </ul>
            </div>
            <input type="text" class="form-control tagNameValue" value="The Root Tag" placeholder="" readonly>
            <input type="hidden" name="parentTagID" value="<?= Configure::read('RootTag') ?>" class="tagIdValue">
        </div>
    </div>
    <div class="col-md-12">
        <div class="input-group">
            <button name="create_user_action" type="submit" class="btn btn-success center-block">Create new Tag
            </button>
        </div>
    </div>
    <input type="hidden" name="task" value="createTag">
</form>
<br>
<?= $this->Html->link(
    'Clear the Cache',
    array(
        'controller' => 'Passmanager',
        'action' => 'clearCache'
    ),
    array('title' => 'Clear the Cache')) ?>
