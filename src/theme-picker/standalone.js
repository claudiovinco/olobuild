// Entry standalone: espone il picker come global window.OloThemePicker per
// contesti non-Vue (es. il setup wizard PHP), via bundle leggero theme-picker.js.
import { createThemePicker } from './themePicker.js';

export { createThemePicker as create };
export { createThemePicker };
