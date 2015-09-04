<div class="span12 text-center">
<ul class="pagination">

    <?php if ($this->firstPage > 1): ?>

    <li><a href="<?= $this->url ?>?pn=1">&laquo;</a></li>

    <?php endif; ?>

    <?php if ($this->previousPage > 0): ?>

    <li><a href="<?= $this->url ?>?pn=<?= $this->previousPage ?>">&lsaquo;</a></li>

    <?php endif; ?>

    <?php for ($i = $this->firstPage; $i <= $this->lastPage; $i ++): if($i > 0 && $i <= $this->pagesNumber): ?>

    <li<?= ($this->currentPage == $i?' class="active"':'') ?>><a href="<?= $this->url ?>?pn=<?= $i ?>"><?= $i ?></a></li>

    <?php endif; endfor; ?>

    <?php if ($this->nextPage <= $this->pagesNumber): ?>

    <li><a href="<?= $this->url ?>?pn=<?= $this->nextPage ?>">&rsaquo;</a></li>

    <?php endif; ?>

    <?php if ($this->lastPage < $this->pagesNumber): ?>

    <li><a href="<?= $this->url ?>?pn=<?= $this->pagesNumber ?>">&raquo;</a></li>

    <?php endif; ?>

</ul>
</div>