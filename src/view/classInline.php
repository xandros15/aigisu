<div class="col-xs-6 col-sm-2 form-group">
    <label class="control-label">Class</label>
    <select class="form-control unit-class" name="unit[class]">
        <option value="">null</option>
        <?php foreach ($classes as $class): ?>
            <?php
            $selected = (($unit->class) && ($class->id == $unit->class->id)) ? 'selected' : ''
            ?>
            <option value="<?= $class->id ?>"<?= $selected ?>><?= $class->name ?></option>
        <?php endforeach; ?>
    </select>
</div>