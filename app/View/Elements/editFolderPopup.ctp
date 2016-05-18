<div id="editFolderPopup" class="popup tmpPopup">
    <h4>Edit Folder</h4>
    <div id="closeAddFolderPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <p id="editFolderMsg" class="alert alert-danger hide"></p>
        <form class="form-horizontal" action="Folder/edit" method="post">
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editFolder_name" class="input-group-addon">Folder name</span>
                    <input type="text" class="form-control" name="folderName" id="folderName"
                           aria-describedby="desc_editFolder_name" value="<?= $entry['name'] ?>" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input-group">
                    <span id="desc_editFolder_desc" class="input-group-addon">Description</span>
                    <input type="text" class="form-control" name="folderDescription" id="folderDescription"
                           aria-describedby="desc_editFolder_desc" value="<?= $entry['description'] ?>">
                </div>
            </div>
            <!--            <div class="form-group">-->
            <!--                <label class="col-sm-2 control-label">Folder Image</label>-->
            <?php
            if (isset($images) && false) {
                    echo '<select name="imageID">';
                    echo '<option value="-1">No Image</option>';
                    foreach ($images as $image) {
                        $selected = '';
                        if ($image['Image']['id'] == $entry['image_id']) $selected = 'selected';
                        echo "<option value=\"{$image['Image']['id']}\" $selected >{$image['Image']['name']}</option>";
                    }
                    echo '</select>';
                } else { ?>
                <!--<p class="form-control-static alert alert-warning">
                    <span class="glyphicon glyphicon-warning-sign"> </span>
                    No Images had been uploaded
                </p>-->
                <?php } ?>
            <!--            </div>-->
            <input type="hidden" name="folderID" value="<?= $entry['id'] ?>">
            <input type="hidden" name="task" value="editFolder">
            <div class="form-group">
                <button type="submit" name="editFolder" class="btn btn-success center-block">Edit Folder</button>
            </div>
        </form>
    </div>
</div>