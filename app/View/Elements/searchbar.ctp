<div class="search <?= isset($class) ? $class : '' ?>">
    <div class="col-md-3">
        <div class="input-group">
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" title="Search" data-toggle="tooltip"><span
                        class="glyphicon glyphicon-search"></span></button>
            </span>
            <input type="text" title="Search (Shortcut: ctrl+y)" data-toggle="tooltip" class="form-control searchInput"
                   placeholder="Search for..."
                   data-searchBody="<?= $target ?>">
            <span class="input-group-btn">
                <button class="btn btn-default clearSearch" type="button" title="Clear Search"
                        data-toggle="tooltip">
                    <span class="glyphicon glyphicon-remove-sign"></span>
                </button>
            </span>
        </div>
    </div>
    <div class="col-md-2 regexSelect" style="display: none">
        <label>Using regex</label>
    </div>
</div>