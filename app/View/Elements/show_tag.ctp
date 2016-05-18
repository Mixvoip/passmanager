<div id="showTag" class="popup tmpPopup" data-tagid="<?= $tag['Tag']['id'] ?>">
    <h4>Show Tag</h4>
    <div id="closeAddFolderPopup" class="closeBTN"><span class="glyphicon glyphicon-remove"></span></div>
    <div class="row">
        <p id="editFolderMsg" class="alert alert-danger hide"></p>
        <div class="col-md-12">
            <div class="input-group">
                <span id="desc_tag_name" class="input-group-addon">Tag name</span>
                <p class="form-control" id="folderName" aria-describedby="desc_tag_name">
                    <?= $tag['Tag']['name'] ?>
                </p>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table table-hover tablesorter">
                <thead>
                <tr>
                    <th class="col-md-2">ID</th>
                    <th class="col-md-7">Username</th>
                    <th class="col-md-3">Remove from Tag</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($users as $user) {
                    echo "<tr data-userid=\"{$user['User']['id']}\">";
                    echo "<td>{$user['User']['id']}</td>";
                    echo "<td>{$user['User']['username']}</td>";
                    echo '<td>
                          <button data-toggle="tooltip" title="Remove user" class="btn btn-xs btn-default removeUserFromTagAction">
                          <span class="glyphicon glyphicon-remove"></span></button>
                      </td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>