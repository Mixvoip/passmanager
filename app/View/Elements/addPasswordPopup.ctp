<div id="addPasswordPopup" class="popup tmpPopup">
    <h4>Add new Password</h4>
    <div class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <form id="addPasswordForm" class="form-horizontal" data-toggle="validator" role="form">
            <div class="alert alert-danger formMessage"></div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addPW_yourPW" class="input-group-addon">Your Password</span>
                    <input type="password" class="form-control clearInput" name="userPassword" id="yourPassword"
                           aria-describedby="desc_addPW_yourPW" placeholder="Your Password" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addPW_name" class="input-group-addon">Password name</span>
                    <input type="text" class="form-control" name="passwordName" placeholder="Password name"
                           aria-describedby="desc_addPW_name" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addPW_uname" class="input-group-addon">Username</span>
                    <input type="text" class="form-control" name="passwordUsername" placeholder="Username"
                           aria-describedby="desc_addPW_uname">
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addPW_pw" class="input-group-addon">Password</span>
                    <input type="text" class="form-control clearInput" name="passwordUserpass" placeholder="Password"
                           aria-describedby="desc_addPW_pw" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addPW_desc" class="input-group-addon">Password Description</span>
                    <input type="text" class="form-control" name="passwordDescription"
                           aria-describedby="desc_addPW_desc" placeholder="Password Description">
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addPW_url" class="input-group-addon">Site URL</span>
                    <input type="text" class="form-control" name="passwordSiteURL" placeholder="http://yourPage"
                           aria-describedby="desc_addPW_url">
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
            <div class="form-group">
                <button type="submit" name="addPassword" class="btn btn-success center-block">Add new Password</button>
            </div>
        </form>
    </div>
</div>
