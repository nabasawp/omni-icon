import type { OmniIconRenderer } from './OmniIconRenderer';

/**
 * Global OmniIcon Observer
 * 
 * This module manages lazy-loading of the OmniIconRenderer and tracks which
 * omni-icon elements have been registered for rendering.
 */

// Singleton renderer instance (lazy-loaded)
let renderer: OmniIconRenderer | null = null;

// Promise for lazy-loading the renderer module
let rendererPromise: Promise<OmniIconRenderer> | null = null;

// Track which elements have been seen to avoid duplicate processing
// Using WeakMap instead of WeakSet to support deletion via clearSeenElement
const seenElements = new WeakMap<Element, boolean>();

/**
 * Lazy-loads the OmniIconRenderer module
 * Returns cached promise if already loading/loaded
 */
function loadRenderer(): Promise<OmniIconRenderer> {
    if (!rendererPromise) {
        rendererPromise = import('./OmniIconRenderer').then((module) => {
            renderer = new module.OmniIconRenderer();
            return renderer;
        });
    }
    return rendererPromise;
}

function ensureRenderer(el: Element) {
    if (!(el.tagName === 'OMNI-ICON')) return;

    // Already processed
    if (seenElements.has(el)) return;

    seenElements.set(el, true);

    // skip if pre-rendered
    if ((el as any).hasAttribute('data-prerendered')) {
        return;
    }

    loadRenderer().then((r) => r.attachRenderer(el));
}

function processNode(node: Node) {
    // DocumentFragment (framework inserts)
    if (node.nodeType === Node.DOCUMENT_FRAGMENT_NODE) {
        node.childNodes.forEach(processNode);
        return;
    }

    if (node.nodeType !== Node.ELEMENT_NODE) return;

    const targets =
        (node as Element).matches('omni-icon')
            ? [(node as Element)]
            : Array.from((node as Element).querySelectorAll('omni-icon'));

    targets.forEach((el) => ensureRenderer(el));
}

/**
 * Initial scan of existing omni-icon elements in the DOM
 */
processNode(document.body);

/**
 * Global MutationObserver
 * Watches for new omni-icon elements added to the DOM
 * Note: Attribute changes are handled by per-element observers in OmniIconRenderer
 */
const observer = new MutationObserver((mutations) => {
    for (const { type, addedNodes } of mutations) {
        if (type === 'childList') {
            addedNodes.forEach(processNode);
        }
    }
});


// Start observing the document
observer.observe(document.body, {
    childList: true,
    subtree: true,
});

/**
 * Get the current renderer instance (may be null if not yet loaded)
 */
export const getRenderer = (): OmniIconRenderer | null => renderer;

/**
 * Mark element as not seen so it can be re-attached if added back to DOM
 * Called when element is disconnected to allow re-initialization on reconnect
 */
export const clearSeenElement = (el: Element): void => {
    seenElements.delete(el);
};
