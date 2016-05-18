<div class="row passwordEntrys">
    <div class="passwordEntry">
        <table class="table table-hover tablesorter searchable">
            <thead>
            <tr>
                <th class="col-md-2">Password Name</th>
                <th class="col-md-5">Password Description</th>
                <?= isset($fav) ? '<th class="col-md-2">Folder Name</th>' : '' ?>
                <th class="col-md-2">Tag Name</th>
                <th class="col-md-1">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($entrys)) {
                foreach ($entrys as $entry) {
                    $valid = isUrlValid($entry['site_url']);
                    echo "<tr data-id=\"{$entry['id']}\" data-folderid=\"{$entry['folder_id']}\">";
                    if (isset($entry['site_url']) && $entry['site_url'] != '') {
                        echo "<td class=\"col-md-2 passwordName\" data-urlvalid=\"$valid\" data-siteurl=\"{$entry['site_url']}\">";
                        if ($valid == 1) {
                            echo "<a target=\"_blank\" href=\"{$entry['site_url']}\">{$entry['site_name']}</a>";
                        } else {
                            echo $entry['site_name'];
                        }
                        echo "</td>";
                    } else {
                        echo "<td class=\"col-md-2 passwordName\">{$entry['site_name']}</td>";
                    }
                    $desc = str_replace('\n', '<br>', htmlspecialchars($entry['site_description']));
                    echo "<td class=\"col-md-5 passwordDescription\">$desc</td>";
                    if (isset($fav)) {
                        echo "<td class=\"col-md-2 passwordFolderName\">{$entry['folder_name']}</td>";
                    }
                    echo "<td class=\"col-md-2 passwordTag\">{$entry['tag_name']}</td>";
                    ?>
                    <td class="col-md-1">
                        <button class="btn btn-sm btn-default favoritePassword" data-toggle="tooltip"
                                data-placement="right"
                                title="Mark as Favorite"><span
                                class="glyphicon glyphicon-star<?= ($entry['favorite'] == 1) ? '' : '-empty' ?>"></span>
                        </button>
                        <button class="btn btn-sm btn-default showPassword" data-toggle="tooltip" data-placement="right"
                                title="Display Password"><span class="glyphicon glyphicon-search"></span></button>
                        <button class="btn btn-sm btn-default editPassword" data-toggle="tooltip" data-placement="right"
                                title="Edit Password"><span class="glyphicon glyphicon-pencil"></span></button>
                    </td>
                    <?php
                    echo '</tr>';
                }
            } ?>
            </tbody>
        </table>
    </div>
</div>