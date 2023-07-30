<?php

namespace Jdvorak23\Visits\Cards;

use Jdvorak23\Visits\Model\VisitManager;
use Nette\Application\UI\Control;

class VisitCardsControl extends Control
{
    const template = __DIR__ . '/cards.latte';

    public function __construct(protected VisitManager $visitManager)
    {
    }

    public function render(): void
    {
        $this->template->setFile(self::template);
        $this->template->total = $this->visitManager->getTotal();
        $this->template->year = $this->visitManager->getYear();
        $this->template->month = $this->visitManager->getMonth();
        $this->template->week = $this->visitManager->getWeek();
        $this->template->render();
    }
}