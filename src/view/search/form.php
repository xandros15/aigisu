<?php

?>
<div class="container-fluid">
    <form class="navbar-form navbar-right" action="<?= $this->pathFor('home') ?>" style="display:inline-block"
          role="search">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search" name="q" value="<?= $this->query('q') ?>">
            <div class="input-group-btn">
                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                <button type="button" class="btn btn-default" data-placement="bottom" data-toggle="popover"
                        title="how to use search form" data-container="body"
                        data-content="You can use in search form: &lt;namespace&gt;:&lt;value&gt; || avaible namespaces: name, rarity, original || example: iris rarity:gold || You can also use: male(for male units), dmm(for only dmm units), nutaku(for nutaku units)">
                    <span class="glyphicon glyphicon-question-sign"></span>
                </button>
                <a href="<?= $this->pathFor('oauth') ?>" role="button" class="btn btn-default"><span
                        class="glyphicon glyphicon-log-in"></span></a>
            </div>
        </div>
    </form>

</div>