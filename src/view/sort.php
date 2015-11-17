<div class="row">
    <div class="col-xs-3 col-sm-2">
        <label>Order by:</label>
    </div>
    <div class="col-xs-9 col-sm-3">
        <label><a href="<?= generateLink(['sort' => 'name']) ?> ">Name</a></label>
    </div>
    <div class="col-xs-9 col-sm-3 col-xs-push-3 col-sm-push-0">
        <label><a href="<?= generateLink(['sort' => 'orginal']) ?> ">Orginal name</a></label>
    </div>
    <div class="col-xs-9 col-sm-2 col-xs-push-3 col-sm-push-0">
        <label><a href="<?= generateLink(['sort' => 'rarity']) ?> ">Rarity</a></label>
    </div>
</div>