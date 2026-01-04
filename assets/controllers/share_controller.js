// assets/controllers/share_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        title: String,
        text: String,
        url: String
    }

    async share(event) {
        event.preventDefault();

        const shareData = {
            title: this.titleValue || document.title,
            text: this.textValue,
            url: this.urlValue || window.location.href
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
            } else {
                // Fallback: Copy to clipboard or show a modal
                this.copyToClipboard(shareData.url);
            }
        } catch (err) {
            console.error('Error sharing:', err);
        }
    }

    copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Link copied to clipboard!');
    }
}
