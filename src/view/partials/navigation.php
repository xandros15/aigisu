<nav class="navbar navbar-default navbar-inverse navbar-static-top">
    <div class="container-fluid" role="search">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#main-nav" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= $this->pathFor('home') ?>">Aigisu</a>
        </div>
        <div class="collapse navbar-collapse" id="main-nav">
            <form class="navbar-form navbar-right" action="<?= $this->pathFor('home') ?>">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search"
                           name="<?= \Models\Unit::SEARCH_PARAM ?>"
                           value="<?= $this->query(\Models\Unit::SEARCH_PARAM) ?>">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i>
                        </button>
                        <button type="button" class="btn btn-default" data-placement="bottom" data-toggle="popover"
                                title="how to use search form" data-container="body"
                                data-content="You can use in search form: &lt;namespace&gt;:&lt;value&gt; || avaible namespaces: name, rarity, original || example: iris rarity:gold || You can also use: male(for male units), dmm(for only dmm units), nutaku(for nutaku units)">
                            <span class="glyphicon glyphicon-question-sign"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</nav>