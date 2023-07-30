# visits
Tři komponenty na zobrazení návštěvnosti
- Cards - 4 karty - celkem, rok, měsíc, 7 dní
- Pages - absolutní i relativní poměry jednotlivých stránek
- Ips - přidání IP adres k ignorování
## Instalace

```
composer require jdvorak23/visits
```

Zaregistruji v `services.neon`:
```neon
- Jdvorak23\Visits\VisitCardsFactory
- Jdvorak23\Visits\VisitPagesFactory
- Jdvorak23\Visits\VisitIpsFactory
- Jdvorak23\Visits\Model\VisitManager
```

### javascript
Potřebuje javascript. Použít script ve složce `/assets`

index.js (main):
```javascript
import '../Components/visits/src/assets/visits' // Podle toho kam se zkopírovalo
```

## Vytvoření komponent
```php
    // DI továren - konstruktor, inject, ...
    private readonly VisitCardsFactory $visitCardsFactory,
    private readonly VisitIpsFactory $visitIpsFactory,
    private readonly VisitPagesFactory $visitPagesFactory

    protected function createComponentCards(): VisitCardsControl
    {
        return $this->visitCardsFactory->create();
    }
    protected function createComponentIps(): VisitIpsControl
    {
        return $this->visitIpsFactory->create();
    }
    protected function createComponentPages(): VisitPagesControl
    {
        return $this->visitPagesFactory->create();
    }
```
A v templatě:
```latte
{control cards}
{control pages}
{control ips}
```

## Přidání přístupu
Někde v Base(Front)Presenter injectnout závislost:
```php
protected VisitManager $visitManager;
    public function injectManagers(VisitManager $visitManager): void
    {
        $this->visitManager = $visitManager;
    }
```
Někde v Base(Front)Presenter, nejlíp asi v beforeRender:
```php
    protected function beforeRender(): void
    {
        parent::beforeRender();
        $this->addVisit();
    }
    protected function addVisit(): void
    {
        $remoteAddress = $this->getHttpRequest()->getRemoteAddress();
        $page = $this->getHttpRequest()->getUrl()->getPath();
        $this->visitManager->addVisit($remoteAddress, $page);
    }
```