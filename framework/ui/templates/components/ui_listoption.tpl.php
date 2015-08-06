<?php if ($this->currentValue == $this->value): ?>

<option value="<?= $this->value ?>" selected="yes"><?= $this->title ?></option>

<?php else: ?>

<option value="<?= $this->value ?>"><?= $this->title ?></option>

<?php endif; ?>