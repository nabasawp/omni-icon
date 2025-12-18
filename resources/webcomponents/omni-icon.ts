import './omni-icon.scss';
import { getRenderer, clearSeenElement } from './OmniIconObserver';

class OmniIcon extends HTMLElement {
	disconnectedCallback(): void {
		getRenderer()?.detachRenderer(this);
		clearSeenElement(this);
	}

	restartAnimation(): void {
		getRenderer()?.restartAnimation(this);
	}
}

if ('customElements' in window) {
	customElements.define('omni-icon', OmniIcon);
}

export default OmniIcon;
