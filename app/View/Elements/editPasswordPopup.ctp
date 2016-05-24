<div id="editPasswordPopup" class="popup tmpPopup">
    <h4>Edit Password</h4>
    <div class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <form class="form-horizontal" data-toggle="validator" role="form" method="post" action="./Passwords/edit"
              id="editPasswordForm">
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editPW_yourPassword" class="input-group-addon">Your Password</span>
                    <input type="password" class="form-control" name="userPassword" id="yourPassword"
                           aria-describedby="desc_editPW_yourPassword" placeholder="Your Password" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editPW_name" class="input-group-addon">Password name</span>
                    <input type="text" class="form-control" name="passwordName" value="<?= $entry['site_name'] ?>"
                           aria-describedby="desc_editPW_name" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editPW_uname" class="input-group-addon">Username</span>
                    <input type="text" class="form-control" name="passwordUsername" placeholder="*************"
                           aria-describedby="desc_editPW_uname">
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editPW_pw" class="input-group-addon">Password</span>
                    <input type="text" class="form-control" name="passwordUserpass" placeholder="*************"
                           aria-describedby="desc_editPW_pw">
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editPW_desc" class="input-group-addon">Password Description</span>
                    <input type="text" class="form-control" name="passwordDescription"
                           aria-describedby="desc_editPW_desc" value="<?= $entry['site_description'] ?>">
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editPW_url" class="input-group-addon">Site URL</span>
                    <input type="text" class="form-control" name="passwordSiteURL" value="<?= $entry['site_url'] ?>"
                           aria-describedby="desc_editPW_url">
                </div>
            </div>
            <!-- todo remove this and add multi select. see: editUserPopup.php:48 -->
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
            <input type="hidden" name="folderID" value="-1">
            <input type="hidden" name="passwordID" value="<?= $entry['id'] ?>">
            <input type="hidden" name="task" value="editPassword">
            <div class="form-group">
                <button type="submit" class="col-sm-offset-5 col-sm-2 btn btn-success">Edit Password</button>
                <button class="deletePassword col-sm-offset-3 col-sm-1 btn btn-danger" data-toggle="tooltip"
                        title="Delete Password">
                    <span class="glyphicon glyphicon-remove"></span>
                </button>
            </div>
        </form>
    </div>
</div>
