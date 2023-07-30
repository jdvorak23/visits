<?php

namespace Jdvorak23\Visits;

use Jdvorak23\Visits\Cards\VisitCardsControl;

interface VisitCardsFactory
{
    public function create(): VisitCardsControl;
}