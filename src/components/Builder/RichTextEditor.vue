<template>
  <div class="rte-wrap">
    <!-- Toolbar -->
    <div class="rte-toolbar">
      <!-- Row 1: character formatting (always) -->
      <div class="rte-row">
        <!-- Undo/Redo -->
        <button
          type="button"
          @click="editor?.chain().focus().undo().run()"
          :disabled="!editor?.can().chain().focus().undo().run()"
          :title="t('Annulla')"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"/><path d="M21 17a9 9 0 0 0-15-6.7L3 13"/></svg>
        </button>
        <button
          type="button"
          @click="editor?.chain().focus().redo().run()"
          :disabled="!editor?.can().chain().focus().redo().run()"
          :title="t('Ripristina')"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"/><path d="M3 17a9 9 0 0 1 15-6.7L21 13"/></svg>
        </button>

        <span class="rte-sep"></span>

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

        <!-- Code inline -->
        <button
          type="button"
          @click="editor?.chain().focus().toggleCode().run()"
          :class="{ active: editor?.isActive('code') }"
          :title="t('Codice inline')"
        >
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
        </button>

        <!-- Super / Sub -->
        <button
          type="button"
          @click="editor?.chain().focus().toggleSuperscript().run()"
          :class="{ active: editor?.isActive('superscript') }"
          :title="t('Apice')"
        >x<sup style="font-size:8px">²</sup></button>
        <button
          type="button"
          @click="editor?.chain().focus().toggleSubscript().run()"
          :class="{ active: editor?.isActive('subscript') }"
          :title="t('Pedice')"
        >x<sub style="font-size:8px">₂</sub></button>

        <!-- Clear formatting -->
        <button
          type="button"
          @click="clearFormatting"
          :title="t('Rimuovi formattazione')"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7V4h16v3"/><path d="M5 20h6"/><path d="M13 4 8 20"/><path d="m15 15 5 5"/><path d="m20 15-5 5"/></svg>
        </button>

        <span class="rte-sep"></span>

        <!-- Link -->
        <div class="rte-dropdown">
          <button
            type="button"
            @click.stop="toggleLinkPopover"
            :class="{ active: editor?.isActive('link') }"
            :title="editor?.isActive('link') ? t('Modifica link') : t('Inserisci link')"
          >
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
              <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>
          </button>
          <div v-if="openDropdown === 'link'" class="rte-link-popover" @mousedown.stop @click.stop @keydown.enter.stop="applyLink">
            <FieldLink :modelValue="linkDraft" @update:modelValue="onLinkPicked" />
            <div class="rte-link-actions">
              <button v-if="editor?.isActive('link')" type="button" class="rte-link-action-remove" @click="unsetLink">{{ t('Rimuovi link') }}</button>
              <button type="button" class="rte-link-action-apply" @click="applyLink" :disabled="!linkDraft">{{ t('Applica') }}</button>
            </div>
          </div>
        </div>

        <!-- Emoji picker -->
        <div class="rte-dropdown">
          <button type="button" @click.stop="toggleDropdown('emoji')" :title="t('Inserisci emoji')">
            <span style="font-size:14px">&#128512;</span>
          </button>
          <div v-if="openDropdown === 'emoji'" class="rte-emoji-popover" @mousedown.stop @click.stop>
            <div class="rte-emoji-grid">
              <button v-for="e in emojiList" :key="e" type="button" class="rte-emoji-btn" @click="insertEmoji(e)">{{ e }}</button>
            </div>
          </div>
        </div>

        <!-- Dynamic field -->
        <div class="rte-dropdown">
          <button type="button" @click.stop="toggleDropdown('dynfield')" :title="t('Inserisci campo dinamico')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          </button>
          <div v-if="openDropdown === 'dynfield'" class="rte-dynfield-popover" @mousedown.stop @click.stop>
            <div class="rte-dynfield-list">
              <button v-for="f in dynamicFields" :key="f.token" type="button" class="rte-dynfield-item" @click="insertDynamicField(f.token)">
                <span class="rte-dynfield-label">{{ f.label }}</span>
                <code class="rte-dynfield-token">{{ f.token }}</code>
              </button>
            </div>
          </div>
        </div>

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

        <!-- Task list -->
        <button
          type="button"
          @click="editor?.chain().focus().toggleTaskList().run()"
          :class="{ active: editor?.isActive('taskList') }"
          :title="t('Lista checkbox')"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="6" height="6" x="3" y="4" rx="1"/><path d="m4 7 1 1 2-2"/><rect width="6" height="6" x="3" y="14" rx="1"/><path d="m4 17 1 1 2-2"/><line x1="12" x2="21" y1="6" y2="6"/><line x1="12" x2="21" y1="16" y2="16"/></svg>
        </button>

        <span class="rte-sep"></span>

        <!-- Horizontal Rule -->
        <button
          type="button"
          @click="editor?.chain().focus().setHorizontalRule().run()"
          :title="t('Linea orizzontale')"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/></svg>
        </button>

        <!-- Insert image -->
        <button
          type="button"
          @click="insertImage"
          :title="t('Inserisci immagine')"
        >
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
        </button>

        <!-- Table -->
        <div class="rte-dropdown">
          <button type="button" @click.stop="toggleDropdown('table')" :class="{ active: editor?.isActive('table') }" :title="t('Tabella')">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>
          </button>
          <div v-if="openDropdown === 'table'" class="rte-table-popover" @mousedown.stop @click.stop>
            <button v-if="!editor?.isActive('table')" type="button" class="rte-table-action" @click="insertTable">{{ t('Inserisci tabella 3x3') }}</button>
            <template v-else>
              <button type="button" class="rte-table-action" @click="editor?.chain().focus().addRowAfter().run()">{{ t('Aggiungi riga sotto') }}</button>
              <button type="button" class="rte-table-action" @click="editor?.chain().focus().addColumnAfter().run()">{{ t('Aggiungi colonna a destra') }}</button>
              <button type="button" class="rte-table-action" @click="editor?.chain().focus().deleteRow().run()">{{ t('Elimina riga') }}</button>
              <button type="button" class="rte-table-action" @click="editor?.chain().focus().deleteColumn().run()">{{ t('Elimina colonna') }}</button>
              <button type="button" class="rte-table-action" @click="editor?.chain().focus().toggleHeaderRow().run()">{{ t('Toggle header riga') }}</button>
              <button type="button" class="rte-table-action rte-table-danger" @click="editor?.chain().focus().deleteTable().run()">{{ t('Elimina tabella') }}</button>
            </template>
          </div>
        </div>

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

    <!-- Footer: word counter -->
    <div class="rte-footer">
      <span>{{ wordCount }} {{ t('parole') }} · {{ charCount }} {{ t('caratteri') }}</span>
    </div>
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
import Link from '@tiptap/extension-link';
import Superscript from '@tiptap/extension-superscript';
import Subscript from '@tiptap/extension-subscript';
import TaskList from '@tiptap/extension-task-list';
import TaskItem from '@tiptap/extension-task-item';
import Image from '@tiptap/extension-image';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableCell } from '@tiptap/extension-table-cell';
import { TableHeader } from '@tiptap/extension-table-header';
import Typography from '@tiptap/extension-typography';
import Placeholder from '@tiptap/extension-placeholder';
import CharacterCount from '@tiptap/extension-character-count';
import FieldLink from './fields/FieldLink.vue';
import { useMediaPicker } from '@/composables/useMediaPicker';
import { t } from '@/i18n';

const props = defineProps({
  modelValue: { type: String, default: '' },
  mode: { type: String, default: 'inline', validator: v => ['block', 'inline'].includes(v) },
});

const emit = defineEmits(['update:modelValue']);

const fontSizes = ['12px', '14px', '16px', '18px', '20px', '24px', '28px', '32px', '40px', '48px'];

// Custom dropdown state
const openDropdown = ref(null);
const linkDraft = ref('');

function toggleDropdown(name) {
  openDropdown.value = openDropdown.value === name ? null : name;
}

function closeDropdowns() {
  openDropdown.value = null;
}

function toggleLinkPopover() {
  if (openDropdown.value === 'link') {
    openDropdown.value = null;
    return;
  }
  // Precarica il valore: se siamo dentro un link esistente, prendi l'href corrente
  linkDraft.value = editor.value?.getAttributes('link')?.href || '';
  openDropdown.value = 'link';
}

function onLinkPicked(url) {
  linkDraft.value = url || '';
}

function applyLink() {
  if (!editor.value || !linkDraft.value) return;
  const href = linkDraft.value.trim();
  const chain = editor.value.chain().focus().extendMarkRange('link').setLink({ href });
  // Se non c'è selezione (cursore semplice), inserisce il testo dell'URL
  const { from, to } = editor.value.state.selection;
  if (from === to) {
    chain.insertContent(href).run();
  } else {
    chain.run();
  }
  openDropdown.value = null;
  linkDraft.value = '';
}

function unsetLink() {
  if (!editor.value) return;
  editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
  openDropdown.value = null;
  linkDraft.value = '';
}

// ─── Clear formatting ────────────────────────────────────────
function clearFormatting() {
  editor.value?.chain().focus().unsetAllMarks().clearNodes().run();
}

// ─── Emoji ───────────────────────────────────────────────────
const emojiList = [
  '😀','😁','😂','🤣','😊','😍','🥰','😘','😎','🤩','🥳','😇',
  '🤔','😏','😴','🤤','😋','🤯','😱','😭','😢','😤','😡','🤬',
  '👍','👎','👌','✌️','🤞','🤝','👏','🙏','💪','🙌','👋','✍️',
  '❤️','🧡','💛','💚','💙','💜','🖤','🤍','💔','💖','💕','💯',
  '🔥','✨','⭐','🌟','💫','⚡','💥','🎉','🎊','🎁','🎈','🏆',
  '✅','❌','⚠️','💡','📌','📍','🔗','📎','🎯','🚀','🛠️','📞',
  '☀️','🌙','⛅','🌧️','❄️','🌈','🌊','🌸','🌻','🌹','🌷','🌳',
];

function insertEmoji(emoji) {
  editor.value?.chain().focus().insertContent(emoji).run();
  openDropdown.value = null;
}

// ─── Dynamic fields ──────────────────────────────────────────
const dynamicFields = [
  { token: '{{site_name}}',    label: t('Nome sito') },
  { token: '{{site_tagline}}', label: t('Tagline sito') },
  { token: '{{site_url}}',     label: t('URL sito') },
  { token: '{{current_year}}', label: t('Anno corrente') },
  { token: '{{current_date}}', label: t('Data corrente') },
  { token: '{{current_time}}', label: t('Ora corrente') },
  { token: '{{post_title}}',   label: t('Titolo pagina/post') },
  { token: '{{post_excerpt}}', label: t('Estratto post') },
  { token: '{{author_name}}',  label: t('Nome autore') },
  { token: '{{page_url}}',     label: t('URL pagina corrente') },
];

function insertDynamicField(token) {
  editor.value?.chain().focus().insertContent(token).run();
  openDropdown.value = null;
}

// ─── Image inline ────────────────────────────────────────────
const { openSingleImage } = useMediaPicker();

function insertImage() {
  openSingleImage(({ url }) => {
    if (url) {
      editor.value?.chain().focus().setImage({ src: url }).run();
    }
  });
}

// ─── Table ───────────────────────────────────────────────────
function insertTable() {
  editor.value?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
  openDropdown.value = null;
}

// ─── Word/char counter ───────────────────────────────────────
const wordCount = computed(() => editor.value?.storage?.characterCount?.words() ?? 0);
const charCount = computed(() => editor.value?.storage?.characterCount?.characters() ?? 0);

// Build extensions based on mode
function buildExtensions() {
  const exts = [
    StarterKit.configure({
      heading: props.mode === 'block' ? { levels: [1, 2, 3, 4] } : false,
      bulletList: props.mode === 'block' ? {} : false,
      orderedList: props.mode === 'block' ? {} : false,
      blockquote: props.mode === 'block' ? {} : false,
      hardBreak: props.mode === 'block',
      horizontalRule: props.mode === 'block' ? {} : false,
      underline: false,
      link: false,
      code: {},
    }),
    Underline,
    TextStyle,
    FontSize,
    Color,
    Highlight.configure({ multicolor: true }),
    Link.configure({
      openOnClick: false,
      autolink: true,
      HTMLAttributes: { rel: 'noopener noreferrer', target: '_self' },
    }),
    Superscript,
    Subscript,
    Typography,
    Placeholder.configure({
      placeholder: t('Scrivi qui...'),
    }),
    CharacterCount.configure({}),
  ];

  if (props.mode === 'block') {
    exts.push(TextAlign.configure({ types: ['heading', 'paragraph'] }));
    exts.push(TaskList);
    exts.push(TaskItem.configure({ nested: true }));
    exts.push(Image.configure({ inline: false, allowBase64: false, HTMLAttributes: { class: 'rte-img' } }));
    exts.push(Table.configure({ resizable: true, HTMLAttributes: { class: 'rte-table' } }));
    exts.push(TableRow);
    exts.push(TableCell);
    exts.push(TableHeader);
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

// Normalize any CSS color (rgb/rgba/hsl/named) to #rrggbb — required by
// <input type="color">. Tiptap may return computed colors as rgb(...),
// which the native picker rejects with a console warning.
function toHexColor(val, fallback = '#000000') {
  if (!val) return fallback;
  const s = String(val).trim();
  // Already a 6-digit hex
  if (/^#[0-9a-f]{6}$/i.test(s)) return s.toLowerCase();
  // 3-digit hex → expand
  const short = s.match(/^#([0-9a-f])([0-9a-f])([0-9a-f])$/i);
  if (short) return ('#' + short[1] + short[1] + short[2] + short[2] + short[3] + short[3]).toLowerCase();
  // 8-digit hex (with alpha) → drop alpha
  if (/^#[0-9a-f]{8}$/i.test(s)) return s.substring(0, 7).toLowerCase();
  // rgb()/rgba()
  const rgb = s.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
  if (rgb) {
    const toHex = (n) => Math.max(0, Math.min(255, parseInt(n, 10))).toString(16).padStart(2, '0');
    return ('#' + toHex(rgb[1]) + toHex(rgb[2]) + toHex(rgb[3])).toLowerCase();
  }
  return fallback;
}

// Computed states for toolbar
const currentColor = computed(() => {
  return toHexColor(editor.value?.getAttributes('textStyle')?.color, '#1a1a1a');
});

const currentHighlight = computed(() => {
  return toHexColor(editor.value?.getAttributes('highlight')?.color, '');
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

.rte-content :deep(.tiptap a) {
  color: var(--olo-color-primary, #6366F1);
  text-decoration: underline;
  cursor: pointer;
}

/* Link popover */
.rte-link-popover {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 4px;
  width: 320px;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
  z-index: 100;
  padding: 10px;
}

.rte-link-actions {
  display: flex;
  justify-content: flex-end;
  gap: 6px;
  margin-top: 8px;
}

.rte-link-action-apply,
.rte-link-action-remove {
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 4px;
  border: 1px solid transparent;
  cursor: pointer;
}

.rte-link-action-apply {
  background: var(--olo-color-primary, #6366F1);
  color: #fff;
  border-color: var(--olo-color-primary, #6366F1);
}

.rte-link-action-apply:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.rte-link-action-remove {
  background: transparent;
  color: #dc2626;
  border-color: #fca5a5;
}

.rte-link-action-remove:hover {
  background: #fef2f2;
}

/* Disabled buttons */
.rte-row > button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.rte-row > button:disabled:hover {
  background: transparent;
  color: #d1d5db;
}

/* Emoji popover */
.rte-emoji-popover {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 4px;
  width: 280px;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
  z-index: 100;
  padding: 8px;
}

.rte-emoji-grid {
  display: grid;
  grid-template-columns: repeat(8, 1fr);
  gap: 2px;
  max-height: 220px;
  overflow-y: auto;
}

.rte-emoji-btn {
  background: transparent;
  border: 0;
  font-size: 18px;
  padding: 4px;
  cursor: pointer;
  border-radius: 4px;
  line-height: 1;
}

.rte-emoji-btn:hover {
  background: #f3f4f6;
}

/* Dynamic field popover */
.rte-dynfield-popover {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 4px;
  width: 260px;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
  z-index: 100;
  padding: 4px;
  max-height: 320px;
  overflow-y: auto;
}

.rte-dynfield-list {
  display: flex;
  flex-direction: column;
}

.rte-dynfield-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding: 6px 10px;
  background: transparent;
  border: 0;
  text-align: left;
  cursor: pointer;
  border-radius: 4px;
  font-size: 12px;
  color: #374151;
}

.rte-dynfield-item:hover {
  background: #f3f4f6;
}

.rte-dynfield-label {
  flex: 1;
  font-weight: 500;
}

.rte-dynfield-token {
  font-size: 10px;
  background: #f3f4f6;
  color: #6b7280;
  padding: 2px 5px;
  border-radius: 3px;
  font-family: monospace;
}

/* Table popover */
.rte-table-popover {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 4px;
  width: 220px;
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
  z-index: 100;
  padding: 4px;
  display: flex;
  flex-direction: column;
}

.rte-table-action {
  background: transparent;
  border: 0;
  padding: 6px 10px;
  text-align: left;
  cursor: pointer;
  border-radius: 4px;
  font-size: 12px;
  color: #374151;
}

.rte-table-action:hover {
  background: #f3f4f6;
}

.rte-table-danger {
  color: #dc2626;
}

.rte-table-danger:hover {
  background: #fef2f2;
}

/* Footer word counter */
.rte-footer {
  display: flex;
  justify-content: flex-end;
  padding: 4px 8px;
  font-size: 10px;
  color: #9ca3af;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
  border-radius: 0 0 5px 5px;
}

/* Inline code */
.rte-content :deep(.tiptap code) {
  background: #f3f4f6;
  padding: 1px 5px;
  border-radius: 3px;
  font-family: 'Fira Code', Consolas, Monaco, monospace;
  font-size: 0.9em;
  color: #ec4899;
}

/* Horizontal rule */
.rte-content :deep(.tiptap hr) {
  border: 0;
  border-top: 2px solid #e5e7eb;
  margin: 1em 0;
}

/* Task list */
.rte-content :deep(.tiptap ul[data-type="taskList"]) {
  list-style: none;
  padding-left: 0;
}

.rte-content :deep(.tiptap ul[data-type="taskList"] li) {
  display: flex;
  align-items: flex-start;
  gap: 6px;
}

.rte-content :deep(.tiptap ul[data-type="taskList"] li > label) {
  flex-shrink: 0;
  margin-top: 3px;
}

.rte-content :deep(.tiptap ul[data-type="taskList"] li[data-checked="true"] > div) {
  text-decoration: line-through;
  opacity: 0.6;
}

/* Image inline */
.rte-content :deep(.tiptap img.rte-img) {
  max-width: 100%;
  height: auto;
  border-radius: 4px;
  margin: 4px 0;
}

/* Table */
.rte-content :deep(.tiptap table.rte-table) {
  border-collapse: collapse;
  table-layout: fixed;
  width: 100%;
  margin: 0.5em 0;
  overflow: hidden;
}

.rte-content :deep(.tiptap table.rte-table td),
.rte-content :deep(.tiptap table.rte-table th) {
  min-width: 1em;
  border: 1px solid #d1d5db;
  padding: 4px 6px;
  vertical-align: top;
  position: relative;
}

.rte-content :deep(.tiptap table.rte-table th) {
  font-weight: 600;
  background: #f3f4f6;
  text-align: left;
}

/* Placeholder */
.rte-content :deep(.tiptap p.is-editor-empty:first-child::before) {
  color: #9ca3af;
  content: attr(data-placeholder);
  float: left;
  height: 0;
  pointer-events: none;
}

/* Superscript / Subscript */
.rte-content :deep(.tiptap sup) {
  vertical-align: super;
  font-size: 0.75em;
}

.rte-content :deep(.tiptap sub) {
  vertical-align: sub;
  font-size: 0.75em;
}
</style>
