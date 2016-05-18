<h1>!! Debug !!</h1>
<p class="alert alert-danger">If you can access this page in Production please contact you administrator</p>
<p class="alert alert-warning">This is the Debug index page here you can access useful links for debugging the app</p>
<div>
    Version:
    <ul>
        <li>App Version: <code><?= Configure::read('Version.core') ?></code></li>
        <li>Database Version: <code><?= Configure::read('Version.db') ?></code></li>
    </ul>
</div>
<ul>
    <li><a href="./debug/cls">Clear the cache</a></li>
    <li><a href="./debug/dump">Dump the database</a></li>
    <li><a href="./debug/testInstall">Test cakePhp Installation</a></li>
    <!-- not needed any more <li><a href="./debug/insert">Insert test user</a></li>-->
    <li><a href="./debug/crypto">Test crypto</a></li>
</ul>