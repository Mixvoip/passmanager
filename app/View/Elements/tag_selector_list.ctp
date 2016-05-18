<?php foreach ($allTags as $tag) {
    echo '<li><a class="tagSelect tagGenerated" data-tagID="' . $tag['Tag']['id'] . '" href="#">' . $tag['Tag']['name'] . '</a></li>';
}