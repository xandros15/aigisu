<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-help">
    How to upload file
</button>
<div id="modal-help" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-label-help">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-label-help">How to upload file</h4>
            </div>
            <div class="modal-body">
                <p>Blocked(male unit, bronze etc): <span  class="glyphicon glyphicon-remove-sign" style="color:red;" aria-hidden="true"></span></p>
                <p>Uploaded: <span  class="glyphicon glyphicon-ok-sign" style="color:green;" aria-hidden="true"></span></p>
                <p>Required: <span  class="glyphicon glyphicon-question-sign" style="color:blue;" aria-hidden="true"></span></p>
                <ol>
                    <li><h4>Run browser</h4></li>
                    <li><h4>Press <kbd>f12</kbd></h4></li>
                    <li><h4>Run game</h4></li>
                    <li><h4>Chose network tab and filter images</h4></li>
                    <img alt="image" src="<?= $this->siteUrl() ?>/upload/images/1.jpg">
                    <li><h4>Go to "Reministre" and chose what event you want to upload</h4></li>
                    <img alt="image" src="<?= $this->siteUrl() ?>/upload/images/2.jpg">
                    <li><h4>Check if load correctly</h4></li>
                    <img alt="image" src="<?= $this->siteUrl() ?>/upload/images/3.jpg">
                    <li><h4>Them copy url address or open in new tab</h4></li>
                    <img alt="image" src="<?= $this->siteUrl() ?>/upload/images/4.jpg">
                </ol>
                <p>Other browsers can have simlary options</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->