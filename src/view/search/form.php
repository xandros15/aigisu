<form class="navbar-form" role="search">
    <div class="col-xs-12">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Search" name="q" value="<?= getSearchQuery() ?>">
        <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </div>
    </div>
    </div>
    <div class="col-xs-12">
        <p class="help-block">
            You can use in search form: <code>&lt;namespace&gt;:&lt;value&gt;</code><br>
            avaible namespaces: <code>name, rarity, orginal</code><br>
            example: <code>iris rarity:gold</code><br>
        </p>
    </div>
</form>
