<form class="navbar-form" role="search">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Search" name="q" value="<?= getSearchQuery() ?>">
        <div class="input-group-btn">
            <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
        </div>
    </div>
</form>
<div>
    <p>you can use in search form: namespace:value</p>
    <p>avaible namespaces: name(default), id, rarity, orginal(japanese)</p>
    <p>example: iris rarity:gold</p>
</div>