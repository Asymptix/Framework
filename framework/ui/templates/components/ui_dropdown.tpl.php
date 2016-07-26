<div>
    <label>
        <?php if (isset($this->dataSet[$this->currentValue])): ?>

        <?php

        new UI_ListOption(
            $this->currentValue,
            $this->dataSet[$this->currentValue],
            null,
            'core/ui/templates/components/ui_list_label.tpl.php'
        )

        ?>

        <?php endif; ?>
    </label>
    <select
        <?php if (!empty($this->id)): ?> id="<?= $this->id ?>" <?php endif; ?>
        <?php if (!empty($this->name)): ?> name="<?= $this->name ?>" <?php endif; ?>
        <?php if (!empty($this->class)): ?> class="<?= $this->class ?>" <?php endif; ?>>
    <?php foreach ($this->dataSet as $optionValue => $option):  ?>

        <?php

        new UI_ListOption(
            $optionValue,
            $option,
            $this->currentValue,
            'core/ui/templates/components/ui_listoption.tpl.php'
        )

        ?>

    <?php endforeach; ?>
    </select>
</div>