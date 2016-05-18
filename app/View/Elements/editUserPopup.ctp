<div id="editUserPopup" class="popup tmpPopup">
    <h4>Edit User</h4>
    <div id="closeAddFolderPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <p id="editUserMsg" class="alert alert-danger hide"></p>
        <form id="editUserForm">
            <div class="alert alert-danger formMessage"></div>
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="edit_User_desc_username">Username name</span>
                    <input type="text" class="form-control" aria-describedby="edit_User_desc_username"
                           name="edit_user_name" value="<?= $entry['username'] ?>" placeholder="Username" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="edit_User_email">Email</span>
                    <input type="text" class="form-control" aria-describedby="edit_User_email" name="edit_user_mail"
                           value="<?= $entry['email'] ?>" placeholder="email">
                </div>
            </div>
            <?php
            if (!Configure::read('Recovery.Enable')) {
                //Hide it completely
                //echo '<p class="col-md-12 alert alert-warning"> You cannot change the Password because the recovery is disabled.</p>';
            } else {
                ?>
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-addon" id="edit_User_password">Password</span>
                        <input name="edit_user_password" type="password" class="form-control"
                               aria-describedby="edit_User_password"
                               placeholder="Password"
                            <?= Configure::read('Recovery.Enable') ? '' : 'readonly' ?> required>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="col-md-12">
                <div class="input-group">
                    <span class="input-group-addon" id="edit_user_accessLevel">Access Level</span>
                    <input name="edit_user_accessLevel" type="number" class="form-control" placeholder="Access Level"
                           aria-describedby="edit_user_accessLevel"
                           value="<?= $entry['access_level'] ?>" required>
                </div>
            </div>
            <div class="col-md-12">
                <label class="control-label col-md-3">Select Users Tags</label>
                <div class="col-md-5">
                    <select name="tags[]" class="form-control" multiple>
                        <?php
                        foreach ($userTagList as $tag) {
                            $selected = ($tag['subscribed']) ? 'selected' : '';
                            echo "<option value=\"{$tag['id']}\" $selected>{$tag['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>
                        <input name="edit_user_blocked" type="checkbox" value="true"
                            <?= $entry['blocked'] == 1 ? 'checked' : '' ?>> User blocked
                    </label>
                </div>
            </div>
            <input type="hidden" name="user_id" value='<?= $entry['id'] ?>'>
            <input type="hidden" name="task" value="editUser">
            <div class="form-group col-md-12">
                <button type="submit" name="editFolder" class="btn btn-success center-block">Edit User</button>
            </div>
        </form>
    </div>
</div>
