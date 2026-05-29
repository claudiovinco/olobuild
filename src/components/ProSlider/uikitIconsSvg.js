// Compatibility re-export: 14+ tile components historically imported this
// file expecting just the UIkit set. From v1.0.85 the icon library is the
// unified Lucide+UIkit merge (see ./iconsLibrary.js). The raw UIkit-only
// dictionary lives in ./uikitIconsRaw.js if ever needed in isolation.
export { default } from './iconsLibrary.js';
