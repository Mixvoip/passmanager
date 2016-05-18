<?php
//Variable pass-thru
$this->assign('title', $title);
$this->assign('administration', $administration);
?>
    <div class="row" id="settings">
        <h3>Your Settings</h3>
        <form class="form-horizontal passwordMatch" action="Users/changePassword" method="post"
              id="changePasswordForm">
            <div class="col-sm-12">
                <div class="input-group">
                    <span id="desc_settings_mail" class="input-group-addon">Email</span>
                    <input type="text" class="form-control" value="<?= $userMail ?>" readonly
                           aria-describedby="desc_settings_mail" placeholder="">
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-group">
                    <label id="desc_settings_OldPassword" class="input-group-addon">OLD Password</label>
                    <input type="password" name="oldPassword" class="form-control" id="inputOldPassword"
                           placeholder="Old Password" required aria-describedby="desc_settings_OldPassword">
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-group">
                    <label id="desc_settings_NewPassword" class="input-group-addon">New Password</label>
                    <p class="alert alert-danger passwordAlert"></p>
                    <input type="password" name="newPassword" class="form-control srcPassword"
                           id="inputNewPassword" placeholder="New Password" required
                           aria-describedby="desc_settings_NewPassword">
                </div>
            </div>
            <div class="col-sm-12">
                <div class="input-group">
                    <label id="desc_settings_confirmPassword" class="input-group-addon">Confirm Password</label>
                    <input type="password" name="confirmPassword" class="form-control matchPassword"
                           id="confirmPassword" placeholder="Confirm Password" required
                           aria-describedby="desc_settings_confirmPassword">
                </div>
            </div>
            <input type="hidden" name="task" value="userEditPassword">
            <div class="form-group">
                <button class="btn btn-success center-block" id="changePasswordFormButton">Change Password
                </button>
            </div>
        </form>
        <h4 class="inline">Subscribed Tags</h4>
        <div class="showTags inline" data-toggle="tooltip" title="Show my tags">
            <span class="glyphicon glyphicon-chevron-down"></span>
            <span class="glyphicon glyphicon-chevron-up hide"></span>
        </div>
        <ul class="row" style="display: none">
            <?php
            foreach ($userTags as $tag) {
                echo "<li>{$tag['Tag']['name']}</li>";
            }
            ?>
        </ul>
    </div>
<?= $this->element('popups', array('mainView' => false)) ?>