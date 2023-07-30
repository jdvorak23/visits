// Shadow dependency - Bootstrap 5, Font Awesome css
// External
import naja from 'naja'; // naja musí být inicializována, pravděpodobně v index(main).js
import defaults from "defaults";
// Internal
import onDomReady from "../../../../../app/js/internal/onDomReady";
import confirmModal from "../../../../../app/js/internal/confirmModal";

const defaultOptions = {
    selectPeriodId: 'frm-pages-pagesForm-period',
    ipInputId: 'frm-ips-ipForm-ip',
    ownCheckboxId: 'frm-ips-ipForm-own'
}

class Visits{
    ipInput;
    ownCheckbox;
    constructor(options = {}) {
        this.options = defaults(options, defaultOptions);
        confirmModal.initialize(); // TODO maybe edit
        this._setPages();
        this._setIps();
    }
    _setPages(){
        const selectBox = document.getElementById(this.options.selectPeriodId);
        if(!selectBox || !(selectBox instanceof HTMLSelectElement))
            return;
        selectBox.addEventListener('change', (event) => {
            naja.uiHandler.submitForm(event.target.form);
        });
    }
    _setIps(){
        this.ipInput = document.getElementById(this.options.ipInputId);
        if(!this.ipInput || !(this.ipInput instanceof HTMLInputElement))
            return;
        this.ownCheckbox = document.getElementById(this.options.ownCheckboxId);
        if(!this.ownCheckbox || !(this.ownCheckbox instanceof HTMLInputElement) || this.ownCheckbox.type !== 'checkbox')
            return;
        this.ownCheckbox.addEventListener('change', () => {
            this._toggleIpInputReadOnly();
        });
        this._toggleIpInputReadOnly();
    }
    _toggleIpInputReadOnly(){
        this.ownCheckbox.checked
            ? this.ipInput.removeAttribute('readonly')
            : this.ipInput.setAttribute('readonly', '');
    }
}

onDomReady(() => {
    const visits = new Visits();
});
