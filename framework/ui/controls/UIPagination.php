<?php

namespace Asymptix\ui\controls;

use Asymptix\core\Tools;

/**
 * Pagination UI control class.
 *
 * @category Asymptix PHP Framework
 * @author Dmytro Zarezenko <dmytro.zarezenko@gmail.com>
 * @copyright (c) 2009 - 2016, Dmytro Zarezenko
 *
 * @git https://github.com/Asymptix/Framework
 * @license http://opensource.org/licenses/MIT
 */
class UIPagination extends \Asymptix\ui\UIControl {
    /**
     * Default dialog panel HTML template.
     */
    const DEFAULT_TEMPLATE = "vendor/asymptix/framework/core/ui/templates/controls/ui_pagination.tpl.php";

    public $url = "";

    public $pagesNumber;
    public $firstPage;
    public $previousPage;
    public $currentPage;
    public $nextPage;
    public $lastPage;
    public $pagesOffset;

    /**
     * Display Pagination control.
     *
     * @param string $url Basic URL of the pagination links (common part).
     * @param int $pagesNumber Total pages number.
     * @param int $currentPage Current page number.
     * @param int $pagesOffset Number of visible pagination links before and
     *           after current page link.
     * @param string $template Path to the pagination HTML template.
     */
    public function __construct($url = "", $pagesNumber = 1, $currentPage = 1, $pagesOffset = 3, $template = "") {
        $this->url = $url;

        $this->pagesNumber = $pagesNumber;
        $this->setCurrentPage($currentPage);
        $this->pagesOffset = $pagesOffset;

        if ($this->pagesNumber > 1) {
            $this->correctPages();
        }

        if (empty($template)) {
            $template = self::DEFAULT_TEMPLATE;
        }
        parent::__construct([], $template);
    }

    public function show() {
        if ($this->pagesNumber > 1) {
            parent::show();
        }
    }

    private function correctPages() {
        $this->firstPage = $this->currentPage - $this->pagesOffset;
        $this->previousPage = $this->currentPage - 1;
        $this->nextPage = $this->currentPage + 1;
        $this->lastPage = $this->currentPage + $this->pagesOffset;
    }

    public function setCurrentPage($currentPage) {
        if (Tools::isInteger($currentPage)) {
            if ($currentPage < 0) {
                $this->currentPage = 1;
            } else {
                $this->currentPage = $currentPage;
            }
        } else {
            $this->currentPage = 1;
        }
    }

    public static function getCurrentPageNumber($page, $totalPages) {
        $page = (int)$page;
        $totalPages = (int)$totalPages;

        if ($page < 1 || $totalPages < 1) {
            return 1;
        }

        if ($page > $totalPages) {
            return $totalPages;
        }

        return $page;
    }

}
