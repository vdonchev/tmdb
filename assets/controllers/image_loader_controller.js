import {Controller} from "@hotwired/stimulus"

export default class extends Controller {
    connect() {
        // Critical: Check if image is already loaded (e.g. from cache)
        // if (this.element.complete) {
        //     this.reveal()
        // }

        if (this.element.complete) {
            // 1. Remove transition settings so it appears instantly
            this.element.classList.remove("transition-opacity", "duration-700", "ease-in-out")
            // 2. Reveal it
            this.reveal()
        }
    }

    reveal() {
        // You can remove the class directly
        this.element.classList.remove("opacity-0")

        // Optional: If you want to use Stimulus Classes API:
        // this.element.classList.remove(this.loadingClass)
    }
}
