<?php

namespace Jdvorak23\Visits\Model;

use Nette\Database\Table\Selection;
use Nette\SmartObject;

/**
 * @property PageData[] $baseStats
 * @property PageData[] $pagesStats
 */
class PagesData
{
    use SmartObject;

    /** @var PageData[] */
    protected array $pages = [];
    protected ?PageData $basePage = null;
    protected int $total = 0;
    protected array $propBaseStats;
    protected bool $isPagesPercentSet = false;
    public function __construct(Selection $pagesVisits, string $basePage = '/')
    {
        foreach ($pagesVisits as $pageVisits){
            $this->total += $pageVisits->count;
            $pageData = new PageData($pageVisits->page, $pageVisits->count);
            if($pageVisits->page === $basePage)
                $this->basePage = $pageData;
            else
                $this->pages[] = $pageData;
        }
    }
    protected function getBaseStats(): ?array
    {
        if(!$this->basePage)
            return null;
        if(isset($this->propBaseStats))
            return $this->propBaseStats;
        $this->basePage->percent = (int) round($this->basePage->count * 100 / $this->total);
        $this->basePage->name = 'Domovská stránka';
        $othersCount = $this->total - $this->basePage->count;
        $otherPages = new PageData('Ostatní', $othersCount);
        $otherPages->percent = (int) round($othersCount * 100 / $this->total);
        return $this->propBaseStats = [$this->basePage, $otherPages];
    }
    protected function getPagesStats(): array
    {
        if($this->isPagesPercentSet)
            return $this->pages;
        $totalPagesVisits = $this->total - ($this->basePage?->count ?? 0);
        foreach ($this->pages as $page){
            $page->percent = (int) round($page->count * 100 / $totalPagesVisits);
        }
        $this->isPagesPercentSet = true;
        return $this->pages;
    }
}