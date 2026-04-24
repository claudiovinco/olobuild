<template>
  <div class="rte-wrap">
    <!-- Toolbar -->
    <div class="rte-toolbar">
      <!-- Row 1: character formatting (always) -->
      <div class="rte-row">
        <button
          type="button"
          @click="editor?.chain().focus().toggleBold().run()"
          :class="{ active: editor?.isActive('bold') }"
          :title="t('Bold')"
        ><strong>B</strong></button>
        <button
          type="button"
          @click="editor?.chain().focus().toggleItalic().run()"
          :class="{ active: editor?.isActive('italic') }"
          :title="t('Italic')"
        ><em>I</em></button>
        <button
          type="button"
          @click="editor?.chain().focus().toggleUnderline().run()"
          :class="{ active: editor?.isActive('underline') }"
          :title="t('Underline')"
        ><u>U</u></button>
        <button
          type="button"
          @click="editor?.chain().focus().toggleStrike().run()"
          :class="{ active: editor?.isActive('strike') }"
          :title="t('Strikethrough')"
        ><s>S</s></button>

        <span class="rte-sep"></span>

        <!-- Text color -->
        <label class="rte-color-btn" :title="t('Text Color')">
          <span class="rte-color-label" :style="{ color: currentColor }">A</span>
          <input
            type="color"
            :value="currentColor"
            @input="editor?.chain().focus().setColor($event.target.value).run()"
            class="rte-color-input"
          />
        </label>

        <!-- Highlight -->
        <label class="rte-color-btn" :title="t('Highlight')">
          <span class="rte-highlight-label" :style="{ backgroundColor: currentHighlight }">H</span>
          <input
            type="color"
            :value="currentHighlight || '#ffff00'"
            @input="editor?.chain().focus().toggleHighlight({ color: $event.target.value }).run()"
            class="rte-color-input"
          />
        </label>

        <span class="rte-sep"></span>

        <!-- Font size -->
        <div class="rte-dropdown">
          <button type="button" class="rte-dropdown-btn" @click.stop="toggleDropdown('fontSize')" :title="t('Font Size')">
            {{ currentFontSize || 'Size' }}
          </button>
          <div v-if="openDropdown === 'fontSize'" class="rte-dropdown-menu">
            <button type="button" class="rte-dropdown-item" :class="{ active: !currentFontSize }"
              @click="setFontSize('')">{{ t('Default') }}</button>
            <button v-for="s in fontSizes" :key="s" type="button" class="rte-dropdown-item"
              :class="{ active: currentFontSize === s }" @click="setFontSize(s)">{{ s }}</button>
          </div>
        </div>
      </div>

      <!-- Row 2: paragraph formatting (block mode only) -->
      <div v-if="mode === 'block'" class="rte-row">
        <button
          type="button"
          @click="editor?.chain().focus().setTextAlign('left').run()"
          :class="{ active: editor?.isActive({ textAlign: 'left' }) }"
          :title="t('Align Left')"
        >&#9776;</button>
        <button
          type="button"
          @click="editor?.chain().focus().setTextAlign('center').run()"
          :class="{ active: editor?.isActive({ textAlign: 'center' }) }"
          :title="t('Align Center')"
        >&#9776;</button>
        <button
          type="button"
          @click="editor?.chain().focus().setTextAlign('right').run()"
          :class="{ active: editor?.isActive({ textAlign: 'right' }) }"
          :title="t('Align Right')"
        >&#9776;</button>
        <button
          type="button"
          @click="editor?.chain().focus().setTextAlign('justify').run()"
          :class="{ active: editor?.isActive({ textAlign: 'justify' }) }"
          :title="t('Justify')"
        >&#9776;</button>

        <span class="rte-sep"></span>

        <button
          type="button"
          @click="editor?.chain().focus().toggleBulletList().run()"
          :class="{ active: editor?.isActive('bulletList') }"
          :title="t('Bullet List')"
        >&#8226;</button>
        <button
          type="button"
          @click="editor?.chain().focus().toggleOrderedList().run()"
          :class="{ active: editor?.isActive('orderedList') }"
          :title="t('Ordered List')"
        >1.</button>
        <button
          type="button"
          @click="editor?.chain().focus().toggleBlockquote().run()"
          :class="{ active: editor?.isActive('blockquote') }"
          :title="t('Blockquote')"
        >&#8220;</button>

        <span class="rte-sep"></span>

        <!-- Heading select -->
        <div class="rte-dropdown">
          <button type="button" class="rte-dropdown-btn" @click.stop="toggleDropdown('heading')" :title="t('Heading')">
            {{ currentHeading === 'p' ? 'P' : 'H' + currentHeading }}
          </button>
          <div v-if="openDropdown === 'heading'" class="rte-dropdown-menu">
            <button type="button" class="rte-dropdown-item" :class="{ active: currentHeading === 'p' }"
              @click="setHeading('p')">P</button>
            <button v-for="n in [1,2,3,4]" :key="n" type="button" class="rte-dropdown-item"
              :class="{ active: currentHeading === String(n) }" @click="setHeading(String(n))">H{{ n }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Editor content -->
    <editor-content :editor="editor" class="rte-content" />
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import { TextStyle, FontSize } from '@tiptap/extension-text-style';
import Color from '@tiptap/extension-color';
import Highlight from '@tiptap/extension-highlight';
import TextAlign from '@tiptap/extension-text-align';
import { t } from '@/i18n';

const props = defineProps({
  modelValue: { type: String, default: '' },
  mode: { type: String, default: 'inline', validator: v => ['block', 'inline'].includes(v) },
});

const emit = defineEmits(['update:modelValue']);

const fontSizes = ['12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px', '40px', '48px'];

// Custom dropdown state
const openDropdown = ref(null);

function toggleDropdown(name) {
  openDropdown.value = openDropdown.value === name ? null : name;
}

function closeDropdowns() {
  openDropdown.value = null;
}

// Build extensions based on mode
function buildExtensions() {
  const exts = [
    StarterKit.configure({
      heading: props.mode === 'block' ? { levels: [1, 2, 3, 4] } : false,
      bulletList: props.mode === 'block' ? {} : false,
      orderedList: props.mode === 'block' ? {} : false,
      blockquote: props.mode === 'block' ? {} : false,
      hardBreak: props.mode === 'block',
      underline: false,
    }),
    Underline,
    TextStyle,
    FontSize,
    Color,
    Highlight.configure({ multicolor: true }),
  ];

  if (props.mode === 'block') {
    exts.push(TextAlign.configure({ types: ['heading', 'paragraph'] }));
  }

  return exts;
}

// Debounce timer
let debounceTimer = null;

const editor = useEditor({
  content: props.modelValue || '',
  extensions: buildExtensions(),
  editorProps: {
    handleKeyDown: props.mode === 'inline'
      ? (view, event) => {
          if (event.key === 'Enter') {
            event.preventDefault();
            return true;
          }
          return false;
        }
      : undefined,
  },
  onUpdate: ({ editor: ed }) => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      emit('update:modelValue', ed.getHTML());
    }, 300);
  },
});

// Sync external changes (undo/redo)
watch(() => props.modelValue, (newVal) => {
  if (!editor.value) return;
  const currentHTML = editor.value.getHTML();
  // Avoid infinite loop — only update if content actually changed externally
  if (newVal !== currentHTML) {
    editor.value.commands.setContent(newVal || '', false);
  }
});

// Computed states for toolbar
const currentColor = computed(() => {
  return editor.value?.getAttributes('textStyle')?.color || '#1a1a1a';
});

const currentHighlight = computed(() => {
  return editor.value?.getAttributes('highlight')?.color || '';
});

const currentFontSize = computed(() => {
  return editor.value?.getAttributes('fontSize')?.fontSize || editor.value?.getAttributes('textStyle')?.fontSize || '';
});

const currentHeading = computed(() => {
  if (!editor.value) return 'p';
  for (const level of [1, 2, 3, 4]) {
    if (editor.value.isActive('heading', { level })) return String(level);
  }
  return 'p';
});

function setFontSize(size) {
  if (!editor.value) return;
  openDropdown.value = null;
  if (!size) {
    editor.value.chain().focus().unsetFontSize().run();
    return;
  }
  editor.value.chain().focus().setFontSize(size).run();
}

function setHeading(val) {
  if (!editor.value) return;
  openDropdown.value = null;
  if (val === 'p') {
    editor.value.chain().focus().setParagraph().run();
  } else {
    editor.value.chain().focus().toggleHeading({ level: parseInt(val) }).run();
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdowns);
});

onBeforeUnmount(() => {
  clearTimeout(debounceTimer);
  document.removeEventListener('click', closeDropdowns);
});
</script>

<style scoped>
.rte-wrap {
  border: 1px solid #4b5563;
  border-radius: 6px;
  background: #1f2937;
}

.rte-toolbar {
  background: #111827;
  border-bottom: 1px solid #374151;
  padding: 2px;
}

.rte-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 1px;
  padding: 2px;
}

.rte-row > button {
  width: 26px;
  height: 26px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  border-radius: 4px;
  background: transparent;
  color: #d1d5db;
  cursor: pointer;
  font-size: 12px;
  padding: 0;
  line-height: 1;
}

.rte-row > button:hover {
  background: #374151;
  color: #fff;
}

.rte-row > button.active {
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
}

.rte-sep {
  width: 1px;
  height: 18px;
  background: #374151;
  margin: 0 3px;
}

.rte-color-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  cursor: pointer;
  position: relative;
  border-radius: 4px;
}

.rte-color-btn:hover {
  background: #374151;
}

.rte-color-label {
  font-weight: 700;
  font-size: 13px;
  text-decoration: underline;
  text-underline-offset: 2px;
}

.rte-highlight-label {
  font-weight: 700;
  font-size: 11px;
  padding: 1px 3px;
  border-radius: 2px;
  color: #000;
}

.rte-color-input {
  position: absolute;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  top: 0;
  left: 0;
}

.rte-dropdown {
  position: relative;
}

.rte-dropdown-btn {
  background: #1f2937;
  color: #d1d5db;
  border: 1px solid #374151;
  border-radius: 4px;
  font-size: 11px;
  padding: 2px 8px;
  height: 26px;
  cursor: pointer;
  white-space: nowrap;
  min-width: 42px;
  text-align: center;
}

.rte-dropdown-btn:hover {
  background: #374151;
  color: #fff;
}

.rte-dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 2px;
  background: #1f2937;
  border: 1px solid #4b5563;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
  z-index: 50;
  min-width: 70px;
  max-height: 200px;
  overflow-y: auto;
  padding: 4px 0;
}

.rte-dropdown-item {
  display: block;
  width: 100%;
  background: transparent;
  border: none;
  color: #d1d5db;
  font-size: 11px;
  padding: 4px 10px;
  cursor: pointer;
  text-align: left;
  white-space: nowrap;
}

.rte-dropdown-item:hover {
  background: #374151;
  color: #fff;
}

.rte-dropdown-item.active {
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
}

.rte-content {
  min-height: 40px;
  max-height: 300px;
  overflow-y: auto;
  background: #ffffff;
  border-radius: 0 0 5px 5px;
}

.rte-content :deep(.tiptap) {
  padding: 8px;
  color: #1f2937;
  font-size: 13px;
  line-height: 1.5;
  outline: none;
  min-height: 32px;
}

.rte-content :deep(.tiptap p) {
  margin: 0 0 0.5em;
}

.rte-content :deep(.tiptap p:last-child) {
  margin-bottom: 0;
}

.rte-content :deep(.tiptap h1),
.rte-content :deep(.tiptap h2),
.rte-content :deep(.tiptap h3),
.rte-content :deep(.tiptap h4) {
  font-weight: 600;
  margin: 0 0 0.4em;
}

.rte-content :deep(.tiptap h1) { font-size: 1.5em; }
.rte-content :deep(.tiptap h2) { font-size: 1.3em; }
.rte-content :deep(.tiptap h3) { font-size: 1.15em; }
.rte-content :deep(.tiptap h4) { font-size: 1em; }

.rte-content :deep(.tiptap ul) {
  list-style: disc;
  padding-left: 1.5em;
  margin: 0 0 0.5em;
}

.rte-content :deep(.tiptap ol) {
  list-style: decimal;
  padding-left: 1.5em;
  margin: 0 0 0.5em;
}

.rte-content :deep(.tiptap blockquote) {
  border-left: 3px solid var(--olo-color-primary, #6366f1);
  padding-left: 0.75em;
  margin: 0 0 0.5em;
  opacity: 0.85;
}

.rte-content :deep(.tiptap mark) {
  border-radius: 2px;
  padding: 0 2px;
}
</style>
