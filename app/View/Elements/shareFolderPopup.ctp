<div id="shareFolderPopup" class="popup tmpPopup">
    <h4>Share Folder</h4>
    <div class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <form class="form-horizontal" data-toggle="validator" role="form">
            <div class="alert alert-danger formMessage"></div>
            <div class="alert alert-success formMessage"></div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editPW_yourPassword" class="input-group-addon">Your Password</span>
                    <input type="password" class="form-control" name="userPassword" id="yourPassword"
                           aria-describedby="desc_editPW_yourPassword" placeholder="Your Password" required>
                </div>
            </div>
            <?php if (isset($users) && count($users) > 0) { ?>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Invite new User</label>
                    <select name="userID[]" multiple class="col-md-6">
                        <?php foreach ($users as $user) {
                            echo "<option value=\"{$user['User']['id']}\">{$user['User']['username']}</option>";
                        } ?>
                    </select>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning col-md-12">No Users left to share</div>
            <?php } ?>
            <input type="hidden" name="folderID" value="<?= $entry['id'] ?>">
            <input type="hidden" name="task" value="shareFolder">
            <div class="form-group">
                <button type="submit" class="col-sm-offset-5 col-sm-2 btn btn-success">Invite User</button>
            </div>
        </form>
    </div>
</div>
