<div id="acceptInvitePopup" class="popup reloadOnClose tmpPopup">
    <h4>Accept Invites</h4>
    <div id="closeAddFolderPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <form class="row" id="acceptInviteForm">
        <p class="alert alert-danger formMessage hide"></p>
        <div class="col-md-12">
            <div class="input-group">
                <span id="desc_invite_pw" class="input-group-addon">Your Password</span>
                <input type="password" class="form-control" name="user_pass" aria-describedby="desc_invite_pw" required>
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group">
                <span id="desc_key" class="input-group-addon">Your Key</span>
                <input type="text" class="form-control" name="invite_key" aria-describedby="desc_key" required>
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group">
                <div class="input-group-btn selectFolderDropdown">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Select Folder<span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($folders as $folder) {
                            echo '<li><a class="folderDropdownSelect" data-folderID="' . $folder['Folder']['id'] . '" href="#">' . "{$folder['Folder']['id']} [{$folder['Folder']['name']}]" . '</a></li>';
                        } ?>
                    </ul>
                </div>
                <input type="text" class="form-control folderNameValue"
                       value="<?= $folders[0]['Folder']['id'] . '[' . $folders[0]['Folder']['name'] ?>]"
                       placeholder="" readonly>
                <input type="hidden" name="folderID" value="<?= $folders[0]['Folder']['id'] ?>" class="folderIdValue"
                       readonly>
            </div>
        </div>
        <input type="hidden" name="task" value="acceptInvite">
        <div class="form-group">
            <button title="Accept selected Invite" type="submit" class="btn btn-success col-md-offset-3 col-md-2">
                Accept
            </button>
            <button title="Reject selected Invite" class="btn btn-danger col-md-offset-2 col-md-2 reject">Reject
            </button>
        </div>
    </form>
</div>