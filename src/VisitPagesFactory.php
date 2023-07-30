<?php

namespace Jdvorak23\Visits;

use Jdvorak23\Visits\Pages\VisitPagesControl;

interface VisitPagesFactory
{
    public function create(): VisitPagesControl;
}