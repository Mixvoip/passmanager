<div id="addFolderPopup" class="popup">
    <h4>Add new Folder</h4>
    <div id="closeAddFolderPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <p id="addFolderMsg" class="alert alert-danger hide"></p>
        <form id="addFolderForm">
            <div class="form-group folderOptions">
                <label class="col-sm-2 control-label">Options</label>
                <div class="col-sm-4 checkbox">
                    <label><input name="privateFolder" type="checkbox" value="true">Private Folder</label>
                </div>
                <div class="col-sm-4 checkbox">
                    <label><input name="shareWithAll" type="checkbox" value="true">Send invite to all users</label>
                </div>
            </div>
            <div class="col-sm-12">
                <p class="alert alert-warning" id="privateFolderWarning"><span
                        class="glyphicon glyphicon-warning-sign"></span>&nbsp; Private folders can
                    <em><b>never</b></em> be shared</p>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_add_folder_yourPassword" class="input-group-addon">Your Password</span>
                    <input type="password" class="form-control" name="userPassword" placeholder="Your Password"
                           aria-describedby="desc_add_folder_yourPassword" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addFolder_name" class="input-group-addon">Folder name</span>
                    <input type="text" class="form-control" name="folderName" id="folderName" placeholder="Folder name"
                           aria-describedby="desc_addFolder_name" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_addFolder_desc" class="input-group-addon">Description</span>
                    <input type="text" class="form-control" name="folderDescription" id="folderDescription"
                           aria-describedby="desc_addFolder_desc" placeholder="Folder Description">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <button type="submit" name="createFolder" class="btn btn-success center-block">Create folder
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>