// Unified icon library: Lucide (~1700 ISC) + UIkit (131) merged.
// UIkit wins on name collisions to preserve historical look-and-feel
// and pre-existing tile defaults (`dashicons-*` legacy keys aside).
import uikitIcons from './uikitIconsRaw.js';
import lucideIcons from './lucideIconsSvg.js';

const library = { ...lucideIcons, ...uikitIcons };

export default library;
export { uikitIcons, lucideIcons };
