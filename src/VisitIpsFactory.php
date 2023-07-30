<?php

namespace Jdvorak23\Visits;

use Jdvorak23\Visits\Ips\VisitIpsControl;

interface VisitIpsFactory
{
    public function create(): VisitIpsControl;
}