<template>
  <div class="orz-ed" :class="{ 'orz-ed--mini': mini, 'orz-ed--spento': spento }">
    <div class="orz-ed-barra" role="toolbar" :aria-label="t('Formattazione del testo')">
      <select
        v-if="!mini"
        class="orz-ed-blocco"
        :value="blocco"
        :aria-label="t('Stile del paragrafo')"
        :disabled="spento"
        @change="impostaBlocco($event.target.value)"
      >
        <option value="p">{{ t('Paragrafo') }}</option>
        <option value="h2">{{ t('Titolo') }}</option>
        <option value="h3">{{ t('Sottotitolo') }}</option>
      </select>
      <button type="button" :class="{ on: attivo('bold') }" :aria-pressed="attivo('bold')" :title="t('Grassetto')" :aria-label="t('Grassetto')" :disabled="spento" @click="comando('toggleBold')"><Icona nome="grassetto" /></button>
      <button type="button" :class="{ on: attivo('italic') }" :aria-pressed="attivo('italic')" :title="t('Corsivo')" :aria-label="t('Corsivo')" :disabled="spento" @click="comando('toggleItalic')"><Icona nome="corsivo" /></button>
      <button type="button" :class="{ on: attivo('link') || linkAperto }" :aria-pressed="attivo('link')" :title="t('Link')" :aria-label="t('Link')" :disabled="spento" @click="apriLink"><Icona nome="link" /></button>
      <span class="orz-ed-sep" aria-hidden="true"></span>
      <button type="button" :class="{ on: attivo('bulletList') }" :aria-pressed="attivo('bulletList')" :title="t('Elenco puntato')" :aria-label="t('Elenco puntato')" :disabled="spento" @click="comando('toggleBulletList')"><Icona nome="elenco" /></button>
      <button type="button" :class="{ on: attivo('orderedList') }" :aria-pressed="attivo('orderedList')" :title="t('Elenco numerato')" :aria-label="t('Elenco numerato')" :disabled="spento" @click="comando('toggleOrderedList')"><Icona nome="numerato" /></button>
      <template v-if="!mini">
        <button type="button" :class="{ on: attivo('blockquote') }" :aria-pressed="attivo('blockquote')" :title="t('Citazione')" :aria-label="t('Citazione')" :disabled="spento" @click="comando('toggleBlockquote')"><Icona nome="citazione" /></button>
        <button type="button" :title="t('Foto nel testo')" :aria-label="t('Foto nel testo')" :disabled="spento" @click="foto"><Icona nome="foto" /></button>
        <button type="button" :class="{ on: inTabella }" :title="t('Tabella')" :aria-label="t('Tabella')" :disabled="spento || inTabella" @click="tabella"><Icona nome="tabella" /></button>
        <select class="orz-ed-blocco orz-ed-riquadro" value="" :aria-label="t('Aggiungi un riquadro')" :disabled="spento" @change="nuovoRiquadro($event)">
          <option value="" disabled>{{ t('Riquadro…') }}</option>
          <option v-for="x in TIPI_RIQUADRO" :key="x.tipo" :value="x.tipo">{{ t(x.nome) }}</option>
        </select>
      </template>
      <span class="orz-ed-spazio"></span>
      <button type="button" :title="t('Annulla')" :aria-label="t('Annulla')" :disabled="spento" @click="comando('undo')"><Icona nome="annulla" /></button>
      <button type="button" :title="t('Ripristina')" :aria-label="t('Ripristina')" :disabled="spento" @click="comando('redo')"><Icona nome="ripristina" /></button>
      <!-- Dentro un riquadro o una tabella: i suoi comandi, su una riga della barra (che resta in vista mentre scrivi). -->
      <div v-if="!mini && (inRiquadro || inTabella)" class="orz-ed-contesto" role="group" :aria-label="inTabella ? t('Tabella') : t('Riquadro')">
      <template v-if="inRiquadro">
        <span class="orz-ed-contesto-nome">{{ t('Riquadro') }}</span>
        <select class="orz-ed-blocco" :value="tipoRiquadro" :aria-label="t('Tipo di riquadro')" :disabled="spento" @change="cambiaRiquadro($event.target.value)">
          <option v-for="x in TIPI_RIQUADRO" :key="x.tipo" :value="x.tipo">{{ t(x.nome) }}</option>
        </select>
        <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" :disabled="spento" @click="comando('togliRiquadro')">{{ t('Togli il riquadro') }}</button>
      </template>
      <template v-if="inTabella">
        <span class="orz-ed-contesto-nome">{{ t('Tabella') }}</span>
        <button type="button" class="orz-btn orz-btn--piccolo" :disabled="spento" @click="comando('addRowAfter')">{{ t('+ Riga') }}</button>
        <button type="button" class="orz-btn orz-btn--piccolo" :disabled="spento" @click="comando('addColumnAfter')">{{ t('+ Colonna') }}</button>
        <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" :disabled="spento" @click="comando('deleteRow')">{{ t('Togli la riga') }}</button>
        <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" :disabled="spento" @click="comando('deleteColumn')">{{ t('Togli la colonna') }}</button>
        <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" :disabled="spento" @click="comando('deleteTable')">{{ t('Elimina la tabella') }}</button>
      </template>
      </div>
    </div>
    <form v-if="linkAperto" class="orz-ed-link" @submit.prevent="applicaLink">
      <input
        ref="campoLink"
        v-model="indirizzo"
        type="text"
        inputmode="url"
        :placeholder="t('https://… oppure /pagina/')"
        :aria-label="t('Indirizzo del link')"
        @keydown.esc.prevent="chiudiLink"
      />
      <button type="submit" class="orz-btn orz-btn--piccolo">{{ t('Applica') }}</button>
      <button v-if="attivo('link')" type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" @click="togliLink">{{ t('Togli il link') }}</button>
      <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" :aria-label="t('Chiudi')" @click="chiudiLink"><Icona nome="x" /></button>
    </form>
    <EditorContent :editor="editor" class="orz-ed-testo" />
  </div>
</template>

<script setup>
/**
 * L'editor del testo della Redazione (TipTap): paragrafi, titoli, grassetto, corsivo, link, elenchi, citazione, foto con
 * didascalia, tabelle, riquadri (nodi.js: Buono a sapersi, Attenzione, Da ricordare…).
 * Solo ciò che Olobuild_Redazione::TAG_TESTO accetta: niente barrato, sottolineato, codice (anche dalle scorciatoie),
 * altrimenti il testo salvato tornerebbe in sola lettura. `mini` = per i campi «Testo formattato» (senza titoli né foto).
 */
import { ref, watch, nextTick, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import { Table, TableRow, TableHeader, TableCell } from '@tiptap/extension-table';
import { Riquadro, RiquadroTitolo, Figura, TIPI_RIQUADRO } from './nodi.js';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import { scegli } from './media.js';

const props = defineProps({
  modelValue: { type: String, default: '' },
  mini: { type: Boolean, default: false },
  spento: { type: Boolean, default: false },
  segnaposto: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

// La foto tiene le classi di WordPress (wp-image-ID dà le misure responsive, align… l'allineamento).
const Foto = Image.extend({
  addAttributes() {
    return {
      ...this.parent?.(),
      class: { default: null },
      width: { default: null },
      height: { default: null },
    };
  },
});

/** L'HTML da salvare: vuoto se non c'è testo; <li><p>x</p></li> → <li>x</li>, come lo scrive WordPress. */
function pulito(html) {
  if (!html || html === '<p></p>') return '';
  const box = document.createElement('div');
  box.innerHTML = html;
  // Le tabelle senza le larghezze che TipTap scrive (style e colgroup): con gli stili il testo andrebbe in sola lettura.
  box.querySelectorAll('colgroup').forEach((c) => c.remove());
  box.querySelectorAll('table, td, th').forEach((c) => c.removeAttribute('style'));
  box.querySelectorAll('td, th').forEach((c) => {
    if (c.getAttribute('colspan') === '1') c.removeAttribute('colspan');
    if (c.getAttribute('rowspan') === '1') c.removeAttribute('rowspan');
  });
  box.querySelectorAll('li, td, th').forEach((li) => {
    const p = Array.from(li.children).filter((c) => c.tagName === 'P');
    if (p.length === 1 && li.firstElementChild === p[0]) {
      while (p[0].firstChild) li.insertBefore(p[0].firstChild, p[0]);
      p[0].remove();
    }
  });
  // Il paragrafo vuoto che TipTap tiene in fondo dopo un riquadro, una tabella o una foto (per scriverci dopo) non si salva.
  while (box.lastElementChild && box.lastElementChild.tagName === 'P' && !box.lastElementChild.textContent.trim()
    && !box.lastElementChild.querySelector('img')) {
    box.lastElementChild.remove();
  }
  return box.innerHTML;
}

let ultimo = props.modelValue || '';
// Il testo appena caricato com'è e come lo riscrive l'editor: finché l'editor dà lo stesso, non è una modifica (TipTap
// riscrive l'HTML a modo suo e aggiunge un paragrafo in fondo dopo un riquadro: la scheda risultava «non salvata»).
let caricato = ultimo;
let caricatoNormale = null;

const editor = useEditor({
  content: props.modelValue || '',
  editable: !props.spento,
  extensions: [
    StarterKit.configure({
      heading: props.mini ? false : { levels: [2, 3, 4] },
      blockquote: props.mini ? false : {},
      horizontalRule: props.mini ? false : {},
      code: false,
      codeBlock: false,
      strike: false,
      underline: false,
      link: { openOnClick: false, autolink: true, HTMLAttributes: { target: null, rel: null } },
    }),
    ...(props.mini ? [] : [
      Foto.configure({ inline: false }),
      Figura,
      Riquadro,
      RiquadroTitolo,
      Table.configure({ resizable: false }),
      TableRow,
      TableHeader,
      TableCell,
    ]),
    Placeholder.configure({
      includeChildren: true,
      placeholder: ({ node }) => {
        if (node.type.name === 'figura') return t('Didascalia (facoltativa)');
        if (node.type.name === 'riquadroTitolo') return t('Titolo del riquadro');
        return props.segnaposto || t('Scrivi qui…');
      },
    }),
  ],
  onCreate: ({ editor: ed }) => {
    if (caricatoNormale === null) caricatoNormale = pulito(ed.getHTML());
  },
  onUpdate: ({ editor: ed }) => {
    const html = pulito(ed.getHTML());
    // Prima che l'editor sia pronto (TipTap avvisa dopo un attimo) le modifiche sono sue, non di chi scrive: il paragrafo
    // che aggiunge in fondo dopo un riquadro. Quello è il testo «come caricato».
    if (caricatoNormale === null) {
      caricatoNormale = html;
      return;
    }
    // Uguale a quello caricato: torna il testo di prima, così com'era (nessuna modifica da salvare).
    const nuovo = caricatoNormale !== null && html === caricatoNormale ? caricato : html;
    if (nuovo === ultimo) return;
    ultimo = nuovo;
    emit('update:modelValue', ultimo);
  },
});

watch(() => props.modelValue, (v) => {
  if (!editor.value || (v || '') === ultimo) return;
  ultimo = v || '';
  caricato = ultimo;
  caricatoNormale = null;
  editor.value.commands.setContent(ultimo, { emitUpdate: false });
  caricatoNormale = pulito(editor.value.getHTML());
});
watch(() => props.spento, (v) => editor.value && editor.value.setEditable(!v));

const blocco = ref('p');
const inTabella = ref(false);
const inRiquadro = ref(false);
const tipoRiquadro = ref('nota');
function aggiornaBlocco() {
  const ed = editor.value;
  if (!ed) return;
  inTabella.value = ed.isActive('table');
  inRiquadro.value = ed.isActive('riquadro');
  tipoRiquadro.value = inRiquadro.value ? (ed.getAttributes('riquadro').tipo || 'nota') : 'nota';
  blocco.value = ed.isActive('heading', { level: 2 }) ? 'h2' : (ed.isActive('heading', { level: 3 }) ? 'h3' : 'p');
}
watch(editor, (ed) => {
  if (ed) ed.on('transaction', aggiornaBlocco);
});

function attivo(nome) {
  return !!(editor.value && editor.value.isActive(nome));
}
function comando(nome) {
  if (editor.value) editor.value.chain().focus()[nome]().run();
}
function impostaBlocco(v) {
  const c = editor.value && editor.value.chain().focus();
  if (!c) return;
  if (v === 'p') c.setParagraph().run();
  else c.setHeading({ level: v === 'h2' ? 2 : 3 }).run();
}

const linkAperto = ref(false);
const indirizzo = ref('');
const campoLink = ref(null);
async function apriLink() {
  if (!editor.value) return;
  indirizzo.value = editor.value.getAttributes('link').href || '';
  linkAperto.value = true;
  await nextTick();
  if (campoLink.value) campoLink.value.focus();
}
function chiudiLink() {
  linkAperto.value = false;
  if (editor.value) editor.value.commands.focus();
}
function applicaLink() {
  const href = indirizzo.value.trim();
  const c = editor.value.chain().focus().extendMarkRange('link');
  if (href) c.setLink({ href }).run();
  else c.unsetLink().run();
  linkAperto.value = false;
}
function togliLink() {
  editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
  linkAperto.value = false;
}

function foto() {
  scegli('immagine', (scelti) => {
    const m = scelti[0];
    if (!m || !editor.value) return;
    editor.value.chain().focus().inserisciFigura({ src: m.url, alt: m.alt || '', classe: 'wp-image-' + m.id + ' size-large' }).run();
  });
}

function tabella() {
  if (editor.value) editor.value.chain().focus().insertTable({ rows: 3, cols: 2, withHeaderRow: true }).run();
}

function nuovoRiquadro(e) {
  const tipo = e.target.value;
  e.target.value = '';
  const x = TIPI_RIQUADRO.find((r) => r.tipo === tipo);
  if (x && editor.value) editor.value.chain().focus().inserisciRiquadro(x.tipo, t(x.nome)).run();
}

/** Cambia il tipo del riquadro; il titolo segue il tipo se era ancora quello proposto. */
function cambiaRiquadro(tipo) {
  const ed = editor.value;
  if (!ed) return;
  const { $from } = ed.state.selection;
  for (let d = $from.depth; d > 0; d -= 1) {
    const n = $from.node(d);
    if (n.type.name !== 'riquadro') continue;
    const pos = $from.before(d);
    const titolo = n.firstChild;
    const vecchio = TIPI_RIQUADRO.find((r) => r.tipo === n.attrs.tipo);
    const nuovo = TIPI_RIQUADRO.find((r) => r.tipo === tipo);
    const tr = ed.state.tr.setNodeMarkup(pos, undefined, { ...n.attrs, tipo });
    if (titolo && vecchio && nuovo && titolo.textContent === t(vecchio.nome)) {
      const inizio = pos + 2;
      tr.insertText(t(nuovo.nome), inizio, inizio + titolo.content.size);
    }
    ed.view.dispatch(tr);
    ed.commands.focus();
    return;
  }
}

onBeforeUnmount(() => {
  if (editor.value) editor.value.destroy();
});
</script>
