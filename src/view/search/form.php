<form class="navbar-form navbar-right" style="display:inline-block" role="search">
    <div class="col-xs-12 form-group">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search" name="q" value="<?= Main::$app->getSearchQuery() ?>">
            <div class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                <button type="button" class="btn btn-default" data-toggle="popover" title="Popover title" data-content="lol"><span class="glyphicon glyphicon-question-sign"></span></button>
            </div>
        </div>
    </div>
    <!--        <div class="col-xs-6">
            <p class="help-block">
                You can use in search form: <code>&lt;namespace&gt;:&lt;value&gt;</code><br>
                avaible namespaces: <code>name, rarity, original</code><br>
                example: <code>iris rarity:gold</code><br>
            </p>
        </div>
        <div class="col-xs-6">
            <p class="help-block">
                You can also use:<br>
                <code>male</code>(for male units),<br>
                <code>dmm</code>(for only dmm units)<br>
                <code>nutaku</code>(for nutaku units)<br>
            </p>
        </div>-->
</form>
