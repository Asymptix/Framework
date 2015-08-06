<div id="<?= $this->id ?>" class="modal hide">
    <div class="modal-header">
        <button data-dismiss="modal" class="close">×</button>
        <h3><?= $this->title ?></h3>
    </div>
    <div class="modal-body">
        <?= $this->text ?>
    </div>
    <div class="modal-footer">
        <?= $this->showButtons() ?>
    </div>
</div>