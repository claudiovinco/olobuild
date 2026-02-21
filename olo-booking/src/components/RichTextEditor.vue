<template>
  <div class="rte-wrap">
    <div class="rte-toolbar">
      <!-- Text style -->
      <button type="button" @click="exec('bold')" :class="{ 'rte-active': isActive('bold') }" title="Grassetto (Ctrl+B)"><b>B</b></button>
      <button type="button" @click="exec('italic')" :class="{ 'rte-active': isActive('italic') }" title="Corsivo (Ctrl+I)"><i>I</i></button>
      <button type="button" @click="exec('underline')" :class="{ 'rte-active': isActive('underline') }" title="Sottolineato (Ctrl+U)"><u>U</u></button>
      <button type="button" @click="exec('strikeThrough')" :class="{ 'rte-active': isActive('strikeThrough') }" title="Barrato"><s>S</s></button>
      <span class="rte-sep"></span>

      <!-- Font size -->
      <select class="rte-select" @change="setFontSize($event)" title="Grandezza testo">
        <option value="">Dim.</option>
        <option value="1">Piccolo</option>
        <option value="3">Normale</option>
        <option value="4">Grande</option>
        <option value="5">Molto grande</option>
        <option value="6">Enorme</option>
      </select>

      <!-- Heading -->
      <select class="rte-select" @change="setHeading($event)" title="Titolo">
        <option value="">Paragrafo</option>
        <option value="h2">Titolo 2</option>
        <option value="h3">Titolo 3</option>
        <option value="h4">Titolo 4</option>
      </select>
      <span class="rte-sep"></span>

      <!-- Colors -->
      <label class="rte-color-btn" title="Colore testo">
        <span class="rte-color-label">A</span>
        <span class="rte-color-bar" :style="{ background: currentColor }"></span>
        <input type="color" v-model="currentColor" @input="applyColor" class="rte-color-input" />
      </label>
      <label class="rte-color-btn" title="Evidenziatore">
        <span class="rte-color-label" style="background:#fef08a;border-radius:2px;padding:0 2px">H</span>
        <span class="rte-color-bar" :style="{ background: currentBg }"></span>
        <input type="color" v-model="currentBg" @input="applyBgColor" class="rte-color-input" />
      </label>
      <span class="rte-sep"></span>

      <!-- Lists & indent -->
      <button type="button" @click="exec('insertUnorderedList')" :class="{ 'rte-active': isActive('insertUnorderedList') }" title="Elenco puntato">&#8226;</button>
      <button type="button" @click="exec('insertOrderedList')" :class="{ 'rte-active': isActive('insertOrderedList') }" title="Elenco numerato">1.</button>
      <span class="rte-sep"></span>

      <!-- Align -->
      <button type="button" @click="exec('justifyLeft')" title="Allinea a sinistra" :class="{ 'rte-active': isActive('justifyLeft') }">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M1 2h14v1.5H1zM1 5.5h10v1.5H1zM1 9h14v1.5H1zM1 12.5h10v1.5H1z"/></svg>
      </button>
      <button type="button" @click="exec('justifyCenter')" title="Centra" :class="{ 'rte-active': isActive('justifyCenter') }">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M1 2h14v1.5H1zM3 5.5h10v1.5H3zM1 9h14v1.5H1zM3 12.5h10v1.5H3z"/></svg>
      </button>
      <button type="button" @click="exec('justifyRight')" title="Allinea a destra" :class="{ 'rte-active': isActive('justifyRight') }">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M1 2h14v1.5H1zM5 5.5h10v1.5H5zM1 9h14v1.5H1zM5 12.5h10v1.5H5z"/></svg>
      </button>
      <span class="rte-sep"></span>

      <!-- Link & quote -->
      <button type="button" @click="insertLink" title="Link">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M6.354 5.354a.5.5 0 010-.708l3-3a3.5 3.5 0 014.95 4.95l-3 3a.5.5 0 01-.708-.708l3-3a2.5 2.5 0 00-3.536-3.536l-3 3a.5.5 0 01-.708 0zM9.646 10.646a.5.5 0 010 .708l-3 3a3.5 3.5 0 01-4.95-4.95l3-3a.5.5 0 01.708.708l-3 3a2.5 2.5 0 003.536 3.536l3-3a.5.5 0 01.708 0zM5.354 10.646a.5.5 0 010-.708l5-5a.5.5 0 01.708.708l-5 5a.5.5 0 01-.708 0z"/></svg>
      </button>
      <button type="button" @click="toggleBlockquote" title="Citazione">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M2.5 3C1.12 3 0 4.12 0 5.5v3C0 9.88 1.12 11 2.5 11H4l-1 2h2l1-2h.5C7.88 11 9 9.88 9 8.5v-3C9 4.12 7.88 3 6.5 3h-4zm7 0C8.12 3 7 4.12 7 5.5v3C7 9.88 8.12 11 9.5 11H11l-1 2h2l1-2h.5c1.38 0 2.5-1.12 2.5-2.5v-3C16 4.12 14.88 3 13.5 3h-4z"/></svg>
      </button>
      <button type="button" @click="exec('insertHorizontalRule')" title="Linea orizzontale">―</button>
      <span class="rte-sep"></span>

      <!-- Clean -->
      <button type="button" @click="exec('removeFormat')" title="Rimuovi formattazione">
        <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor"><path d="M1 2l14 14M15 2L1 16" stroke="currentColor" stroke-width="1.5" fill="none"/></svg>
      </button>
    </div>
    <div
      ref="editor"
      class="rte-content"
      contenteditable="true"
      :style="{ minHeight: minHeight + 'px' }"
      @input="onInput"
      @paste="onPaste"
      @keydown="onKeydown"
    ></div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  minHeight: { type: Number, default: 80 },
});
const emit = defineEmits(['update:modelValue']);

const editor = ref(null);
const currentColor = ref('#1f2937');
const currentBg = ref('#fef08a');

onMounted(() => {
  if (editor.value) {
    editor.value.innerHTML = props.modelValue || '';
  }
});

watch(() => props.modelValue, (val) => {
  if (editor.value && editor.value.innerHTML !== val) {
    editor.value.innerHTML = val || '';
  }
});

function onInput() {
  emit('update:modelValue', editor.value.innerHTML);
}

function onPaste(e) {
  e.preventDefault();
  const html = e.clipboardData.getData('text/html');
  const text = e.clipboardData.getData('text/plain');
  if (html) {
    const clean = sanitizeHtml(html);
    document.execCommand('insertHTML', false, clean);
  } else {
    document.execCommand('insertText', false, text);
  }
}

function sanitizeHtml(html) {
  const tmp = document.createElement('div');
  tmp.innerHTML = html;
  tmp.querySelectorAll('script,style,iframe,object,embed').forEach(el => el.remove());
  // Keep style, href attributes for pasted content
  tmp.querySelectorAll('*').forEach(el => {
    const tag = el.tagName.toLowerCase();
    [...el.attributes].forEach(attr => {
      if (tag === 'a' && attr.name === 'href') return;
      if (attr.name === 'style') return; // keep inline styles (colors, sizes)
      el.removeAttribute(attr.name);
    });
  });
  return tmp.innerHTML;
}

function onKeydown(e) {
  // Ctrl+B, Ctrl+I, Ctrl+U handled natively
}

function exec(cmd, val = null) {
  editor.value.focus();
  document.execCommand(cmd, false, val);
  nextTick(() => onInput());
}

function isActive(cmd) {
  try { return document.queryCommandState(cmd); } catch(e) { return false; }
}

function setFontSize(e) {
  const val = e.target.value;
  if (val) {
    exec('fontSize', val);
  }
  e.target.value = '';
}

function setHeading(e) {
  const val = e.target.value;
  if (val) {
    exec('formatBlock', val);
  } else {
    exec('formatBlock', 'p');
  }
  e.target.value = '';
}

function applyColor() {
  exec('foreColor', currentColor.value);
}

function applyBgColor() {
  exec('hiliteColor', currentBg.value);
}

function toggleBlockquote() {
  exec('formatBlock', 'blockquote');
}

function insertLink() {
  const sel = window.getSelection();
  const text = sel.toString();
  const url = prompt('URL del link:', 'https://');
  if (!url) return;
  if (text) {
    exec('createLink', url);
  } else {
    const label = prompt('Testo del link:', '') || url;
    exec('insertHTML', `<a href="${url}">${label}</a>`);
  }
}
</script>

<style scoped>
.rte-wrap {
  border: 1px solid #ccc;
  border-radius: 8px;
  overflow: hidden;
  background: #ffffff;
  transition: border-color 0.2s;
}
.rte-wrap:focus-within {
  border-color: var(--olom-accent, #f59e0b);
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.12);
}
.rte-toolbar {
  display: flex;
  align-items: center;
  gap: 2px;
  padding: 4px 6px;
  background: #f3f3f3;
  border-bottom: 1px solid #ddd;
  flex-wrap: wrap;
}
.rte-toolbar button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 26px;
  flex: 0 0 auto;
  border: none;
  background: none;
  color: #555;
  cursor: pointer;
  border-radius: 4px;
  font-size: 13px;
  transition: background 0.15s, color 0.15s;
}
.rte-toolbar button:hover {
  background: #e5e5e5;
  color: #1a1a1a;
}
.rte-toolbar button.rte-active {
  background: rgba(245, 158, 11, 0.12);
  color: var(--olom-accent, #f59e0b);
}
.rte-sep {
  width: 1px;
  height: 18px;
  flex: 0 0 auto;
  background: #ccc;
  margin: 0 3px;
}
.rte-select {
  height: 26px;
  width: 80px;
  flex: 0 0 auto;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: #ffffff;
  color: #1a1a1a;
  font-size: 12px;
  padding: 0 4px;
  cursor: pointer;
  outline: none;
}
.rte-select:hover { border-color: #999; }
.rte-select:focus { border-color: var(--olom-accent, #f59e0b); }

/* Color picker button */
.rte-color-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  flex: 0 0 auto;
  width: 28px;
  height: 26px;
  border-radius: 4px;
  cursor: pointer;
  position: relative;
  transition: background 0.15s;
}
.rte-color-btn:hover { background: #e5e5e5; }
.rte-color-label {
  font-size: 13px;
  font-weight: 700;
  color: #333;
  line-height: 1;
}
.rte-color-bar {
  width: 16px;
  height: 3px;
  border-radius: 1px;
  margin-top: 1px;
}
.rte-color-input {
  position: absolute;
  width: 0;
  height: 0;
  opacity: 0;
  pointer-events: none;
}

.rte-content {
  padding: 10px 12px;
  font-size: 14px;
  line-height: 1.6;
  color: #1a1a1a;
  background: #ffffff;
  outline: none;
}
.rte-content:empty::before {
  content: attr(data-placeholder);
  color: #aaa;
  pointer-events: none;
}
.rte-content p { margin: 0 0 0.5em; }
.rte-content p:last-child { margin-bottom: 0; }
.rte-content ul, .rte-content ol { margin: 0 0 0.5em 1.2em; padding: 0; }
.rte-content a { color: var(--olom-primary, #6366F1); text-decoration: underline; }
.rte-content blockquote {
  margin: 0.5em 0;
  padding: 8px 14px;
  border-left: 3px solid var(--olom-primary, #6366F1);
  background: #f3f4f6;
  color: #4b5563;
  font-style: italic;
}
.rte-content h2 { font-size: 1.4em; font-weight: 700; margin: 0.5em 0 0.3em; }
.rte-content h3 { font-size: 1.2em; font-weight: 700; margin: 0.5em 0 0.3em; }
.rte-content h4 { font-size: 1.05em; font-weight: 600; margin: 0.5em 0 0.3em; }
.rte-content hr { border: none; border-top: 1px solid #d1d5db; margin: 0.8em 0; }
</style>
