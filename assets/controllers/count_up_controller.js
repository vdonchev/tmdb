import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        number: Number,
        duration: { type: Number, default: 1000 }
    }

    connect() {
        this.animateValue();
    }

    animateValue() {
        const start = 0;
        const end = this.numberValue;
        const duration = this.durationValue;
        let startTimestamp = null;

        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);

            // For ratings, we want 1 decimal point (e.g. 7.5)
            const currentValue = (progress * (end - start) + start).toFixed(1);

            this.element.innerHTML = currentValue;

            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };

        window.requestAnimationFrame(step);
    }
}
