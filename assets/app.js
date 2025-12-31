import './stimulus_bootstrap.js';

import '@fontsource-variable/inter/index.css'
import {initFlowbite} from 'flowbite'

import './styles/app.css';

import { shouldPerformTransition, performTransition } from "turbo-view-transitions";

document.addEventListener('turbo:load', () => {
    initFlowbite();
});

document.addEventListener("turbo:before-render", (event) => {
    if (shouldPerformTransition()) {
        event.preventDefault();

        performTransition(document.body, event.detail.newBody, async () => {
            await event.detail.resume();
        });
    }
});

document.addEventListener("turbo:load", () => {
    // View Transitions don't play nicely with Turbo cache
    if (shouldPerformTransition()) Turbo.cache.exemptPageFromCache();
});

// --- ADD THIS FOR FRAMES ---
document.addEventListener("turbo:before-frame-render", (event) => {
    // 1. Check if View Transitions are supported
    if (!document.startViewTransition) return;

    // 2. Optional: Check if we actually want to animate this specific frame
    // You can remove this 'if' to animate ALL frames, or keep it to control
    // transitions via a data attribute like <turbo-frame data-transition="true">
    // if (!shouldPerformTransition()) return;

    // 3. Pause the normal Turbo render
    event.preventDefault();

    // 4. Wrap the render in a transition
    document.startViewTransition(async () => {
        // This function applies the new HTML to the page
        await event.detail.resume();
    });
});
