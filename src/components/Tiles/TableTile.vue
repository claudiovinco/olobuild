<template>
  <div class="olo-table-editor" :class="{ 'olo-table-compact': s.compact }">
    <!-- Toolbar colonne -->
    <div class="olo-te-col-bar" :style="colBarGrid">
      <div v-for="(_, ci) in colCount" :key="'ch-' + ci" class="olo-te-col-head">
        <button v-if="ci > 0" class="olo-te-align-btn" :title="alignLabel(ci)"
          @click.stop="cycleAlign(ci)">
          <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
            <template v-if="getAlign(ci) === 'center'">
              <rect x="3" y="2" width="10" height="2" rx="1"/>
              <rect x="1" y="6" width="14" height="2" rx="1"/>
              <rect x="3" y="10" width="10" height="2" rx="1"/>
              <rect x="5" y="14" width="6" height="1" rx=".5"/>
            </template>
            <template v-else-if="getAlign(ci) === 'right'">
              <rect x="4" y="2" width="11" height="2" rx="1"/>
              <rect x="1" y="6" width="14" height="2" rx="1"/>
              <rect x="4" y="10" width="11" height="2" rx="1"/>
              <rect x="8" y="14" width="7" height="1" rx=".5"/>
            </template>
            <template v-else>
              <rect x="1" y="2" width="11" height="2" rx="1"/>
              <rect x="1" y="6" width="14" height="2" rx="1"/>
              <rect x="1" y="10" width="11" height="2" rx="1"/>
              <rect x="1" y="14" width="7" height="1" rx=".5"/>
            </template>
          </svg>
        </button>
        <button v-if="ci === 0" class="olo-te-mini-btn olo-te-add-col" :title="t('Aggiungi colonna')" @click.stop="addCol">+</button>
        <button v-if="colCount > 1 && ci === colCount - 1" class="olo-te-mini-btn olo-te-del-col" :title="t('Rimuovi ultima colonna')" @click.stop="removeCol">−</button>
      </div>
    </div>

    <!-- Tabella -->
    <div class="olo-te-scroll" :style="{ overflowX: 'auto' }">
      <table class="olo-te-table" :style="tableStyle">
        <!-- Header -->
        <thead v-if="s.has_header && tableData.length > 0">
          <tr :style="headerRowStyle">
            <th v-for="(cell, ci) in tableData[0]" :key="'h-' + ci"
              class="olo-te-cell olo-te-th"
              :style="cellStyle(0, ci)"
              @dblclick.stop="startEdit(0, ci, $event)">
              <input v-if="editingCell === '0-' + ci"
                ref="cellInput"
                class="olo-te-input"
                :value="cell"
                @input="onCellInput(0, ci, $event)"
                @blur="stopEdit"
                @keydown.enter.prevent="stopEdit"
                @keydown.tab.prevent="tabNext(0, ci, $event)"
                @keydown.escape.prevent="stopEdit" />
              <span v-else class="olo-te-text">{{ cell || '\u00A0' }}</span>
            </th>
          </tr>
        </thead>
        <!-- Body -->
        <tbody>
          <tr v-for="(row, ri) in bodyRows" :key="'r-' + ri"
            :style="bodyRowStyle(ri)"
            :class="{ 'olo-te-hover': s.hover_effect }">
            <td v-for="(cell, ci) in row" :key="'c-' + ri + '-' + ci"
              class="olo-te-cell"
              :class="{ 'olo-te-bold': ci === 0 && s.first_col_bold }"
              :style="cellStyle(actualRowIndex(ri), ci)"
              @dblclick.stop="startEdit(actualRowIndex(ri), ci, $event)">
              <input v-if="editingCell === actualRowIndex(ri) + '-' + ci"
                ref="cellInput"
                class="olo-te-input"
                :value="cell"
                @input="onCellInput(actualRowIndex(ri), ci, $event)"
                @blur="stopEdit"
                @keydown.enter.prevent="tabDown(actualRowIndex(ri), ci)"
                @keydown.tab.prevent="tabNext(actualRowIndex(ri), ci, $event)"
                @keydown.escape.prevent="stopEdit" />
              <span v-else class="olo-te-text">{{ cell || '\u00A0' }}</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Toolbar righe -->
    <div class="olo-te-row-bar">
      <button class="olo-te-mini-btn olo-te-add-row" :title="t('Aggiungi riga')" @click.stop="addRow">+ {{ t('Riga') }}</button>
      <button v-if="tableData.length > 1" class="olo-te-mini-btn olo-te-del-row" :title="t('Rimuovi ultima riga')" @click.stop="removeRow">− {{ t('Riga') }}</button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, nextTick } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tilesStore = useTilesStore();

const defaults = {
  table_data: [
    ['Funzionalità', 'Base', 'Pro'],
    ['Spazio', '5 GB', '50 GB'],
    ['Utenti', '1', '10'],
    ['Supporto', 'Email', 'Prioritario'],
  ],
  has_header: true,
  striped: true,
  bordered: true,
  hover_effect: true,
  compact: false,
  first_col_bold: false,
  col_alignments: [],
  responsive_mode: 'scroll',
  header_bg: '',
  header_text_color: '',
  text_color: '',
  border_color: '',
  even_row_bg: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

/* -- Backward compat: parse string format -- */
function parseData(raw) {
  if (Array.isArray(raw)) return raw.map(r => [...r]);
  if (typeof raw === 'string' && raw.trim()) {
    return raw.split('\n').map(l => l.trim()).filter(Boolean).map(l => l.split('|').map(c => c.trim()));
  }
  return [['', '', ''], ['', '', '']];
}

const tableData = computed(() => parseData(s.value.table_data));
const colCount = computed(() => tableData.value[0]?.length || 1);
const bodyRows = computed(() => s.value.has_header ? tableData.value.slice(1) : tableData.value);
const actualRowIndex = (bodyIdx) => s.value.has_header ? bodyIdx + 1 : bodyIdx;

/* -- Editing -- */
const editingCell = ref(null);
const cellInput = ref(null);

function startEdit(ri, ci, ev) {
  editingCell.value = ri + '-' + ci;
  nextTick(() => {
    const inputs = document.querySelectorAll('.olo-te-input');
    const input = inputs[inputs.length - 1];
    if (input) { input.focus(); input.select(); }
  });
}

function stopEdit() {
  editingCell.value = null;
}

function onCellInput(ri, ci, ev) {
  const newData = parseData(s.value.table_data);
  if (newData[ri]) {
    newData[ri][ci] = ev.target.value;
    tilesStore.updateTile(props.tileId, { table_data: newData });
  }
}

function tabNext(ri, ci, ev) {
  const nc = colCount.value;
  const nr = tableData.value.length;
  let nextCi = ci + 1;
  let nextRi = ri;
  if (nextCi >= nc) { nextCi = 0; nextRi++; }
  if (nextRi >= nr) { nextRi = 0; }
  stopEdit();
  nextTick(() => startEdit(nextRi, nextCi));
}

function tabDown(ri, ci) {
  const nr = tableData.value.length;
  let nextRi = ri + 1;
  if (nextRi >= nr) nextRi = 0;
  stopEdit();
  nextTick(() => startEdit(nextRi, ci));
}

/* -- Add/remove rows & cols -- */
function addRow() {
  const newData = parseData(s.value.table_data);
  const nc = colCount.value;
  newData.push(new Array(nc).fill(''));
  tilesStore.updateTile(props.tileId, { table_data: newData });
}

function removeRow() {
  const newData = parseData(s.value.table_data);
  if (newData.length > 1) {
    newData.pop();
    tilesStore.updateTile(props.tileId, { table_data: newData });
  }
}

function addCol() {
  const newData = parseData(s.value.table_data);
  newData.forEach(row => row.push(''));
  tilesStore.updateTile(props.tileId, { table_data: newData });
}

function removeCol() {
  const newData = parseData(s.value.table_data);
  if (newData[0]?.length > 1) {
    newData.forEach(row => row.pop());
    tilesStore.updateTile(props.tileId, { table_data: newData });
  }
}

/* -- Column alignment -- */
function getAlign(ci) {
  return (s.value.col_alignments && s.value.col_alignments[ci]) || 'left';
}

function alignLabel(ci) {
  const map = { left: t('Sinistra'), center: t('Centro'), right: t('Destra') };
  return map[getAlign(ci)] || t('Sinistra');
}

function cycleAlign(ci) {
  const order = ['left', 'center', 'right'];
  const cur = getAlign(ci);
  const next = order[(order.indexOf(cur) + 1) % 3];
  const aligns = [...(s.value.col_alignments || [])];
  while (aligns.length <= ci) aligns.push('left');
  aligns[ci] = next;
  tilesStore.updateTile(props.tileId, { col_alignments: aligns });
}

/* -- Styles -- */
const colBarGrid = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${colCount.value}, 1fr)`,
}));

const tableStyle = computed(() => ({
  width: '100%',
  borderCollapse: 'collapse',
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  fontSize: s.value.compact ? '12px' : '13px',
}));

const headerRowStyle = computed(() => ({
  background: s.value.header_bg || 'var(--olo-color-secondary, #16263d)',
  color: s.value.header_text_color || 'var(--olo-color-on-primary, #ffffff)',
}));

function bodyRowStyle(bodyIdx) {
  const style = {};
  if (s.value.striped && bodyIdx % 2 === 1) {
    style.background = s.value.even_row_bg || 'rgba(0,0,0,0.025)';
  }
  return style;
}

function cellStyle(ri, ci) {
  const pad = s.value.compact ? '6px 10px' : '10px 16px';
  const border = s.value.bordered ? `1px solid ${s.value.border_color || 'var(--olo-color-border, #e5e7eb)'}` : 'none';
  const align = getAlign(ci);
  return { padding: pad, borderBottom: border, borderRight: border, textAlign: align };
}
</script>

<style scoped>
.olo-table-editor { position: relative; }
.olo-te-col-bar {
  gap: 0; margin-bottom: 2px;
}
.olo-te-col-head {
  display: flex; align-items: center; justify-content: center; gap: 4px;
  padding: 2px 4px; min-height: 22px;
}
.olo-te-mini-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: auto; min-width: 20px; height: 20px; padding: 0 5px;
  border: 1px solid color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); border-radius: 4px;
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 8%, transparent); color: var(--olo-color-primary, #e1474f);
  font-size: 11px; font-weight: 600; cursor: pointer; line-height: 1;
  transition: all .15s;
}
.olo-te-mini-btn:hover { background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 20%, transparent); }
.olo-te-mini-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }
.olo-te-del-col, .olo-te-del-row { color: var(--olo-color-error, #b42318); border-color: color-mix(in srgb, var(--olo-color-error, #b42318) 30%, transparent); background: color-mix(in srgb, var(--olo-color-error, #b42318) 8%, transparent); }
.olo-te-del-col:hover, .olo-te-del-row:hover { background: color-mix(in srgb, var(--olo-color-error, #b42318) 20%, transparent); }

.olo-te-align-btn {
  display: inline-flex; align-items: center; justify-content: center;
  width: 20px; height: 20px; border: none; border-radius: 3px;
  background: transparent; color: var(--olo-color-text-faint, #94a3b8); cursor: pointer; padding: 0;
  transition: all .15s;
}
.olo-te-align-btn:hover { color: var(--olo-color-primary, #e1474f); background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 10%, transparent); }
.olo-te-align-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent); }

.olo-te-table { table-layout: auto; }
.olo-te-cell {
  position: relative; cursor: text; min-width: 60px;
  transition: background .15s;
}
.olo-te-cell:hover { background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 6%, transparent) !important; }
.olo-te-th { font-weight: 600; }
.olo-te-bold { font-weight: 600; }
.olo-te-text {
  display: block; min-height: 1.2em; white-space: pre-wrap;
}
.olo-te-input {
  position: absolute; inset: 0; width: 100%; height: 100%;
  border: 2px solid var(--olo-color-primary, #e1474f); border-radius: 0;
  background: #fff; color: #111; padding: 8px 14px;
  font-size: inherit; font-family: inherit;
  outline: none; z-index: 5; box-sizing: border-box;
}
.olo-te-hover:hover { background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 5%, transparent); }

.olo-te-row-bar {
  display: flex; gap: 6px; margin-top: 4px; padding: 2px 0;
}
</style>
