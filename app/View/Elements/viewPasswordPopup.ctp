<div id="viewPasswordPopup" class="popup">
    <h4>Show Password</h4>
    <div id="closeViewPasswordPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <p id="viewPasswordMsg" class="alert alert-danger">&nbsp;</p>
    <div class="row">
        <form class="form-horizontal" id="viewPasswordForm">
            <div class="form-group">
                <label class="col-sm-2 control-label">Page</label>
                <div class="col-sm-2">
                    <p class="form-control-static pageLink"></p>
                </div>
                <label class="col-sm-2 control-label">Description</label>
                <div class="col-sm-4">
                    <p class="form-control-static passwordDescription"></p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <label id="desc_showPW_yourPassword" class="input-group-addon">Your Password</label>
                    <input type="password" name="yourPassword" class="form-control" placeholder="Your Password"
                           aria-describedby="desc_showPW_yourPassword" required>
                    <span class="input-group-btn">
                        <button class="btn btn-success" type="submit">Show Passwords</button>
                    </span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <label id="desc_showPW_uname" class="input-group-addon">Username</label>
                    <input type="text" class="form-control" id="encUsername" placeholder=""
                           aria-describedby="desc_showPW_uname" readonly>
                    <span class="input-group-btn">
                        <button class="btn btn-default copyToClipboard" data-clipboard-target="#encUsername"
                                type="button">Copy</button>
                    </span>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <label id="desc_showPW_pw" class="input-group-addon">Password</label>
                    <input type="text" class="form-control" id="encPassword" placeholder=""
                           aria-describedby="desc_showPW_pw" readonly>
                    <span class="input-group-btn">
                        <button class="btn btn-default copyToClipboard passwordCopyButton"
                                data-clipboard-target="#encPassword"
                                type="button">Copy</button>
                    </span>
                </div>

            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <label id="desc_showPW_url" class="input-group-addon">URL</label>
                    <input type="text" class="form-control" id="urlCopy" placeholder=""
                           aria-describedby="desc_showPW_url" readonly>
                    <span class="input-group-btn">
                        <button class="btn btn-default copyToClipboard"
                                data-clipboard-target="#urlCopy"
                                type="button">Copy</button>
                    </span>
                    <span class="input-group-btn">
                        <a class="btn btn-default passwordCopyButton disabled"
                           type="button" id="urlCopyLink" href="" target="_blank">Go to URL</a>
                    </span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <label id="desc_showPW_tag" class="input-group-addon">Tag Name</label>
                    <input type="text" class="form-control passwordTag" placeholder=""
                           aria-describedby="desc_showPW_tag" readonly>
                </div>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <button title="Edit Password" class="btn btn-sm btn-default" id="editPasswordShow"><span
                            class="glyphicon glyphicon-pencil"></span></button>
                </div>
            </div>
            <input type="hidden" name="passwordID" id="viewPasswordPopupID" value="-1">
            <input type="hidden" name="folderID" id="viewPasswordFolderID" value="-1">
        </form>
    </div>
    <div class="hidden">
        <button id="autoCopy">Auto Copy to clipboard</button>
    </div>
</div>