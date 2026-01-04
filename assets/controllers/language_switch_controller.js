import {Controller} from "@hotwired/stimulus"
import * as Turbo from "@hotwired/turbo"

export default class extends Controller {
    static values = {
        en: String,
        bg: String,
        currentLocale: String
    }

    connect() {
        // Set the value directly from the passed value
        this.element.value = this.currentLocaleValue;
    }

    change(event) {
        const locale = event.target.value;
        const url = locale === 'en' ? this.enValue : this.bgValue;

        if (url) {
            Turbo.visit(url);
        }
    }
}
