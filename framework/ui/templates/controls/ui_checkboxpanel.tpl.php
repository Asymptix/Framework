<?php if ($this->title): ?>

<fieldset class="CheckBoxPanel">
    <legend>&nbsp;<?= $this->title ?>&nbsp;</legend>

<?php endif; ?>
<?php if ($this->tip): ?>

    <h1><?= $this->tip ?></h1>

<?php endif; ?>

<?php if ($this->scrollable): ?>

    <div class="checkContainerScrollable">

<?php else: ?>

    <div class="checkContainer">

<?php endif; ?>

<?php foreach ($this->dataSet as $checkbox): ?>

    <?php

    new UI_CheckBox(
        [
            'name'    => $this->name.'[]',
            'value'   => $checkbox->id,
            'title'   => $checkbox->title,
            'checked' => in_array($checkbox->id, $this->currentValue),
        ],
        'core/ui/templates/components/ui_checkbox.tpl.php'
    )

    ?>

<?php endforeach; ?>

        <div style="clear:both;"></div>
    </div>

<?php if ($this->title): ?>

</fieldset>

<?php endif; ?>