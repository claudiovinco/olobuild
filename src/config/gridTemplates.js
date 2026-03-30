/**
 * CSS Grid Layout Templates for Olobuild
 *
 * Each template defines:
 * - id: unique key
 * - name: user label
 * - category: 'columns' | 'multirow' | 'masonry'
 * - gridTemplateColumns: CSS grid-template-columns
 * - gridTemplateRows: CSS grid-template-rows
 * - cells: array of { gridColumn, gridRow } for each child
 * - preview: { cols, rows, rects } for SVG thumbnail generation
 */

// ═══════════════════════════════════════
//  STANDARD COLUMNS (single row, flex-like)
// ═══════════════════════════════════════
const columns = [
  {
    id: 'g-1',
    name: '1 colonna',
    gridTemplateColumns: '1fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1 / -1', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 4, h: 1 }] },
  },
  {
    id: 'g-2',
    name: '2 colonne uguali',
    gridTemplateColumns: '1fr 1fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 2, y: 0, w: 2, h: 1 }] },
  },
  {
    id: 'g-3',
    name: '3 colonne uguali',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }],
    preview: { cols: 6, rows: 1, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 2, y: 0, w: 2, h: 1 }, { x: 4, y: 0, w: 2, h: 1 }] },
  },
  {
    id: 'g-4',
    name: '4 colonne uguali',
    gridTemplateColumns: 'repeat(4, 1fr)',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }, { gridColumn: '4', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }] },
  },
  {
    id: 'g-5',
    name: '5 colonne uguali',
    gridTemplateColumns: 'repeat(5, 1fr)',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }, { gridColumn: '4', gridRow: '1' }, { gridColumn: '5', gridRow: '1' }],
    preview: { cols: 5, rows: 1, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 4, y: 0, w: 1, h: 1 }] },
  },
  {
    id: 'g-6',
    name: '6 colonne uguali',
    gridTemplateColumns: 'repeat(6, 1fr)',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }, { gridColumn: '4', gridRow: '1' }, { gridColumn: '5', gridRow: '1' }, { gridColumn: '6', gridRow: '1' }],
    preview: { cols: 6, rows: 1, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 4, y: 0, w: 1, h: 1 }, { x: 5, y: 0, w: 1, h: 1 }] },
  },
  {
    id: 'g-2-1',
    name: '2/3 + 1/3',
    gridTemplateColumns: '2fr 1fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }],
    preview: { cols: 3, rows: 1, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }] },
  },
  {
    id: 'g-1-2',
    name: '1/3 + 2/3',
    gridTemplateColumns: '1fr 2fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }],
    preview: { cols: 3, rows: 1, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 2, h: 1 }] },
  },
  {
    id: 'g-1-2-1',
    name: '1/4 + 1/2 + 1/4',
    gridTemplateColumns: '1fr 2fr 1fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 2, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }] },
  },
  {
    id: 'g-3-1',
    name: '3/4 + 1/4',
    gridTemplateColumns: '3fr 1fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }] },
  },
  {
    id: 'g-1-3',
    name: '1/4 + 3/4',
    gridTemplateColumns: '1fr 3fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 3, h: 1 }] },
  },
  {
    id: 'g-1-1-2',
    name: '1/4 + 1/4 + 1/2',
    gridTemplateColumns: '1fr 1fr 2fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 2, h: 1 }] },
  },
  {
    id: 'g-2-1-1',
    name: '1/2 + 1/4 + 1/4',
    gridTemplateColumns: '2fr 1fr 1fr',
    gridTemplateRows: 'auto',
    cells: [{ gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }],
    preview: { cols: 4, rows: 1, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }] },
  },
];

// ═══════════════════════════════════════
//  MULTI-ROW GRIDS
// ═══════════════════════════════════════
const multirow = [
  // 2x2
  {
    id: 'mg-2x2',
    name: '2×2 Griglia',
    gridTemplateColumns: '1fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' },
    ],
    preview: { cols: 2, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }] },
  },
  // 3x2
  {
    id: 'mg-3x2',
    name: '3×2 Griglia',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 3, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }] },
  },
  // 4x2
  {
    id: 'mg-4x2',
    name: '4×2 Griglia',
    gridTemplateColumns: 'repeat(4, 1fr)',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' }, { gridColumn: '4', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' }, { gridColumn: '4', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 3, y: 1, w: 1, h: 1 }] },
  },
  // 2x3
  {
    id: 'mg-2x3',
    name: '2×3 Griglia',
    gridTemplateColumns: '1fr 1fr',
    gridTemplateRows: 'auto auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' },
      { gridColumn: '1', gridRow: '3' }, { gridColumn: '2', gridRow: '3' },
    ],
    preview: { cols: 2, rows: 3, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 0, y: 2, w: 1, h: 1 }, { x: 1, y: 2, w: 1, h: 1 }] },
  },
  // 3x3
  {
    id: 'mg-3x3',
    name: '3×3 Griglia',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: 'auto auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
      { gridColumn: '1', gridRow: '3' }, { gridColumn: '2', gridRow: '3' }, { gridColumn: '3', gridRow: '3' },
    ],
    preview: { cols: 3, rows: 3, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 0, y: 2, w: 1, h: 1 }, { x: 1, y: 2, w: 1, h: 1 }, { x: 2, y: 2, w: 1, h: 1 }] },
  },
  // Full width + 2 cols
  {
    id: 'mg-full-2',
    name: 'Full + 2 colonne',
    gridTemplateColumns: '1fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' },
    ],
    preview: { cols: 2, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }] },
  },
  // Full width + 3 cols
  {
    id: 'mg-full-3',
    name: 'Full + 3 colonne',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 3, rows: 2, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }] },
  },
  // Full width + 4 cols
  {
    id: 'mg-full-4',
    name: 'Full + 4 colonne',
    gridTemplateColumns: 'repeat(4, 1fr)',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' }, { gridColumn: '4', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 4, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 3, y: 1, w: 1, h: 1 }] },
  },
  // 2 cols + full width
  {
    id: 'mg-2-full',
    name: '2 colonne + Full',
    gridTemplateColumns: '1fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' },
      { gridColumn: '1 / -1', gridRow: '2' },
    ],
    preview: { cols: 2, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 2, h: 1 }] },
  },
  // 3 cols + full width
  {
    id: 'mg-3-full',
    name: '3 colonne + Full',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '1 / -1', gridRow: '2' },
    ],
    preview: { cols: 3, rows: 2, rects: [{ x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 0, y: 0, w: 3, h: 1 }] },
  },
  // Sidebar left + content
  {
    id: 'mg-sidebar-l',
    name: 'Sidebar sx + Contenuto',
    gridTemplateColumns: '1fr 3fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' }, { gridColumn: '2', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 3, h: 1 }, { x: 1, y: 1, w: 3, h: 1 }] },
  },
  // Content + sidebar right
  {
    id: 'mg-sidebar-r',
    name: 'Contenuto + Sidebar dx',
    gridTemplateColumns: '3fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '1', gridRow: '2' },
      { gridColumn: '2', gridRow: '1 / 3' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 0, y: 1, w: 3, h: 1 }, { x: 3, y: 0, w: 1, h: 2 }] },
  },
  // Sidebar + 2x2
  {
    id: 'mg-sidebar-2x2',
    name: 'Sidebar + 2×2',
    gridTemplateColumns: '1fr 2fr 2fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 2, h: 1 }, { x: 3, y: 0, w: 2, h: 1 }, { x: 1, y: 1, w: 2, h: 1 }, { x: 3, y: 1, w: 2, h: 1 }] },
  },
  // Holy grail: sidebar + content + sidebar
  {
    id: 'mg-holy-grail',
    name: 'Sidebar + Content + Sidebar',
    gridTemplateColumns: '1fr 3fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' }, { gridColumn: '2', gridRow: '2' },
      { gridColumn: '3', gridRow: '1 / 3' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 3, h: 1 }, { x: 1, y: 1, w: 3, h: 1 }, { x: 4, y: 0, w: 1, h: 2 }] },
  },
];

// ═══════════════════════════════════════
//  MASONRY / ASYMMETRIC
// ═══════════════════════════════════════
const masonry = [
  // Big left + 2 small right
  {
    id: 'ms-big-l',
    name: 'Grande sx + 2 piccole',
    gridTemplateColumns: '2fr 1fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' }, { gridColumn: '2', gridRow: '2' },
    ],
    preview: { cols: 3, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 2 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }] },
  },
  // 2 small left + big right
  {
    id: 'ms-big-r',
    name: '2 piccole + Grande dx',
    gridTemplateColumns: '1fr 2fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '1', gridRow: '2' },
      { gridColumn: '2', gridRow: '1 / 3' },
    ],
    preview: { cols: 3, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 0, w: 2, h: 2 }] },
  },
  // Big top + 3 small bottom
  {
    id: 'ms-big-top',
    name: 'Grande sopra + 3 sotto',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: '2fr 1fr',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 3, rows: 3, rects: [{ x: 0, y: 0, w: 3, h: 2 }, { x: 0, y: 2, w: 1, h: 1 }, { x: 1, y: 2, w: 1, h: 1 }, { x: 2, y: 2, w: 1, h: 1 }] },
  },
  // 3 small top + big bottom
  {
    id: 'ms-big-bot',
    name: '3 sopra + Grande sotto',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: '1fr 2fr',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '1 / -1', gridRow: '2' },
    ],
    preview: { cols: 3, rows: 3, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 3, h: 2 }] },
  },
  // L-shape: big left-top + 2 small right + 2 small bottom
  {
    id: 'ms-l-shape',
    name: 'L-shape',
    gridTemplateColumns: '2fr 1fr 1fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '2 / 4', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 2 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 2, y: 1, w: 2, h: 1 }] },
  },
  // Reverse L-shape
  {
    id: 'ms-l-reverse',
    name: 'L-shape inversa',
    gridTemplateColumns: '1fr 1fr 2fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' },
      { gridColumn: '1 / 3', gridRow: '2' },
      { gridColumn: '3', gridRow: '1 / 3' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 2, h: 1 }, { x: 2, y: 0, w: 2, h: 2 }] },
  },
  // T-shape: wide top + 3 below
  {
    id: 'ms-t-shape',
    name: 'T-shape',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: '1fr 1fr 1fr',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2 / 4' },
      { gridColumn: '2', gridRow: '2 / 4' },
      { gridColumn: '3', gridRow: '2 / 4' },
    ],
    preview: { cols: 3, rows: 3, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 0, y: 1, w: 1, h: 2 }, { x: 1, y: 1, w: 1, h: 2 }, { x: 2, y: 1, w: 1, h: 2 }] },
  },
  // Magazine: big left + 4 small grid right (2x2)
  {
    id: 'ms-magazine',
    name: 'Magazine',
    gridTemplateColumns: '1fr 1fr 1fr 1fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1 / 3', gridRow: '1 / 3' },
      { gridColumn: '3', gridRow: '1' }, { gridColumn: '4', gridRow: '1' },
      { gridColumn: '3', gridRow: '2' }, { gridColumn: '4', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 2 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 3, y: 1, w: 1, h: 1 }] },
  },
  // Gallery: 2 big + 4 small
  {
    id: 'ms-gallery',
    name: 'Gallery',
    gridTemplateColumns: '1fr 1fr 1fr 1fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1 / 3', gridRow: '1' }, { gridColumn: '3 / 5', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' }, { gridColumn: '4', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 2, y: 0, w: 2, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 3, y: 1, w: 1, h: 1 }] },
  },
  // Portfolio: big center, small corners
  {
    id: 'ms-portfolio',
    name: 'Portfolio',
    gridTemplateColumns: '1fr 2fr 1fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1' },
      { gridColumn: '2', gridRow: '1 / 3' },
      { gridColumn: '3', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' },
      { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 2, h: 2 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 3, y: 1, w: 1, h: 1 }] },
  },
  // Mosaic: 1 big + 1 medium + 2 small
  {
    id: 'ms-mosaic',
    name: 'Mosaico',
    gridTemplateColumns: '2fr 1fr 1fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2 / 4', gridRow: '1' },
      { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 2 }, { x: 2, y: 0, w: 2, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 3, y: 1, w: 1, h: 1 }] },
  },
  // Mosaic reverse: medium + 2 small + big
  {
    id: 'ms-mosaic-r',
    name: 'Mosaico inverso',
    gridTemplateColumns: '1fr 1fr 2fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1 / 3', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' },
      { gridColumn: '3', gridRow: '1 / 3' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 0, w: 2, h: 2 }] },
  },
  // Z-shape: wide top-left, small top-right, small bottom-left, wide bottom-right
  {
    id: 'ms-z-shape',
    name: 'Z-shape',
    gridTemplateColumns: '2fr 1fr 1fr 2fr',
    gridTemplateRows: '1fr 1fr',
    cells: [
      { gridColumn: '1 / 3', gridRow: '1' }, { gridColumn: '3 / 5', gridRow: '1' },
      { gridColumn: '1 / 3', gridRow: '2' }, { gridColumn: '3 / 5', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 1, y: 1, w: 3, h: 1 }] },
  },
  // Cross: center big, 4 small corners
  {
    id: 'ms-cross',
    name: 'Croce',
    gridTemplateColumns: '1fr 2fr 1fr',
    gridTemplateRows: '1fr 2fr 1fr',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' },
      { gridColumn: '2', gridRow: '2' },
      { gridColumn: '3', gridRow: '2' },
      { gridColumn: '1 / -1', gridRow: '3' },
    ],
    preview: { cols: 4, rows: 4, rects: [{ x: 0, y: 0, w: 4, h: 1 }, { x: 0, y: 1, w: 1, h: 2 }, { x: 1, y: 1, w: 2, h: 2 }, { x: 3, y: 1, w: 1, h: 2 }, { x: 0, y: 3, w: 4, h: 1 }] },
  },
  // 2 big top + 2 medium + 2 small bottom
  {
    id: 'ms-pyramid',
    name: 'Piramide',
    gridTemplateColumns: '1fr 1fr 1fr 1fr',
    gridTemplateRows: '1fr 1fr 1fr',
    cells: [
      { gridColumn: '1 / 3', gridRow: '1' }, { gridColumn: '3 / 5', gridRow: '1' },
      { gridColumn: '1 / 3', gridRow: '2' }, { gridColumn: '3 / 5', gridRow: '2' },
      { gridColumn: '1', gridRow: '3' }, { gridColumn: '2', gridRow: '3' }, { gridColumn: '3', gridRow: '3' }, { gridColumn: '4', gridRow: '3' },
    ],
    preview: { cols: 4, rows: 3, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 2, y: 0, w: 2, h: 1 }, { x: 0, y: 1, w: 2, h: 1 }, { x: 2, y: 1, w: 2, h: 1 }, { x: 0, y: 2, w: 1, h: 1 }, { x: 1, y: 2, w: 1, h: 1 }, { x: 2, y: 2, w: 1, h: 1 }, { x: 3, y: 2, w: 1, h: 1 }] },
  },
  // Feature: big top + 2 medium bottom
  {
    id: 'ms-feature',
    name: 'Feature',
    gridTemplateColumns: '1fr 1fr',
    gridTemplateRows: '2fr 1fr',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' },
    ],
    preview: { cols: 2, rows: 3, rects: [{ x: 0, y: 0, w: 2, h: 2 }, { x: 0, y: 2, w: 1, h: 1 }, { x: 1, y: 2, w: 1, h: 1 }] },
  },
  // Inverted feature: 2 top + big bottom
  {
    id: 'ms-feature-inv',
    name: 'Feature inversa',
    gridTemplateColumns: '1fr 1fr',
    gridTemplateRows: '1fr 2fr',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' },
      { gridColumn: '1 / -1', gridRow: '2' },
    ],
    preview: { cols: 2, rows: 3, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 2, h: 2 }] },
  },
  // Bento: big + 3 small stacked right
  {
    id: 'ms-bento',
    name: 'Bento',
    gridTemplateColumns: '2fr 1fr',
    gridTemplateRows: '1fr 1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1 / 4' },
      { gridColumn: '2', gridRow: '1' }, { gridColumn: '2', gridRow: '2' }, { gridColumn: '2', gridRow: '3' },
    ],
    preview: { cols: 3, rows: 3, rects: [{ x: 0, y: 0, w: 2, h: 3 }, { x: 2, y: 0, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 2, y: 2, w: 1, h: 1 }] },
  },
  // Bento reverse: 3 small stacked left + big
  {
    id: 'ms-bento-r',
    name: 'Bento inverso',
    gridTemplateColumns: '1fr 2fr',
    gridTemplateRows: '1fr 1fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '1', gridRow: '2' }, { gridColumn: '1', gridRow: '3' },
      { gridColumn: '2', gridRow: '1 / 4' },
    ],
    preview: { cols: 3, rows: 3, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 1, h: 1 }, { x: 0, y: 2, w: 1, h: 1 }, { x: 1, y: 0, w: 2, h: 3 }] },
  },
  // Dashboard: header + sidebar + 2x2 content
  {
    id: 'ms-dashboard',
    name: 'Dashboard',
    gridTemplateColumns: '1fr 1fr 1fr',
    gridTemplateRows: '1fr 1fr 1fr',
    cells: [
      { gridColumn: '1 / -1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2 / 4' },
      { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
      { gridColumn: '2', gridRow: '3' }, { gridColumn: '3', gridRow: '3' },
    ],
    preview: { cols: 3, rows: 3, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 0, y: 1, w: 1, h: 2 }, { x: 1, y: 1, w: 1, h: 1 }, { x: 2, y: 1, w: 1, h: 1 }, { x: 1, y: 2, w: 1, h: 1 }, { x: 2, y: 2, w: 1, h: 1 }] },
  },
  // Showcase: big center + 2 small top + 2 small bottom
  {
    id: 'ms-showcase',
    name: 'Showcase',
    gridTemplateColumns: '1fr 2fr 1fr',
    gridTemplateRows: '1fr 2fr 1fr',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '1 / -1', gridRow: '2' },
      { gridColumn: '1', gridRow: '3' }, { gridColumn: '2', gridRow: '3' }, { gridColumn: '3', gridRow: '3' },
    ],
    preview: { cols: 4, rows: 4, rects: [{ x: 0, y: 0, w: 1, h: 1 }, { x: 1, y: 0, w: 2, h: 1 }, { x: 3, y: 0, w: 1, h: 1 }, { x: 0, y: 1, w: 4, h: 2 }, { x: 0, y: 3, w: 1, h: 1 }, { x: 1, y: 3, w: 2, h: 1 }, { x: 3, y: 3, w: 1, h: 1 }] },
  },
];

// ═══════════════════════════════════════
//  SIDEBAR LAYOUTS
// ═══════════════════════════════════════
const sidebar = [
  // Sidebar left + 1 content
  {
    id: 'sb-l-1',
    name: 'Sidebar sx + 1',
    gridTemplateColumns: '1fr 3fr',
    gridTemplateRows: 'auto',
    cells: [
      { gridColumn: '1', gridRow: '1' },
      { gridColumn: '2', gridRow: '1' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 3, h: 2 }] },
  },
  // Sidebar right + 1 content
  {
    id: 'sb-r-1',
    name: '1 + Sidebar dx',
    gridTemplateColumns: '3fr 1fr',
    gridTemplateRows: 'auto',
    cells: [
      { gridColumn: '1', gridRow: '1' },
      { gridColumn: '2', gridRow: '1' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 3, h: 2 }, { x: 3, y: 0, w: 1, h: 2 }] },
  },
  // Sidebar left + 2 stacked
  {
    id: 'sb-l-2',
    name: 'Sidebar sx + 2 contenuti',
    gridTemplateColumns: '1fr 3fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' },
      { gridColumn: '2', gridRow: '2' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 3, h: 1 }, { x: 1, y: 1, w: 3, h: 1 }] },
  },
  // Sidebar right + 2 stacked
  {
    id: 'sb-r-2',
    name: '2 contenuti + Sidebar dx',
    gridTemplateColumns: '3fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' },
      { gridColumn: '2', gridRow: '1 / 3' },
    ],
    preview: { cols: 4, rows: 2, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 0, y: 1, w: 3, h: 1 }, { x: 3, y: 0, w: 1, h: 2 }] },
  },
  // Sidebar left + 3 stacked
  {
    id: 'sb-l-3',
    name: 'Sidebar sx + 3 contenuti',
    gridTemplateColumns: '1fr 3fr',
    gridTemplateRows: 'auto auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 4' },
      { gridColumn: '2', gridRow: '1' },
      { gridColumn: '2', gridRow: '2' },
      { gridColumn: '2', gridRow: '3' },
    ],
    preview: { cols: 4, rows: 3, rects: [{ x: 0, y: 0, w: 1, h: 3 }, { x: 1, y: 0, w: 3, h: 1 }, { x: 1, y: 1, w: 3, h: 1 }, { x: 1, y: 2, w: 3, h: 1 }] },
  },
  // Sidebar right + 3 stacked
  {
    id: 'sb-r-3',
    name: '3 contenuti + Sidebar dx',
    gridTemplateColumns: '3fr 1fr',
    gridTemplateRows: 'auto auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' },
      { gridColumn: '1', gridRow: '3' },
      { gridColumn: '2', gridRow: '1 / 4' },
    ],
    preview: { cols: 4, rows: 3, rects: [{ x: 0, y: 0, w: 3, h: 1 }, { x: 0, y: 1, w: 3, h: 1 }, { x: 0, y: 2, w: 3, h: 1 }, { x: 3, y: 0, w: 1, h: 3 }] },
  },
  // Sidebar left + header + 2 content
  {
    id: 'sb-l-header-2',
    name: 'Sidebar + Header + 2 col',
    gridTemplateColumns: '1fr 2fr 2fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2 / 4', gridRow: '1' },
      { gridColumn: '2', gridRow: '2' },
      { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 4, h: 1 }, { x: 1, y: 1, w: 2, h: 1 }, { x: 3, y: 1, w: 2, h: 1 }] },
  },
  // Sidebar right + header + 2 content
  {
    id: 'sb-r-header-2',
    name: 'Header + 2 col + Sidebar',
    gridTemplateColumns: '2fr 2fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1 / 3', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' },
      { gridColumn: '2', gridRow: '2' },
      { gridColumn: '3', gridRow: '1 / 3' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 4, h: 1 }, { x: 0, y: 1, w: 2, h: 1 }, { x: 2, y: 1, w: 2, h: 1 }, { x: 4, y: 0, w: 1, h: 2 }] },
  },
  // Double sidebar
  {
    id: 'sb-double',
    name: 'Doppia sidebar',
    gridTemplateColumns: '1fr 3fr 1fr',
    gridTemplateRows: 'auto',
    cells: [
      { gridColumn: '1', gridRow: '1' },
      { gridColumn: '2', gridRow: '1' },
      { gridColumn: '3', gridRow: '1' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 3, h: 2 }, { x: 4, y: 0, w: 1, h: 2 }] },
  },
  // Double sidebar + 2 content rows
  {
    id: 'sb-double-2',
    name: 'Doppia sidebar + 2 righe',
    gridTemplateColumns: '1fr 3fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' },
      { gridColumn: '2', gridRow: '2' },
      { gridColumn: '3', gridRow: '1 / 3' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 3, h: 1 }, { x: 1, y: 1, w: 3, h: 1 }, { x: 4, y: 0, w: 1, h: 2 }] },
  },
  // Sidebar left + 2x2 grid
  {
    id: 'sb-l-2x2',
    name: 'Sidebar + griglia 2×2',
    gridTemplateColumns: '1fr 2fr 2fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1 / 3' },
      { gridColumn: '2', gridRow: '1' }, { gridColumn: '3', gridRow: '1' },
      { gridColumn: '2', gridRow: '2' }, { gridColumn: '3', gridRow: '2' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 1, h: 2 }, { x: 1, y: 0, w: 2, h: 1 }, { x: 3, y: 0, w: 2, h: 1 }, { x: 1, y: 1, w: 2, h: 1 }, { x: 3, y: 1, w: 2, h: 1 }] },
  },
  // 2x2 grid + sidebar right
  {
    id: 'sb-r-2x2',
    name: 'Griglia 2×2 + Sidebar',
    gridTemplateColumns: '2fr 2fr 1fr',
    gridTemplateRows: 'auto auto',
    cells: [
      { gridColumn: '1', gridRow: '1' }, { gridColumn: '2', gridRow: '1' },
      { gridColumn: '1', gridRow: '2' }, { gridColumn: '2', gridRow: '2' },
      { gridColumn: '3', gridRow: '1 / 3' },
    ],
    preview: { cols: 5, rows: 2, rects: [{ x: 0, y: 0, w: 2, h: 1 }, { x: 2, y: 0, w: 2, h: 1 }, { x: 0, y: 1, w: 2, h: 1 }, { x: 2, y: 1, w: 2, h: 1 }, { x: 4, y: 0, w: 1, h: 2 }] },
  },
];

// Collect all templates
const ALL_TEMPLATES = [
  ...columns.map(t => ({ ...t, category: 'columns' })),
  ...multirow.map(t => ({ ...t, category: 'multirow' })),
  ...masonry.map(t => ({ ...t, category: 'masonry' })),
  ...sidebar.map(t => ({ ...t, category: 'sidebar' })),
];

// Quick lookup by id
const TEMPLATES_MAP = {};
ALL_TEMPLATES.forEach(t => { TEMPLATES_MAP[t.id] = t; });

export { ALL_TEMPLATES, TEMPLATES_MAP, columns, multirow, masonry, sidebar };
export default ALL_TEMPLATES;
