<?php

namespace Jdvorak23\Visits\Pages;

use Jdvorak23\Visits\Model\VisitManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\DateTime;
class VisitPagesControl extends Control
{
    const template = __DIR__ . '/pages.latte';

    const periodSelectId = "frm-pages-pagesForm-period"; // Aby byl stejný pro javascript

    protected ?DateTime $from = null;

    public function __construct(protected VisitManager $visitManager)
    {
    }

    public function render(): void
    {
        $this->template->setFile(self::template);
        $this->template->pages = $this->visitManager->getPagesVisits($this->from);
        $this->template->render();
    }

    protected function createComponentPagesForm(): Form
    {
        $form = new Form();
        $items = [
            '' => 'Celkem',
            'first day of january this year' => 'Letos',
            '-30 days' => '30 dní',
            '-6 days' => 'Týden'
        ];
        $form->addSelect('period', 'Zobrazit statistiky:', $items)
            ->setHtmlId(self::periodSelectId);

        $form->onSuccess[] = function (Form $form, $values)
        {
            $this->setFromDate($values->period);
            if ($this->presenter->isAjax()) {
                $this->redrawControl();
            } else
                $this->presenter->redirect('this');
        };

        return $form;
    }

    protected function setFromDate(string $modifier): void
    {
        if(!$modifier)
            return;
        $today = $this->visitManager->today;
        $this->from = $today->modifyClone($modifier);
    }
}