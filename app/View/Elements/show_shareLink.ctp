<div id="showShareLink" class="popup">
    <h4>Your share Link</h4>
    <div id="closeShowShareLinkPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <p id="editFolderMsg" class="alert alert-danger hide"></p>
        <div class="col-md-12">
            <div class="input-group">
                <span id="desc_share_link" class="input-group-addon">Share Key</span>
                <input class="form-control" id="shareLink" aria-describedby="desc_share_link" readonly>
                <span class="input-group-btn">
                        <button class="btn btn-default copyToClipboard" data-clipboard-target="#shareLink"
                                type="button">Copy</button>
                </span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="input-group">
                <span id="desc_share_folder" class="input-group-addon">Folder id</span>
                <input class="form-control" id="sharefolderID" aria-describedby="desc_share_folder" readonly>
            </div>
        </div>
        <div class="form-group">
            <button class="btn btn-success center-block okButton">OK</button>
        </div>
    </div>
</div>