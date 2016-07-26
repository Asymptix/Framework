<div>
    <input type="checkbox"
           name="<?= $this->name ?>"
           value="<?= $this->value ?>"
           <?= (($this->checked) ? (' checked="yes"') : ('')) ?>
    />
    <span>&nbsp;<?= $this->title ?></span>
</div>