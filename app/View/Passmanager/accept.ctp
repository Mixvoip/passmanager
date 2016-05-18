<?php $this->assign('title', $title);
$logoutBtn = '<p class="nav nav-pills navbar vertical-align right navbar-right logout"><a href="./Logout"><button class="btn btn-danger ">Logout</button></a></p>';
$this->assign('logoutButton', $logoutBtn);
?>
<div class="row">
    <form method="post" action="" class="form-signin col-lg-4 col-md-4 col-sm-4 col-lg-offset-4">
        <h2 class="form-signin-heading">Please enter your password</h2>
        <label for="inputPassword" class="sr-only">Password</label>
        <input name="password" type="password" id="inputPassword" class="form-control" placeholder="Password"
               required autofocus>
        <br>
        <button name="submit" class="btn btn-lg btn-info btn-block" type="submit">Accept</button>
    </form>
</div>