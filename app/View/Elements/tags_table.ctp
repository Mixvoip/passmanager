<?php
foreach ($tags as $tag) {
    if (!isset($tag['Tag']['parenttag_id'])) $tagName = '[Root Tag]';
    else $tagName = $tag['Tag']['parenttag_id'];
    echo "<tr data-tagID=\"{$tag['Tag']['id']}\">";
    echo "<td>{$tag['Tag']['id']}</td>";
    echo "<td>{$tag['Tag']['name']}</td>";
    echo "<td>$tagName</td>";
    echo "<td>" . count($tag['User']) . "</td>";
    $pwNum = 0;
    if (isset($countsTag, $countsTag[$tag['Tag']['id']])) {
        $pwNum = $countsTag[$tag['Tag']['id']];
    }
    echo "<td>$pwNum</td>";
    ?>
    <td>
        <button data-toggle="tooltip" title="Show users in tag" class="btn btn-xs btn-default showTagAction"><span
                class="glyphicon glyphicon-search"></span></button>
        <button data-toggle="tooltip" title="Delete Tag" class="btn btn-xs btn-default deleteTagAction"><span
                class="glyphicon glyphicon-minus-sign"></span></button>
        <button data-toggle="tooltip" title="Edit Tag" class="btn btn-xs btn-default editTagAction"><span
                class="glyphicon glyphicon-pencil"></span></button>
    </td>
    </tr>
<?php } ?>