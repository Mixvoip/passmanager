<?php $this->assign('title', $title); ?>
<div class="row" id="loginPage">
    <form method="post" action="./" class="form-signin col-lg-4 col-md-4 col-sm-4 col-lg-offset-4">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputUsername" class="sr-only">Username</label>
        <input name="login_username" type="text" id="inputUsername" class="form-control" placeholder="Username" required
               autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="login_password" type="password" id="inputPassword" class="form-control" placeholder="Password"
               required>
        <!--<div class="checkbox">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>-->
        <br>
        <button name="login_submit" class="btn btn-lg btn-info btn-block" type="submit">Sign in</button>
    </form>
</div>
<?php
if (Configure::read('debug') > 0) {
    echo '<p class="alert alert-danger">DEBUG Mode is enabled. If you are in Production please deactivate it</p>';
}
?>
<div class="row" id="disclaimer">
    <?= $this->element('disclaimer', array()) ?>
</div>
