<?php

namespace Jdvorak23\Visits\Model;

use Nette\SmartObject;

/**
 * @property-read int $viewsAverage
 * @property-read int $uipAverage
 */
class VisitsData
{
    use SmartObject;
    public function __construct(public int $views,
                                public int $uip,
                                public int $units,
                                public string $unitName = "den")
    {
    }

    protected function getViewsAverage(): int
    {
        return round($this->views / $this->units);
    }

    protected function getUipAverage(): int
    {
        return round($this->uip / $this->units);
    }

}