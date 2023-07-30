<?php

namespace Jdvorak23\Visits\Ips;


use Jdvorak23\Visits\Model\VisitManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\UniqueConstraintViolationException;

class VisitIpsControl extends Control
{
    const template = __DIR__ . '/ips.latte';
    const ipInputId = "frm-ips-ipForm-ip"; // Aby byl stejný pro javascript
    const ownCheckboxId = "frm-ips-ipForm-own"; // Aby byl stejný pro javascript

    protected bool $isFlashOnForm = false;

    public function __construct(protected VisitManager $visitManager)
    {
    }
    public function render(): void
    {
        $this->template->setFile(self::template);
        $this->template->ips = $this->visitManager->getIps();
        $this->template->isFlashOnForm = $this->isFlashOnForm;
        $this->template->render();
    }

    public function handleDeleteIp(string $ip): void
    {
        $this->visitManager->removeIp($ip);
        $this->flashMessage('IP adresa ' . $ip . ' byla odebrána', 'info');
        if ($this->presenter->isAjax()) {
            $this->redrawControl('table');
        } else
            $this->presenter->redirect('this#OwnIpCard');
    }

    protected function createComponentIpForm(): Form
    {
        $form = new Form();

        $form->addText('ip', 'IP')
            ->setValue($this->presenter->getHttpRequest()->getRemoteAddress())
            ->setHtmlAttribute('readonly')
            ->setHtmlId(self::ipInputId);
        $form->addText('note', 'Poznámka');
        $form->addCheckbox('own')
            ->setHtmlId(self::ownCheckboxId)
            ->setOmitted();
        $form->addSubmit('save', 'Přidat');

        $form->onSuccess[] = function (Form $form, $values)
        {
            try {
                $this->visitManager->addIp($values->ip, $values->note);
            }catch(UniqueConstraintViolationException){
                $this->flashMessage('IP adresa ' . $values->ip . ' je již na seznamu', 'warning');
                $this->isFlashOnForm = true;
                $form->addError("");
                return;
            }
            $this->flashMessage('IP adresa ' . $values->ip . ' byla přidána', 'success');
            $this->presenter->redirect('this#OwnIpCard');

        };
        $form->onRender[] = function (Form $form)
        {
            $id = $form->getElementPrototype()->id;
            $action = $this->link('this#' . $id);
            $form->setAction($action);
        };

        return $form;
    }
}