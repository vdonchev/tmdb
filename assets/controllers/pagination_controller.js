import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["pageInput", "select"]

    next(event) {
        event.preventDefault();
        const currentPage = parseInt(this.pageInputTarget.value);
        const maxPages = parseInt(this.pageInputTarget.dataset.max);

        if (currentPage < maxPages) {
            this.pageInputTarget.value = currentPage + 1;
            this.element.requestSubmit();
        }
    }

    previous(event) {
        event.preventDefault();
        const currentPage = parseInt(this.pageInputTarget.value);

        if (currentPage > 1) {
            this.pageInputTarget.value = currentPage - 1;
            this.element.requestSubmit();
        }
    }

    // Optional: submit when dropdown changes
    change() {
        this.pageInputTarget.value = this.selectTarget.value;
        this.element.requestSubmit();
    }
}
