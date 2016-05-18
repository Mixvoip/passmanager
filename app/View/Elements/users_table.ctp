<?php
foreach ($users as $user) {
    if ($user['User']['lastlogin'] != '') {
        $date = date(DateTime::RFC850, strtotime($user['User']['lastlogin']));
    } else {
        $date = 'never';
    }
    echo "<tr data-userID=\"{$user['User']['id']}\">";
    echo "<td>{$user['User']['id']}</td>";
    echo "<td>{$user['User']['username']}</td>";
    echo "<td>$date</td>";
    echo "<td>" . ($user['User']['blocked'] == 1 ? 'Yes' : 'No') . "</td>";
    ?>
    <td>
        <button data-toggle="tooltip" title="Toggle User Lock" class="btn btn-xs btn-default lockUserAction"><span
                class="glyphicon glyphicon-lock"></span></button>
        <button data-toggle="tooltip" title="Delete User" class="btn btn-xs btn-default deleteUserAction"><span
                class="glyphicon glyphicon-minus-sign"></span></button>
        <button data-toggle="tooltip" title="Edit User" class="btn btn-xs btn-default editUserAction"><span
                class="glyphicon glyphicon-pencil"></span></button>
    </td>
    </tr>
<?php } ?>