<?php

namespace Jdvorak23\Visits\Model;

use Nette\InvalidStateException;
use Nette\SmartObject;

/**
 * @property int $percent
 * @property string $name
 */
class PageData
{
    use SmartObject;

    private ?int $propPercent = null;
    private ?string $propName = null;
    public function __construct(public readonly string $page,
                                public readonly int $count)
    {

    }

    /**
     * @return int
     * @throws InvalidStateException
     */
    protected function getPercent(): int
    {
        if(!isset($this->propPercent))
            throw new InvalidStateException("Property '\$percent' is accessed before initialisation");
        return $this->propPercent;
    }

    /**
     * @param int $percent
     */
    protected function setPercent(int $percent): void
    {
        $this->propPercent = $percent;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return $this->propName ?? $this->page;
    }

    /**
     * @param string $name
     */
    protected function setName(string $name): void
    {
        $this->propName = $name;
    }


}