/**
 * Gli elementi in più dell'editor della Redazione, per scrivere un manuale (e qualunque testo lungo):
 * - il riquadro: <aside class="olo-riquadro olo-riquadro--nota"><p class="olo-riquadro-t">Buono a sapersi</p>…</aside>,
 *   col titolo che si scrive (e si traduce) come il resto;
 * - la foto con la didascalia: <figure class="olo-figura"><img …><figcaption>…</figcaption></figure>.
 * Sul sito li disegna frontend.css (.olo-riquadro, .olo-figura), con i colori del tema. Olobuild_Redazione::TAG_TESTO
 * accetta questi tag: un testo che li contiene resta scrivibile nella Redazione.
 */
import { Node, mergeAttributes } from '@tiptap/core';

/** I tipi di riquadro, col titolo che il riquadro prende quando lo inserisci (poi si cambia scrivendo). */
export const TIPI_RIQUADRO = [
  { tipo: 'nota', nome: 'Buono a sapersi' },
  { tipo: 'attenzione', nome: 'Attenzione' },
  { tipo: 'ricorda', nome: 'Da ricordare' },
  { tipo: 'obiettivi', nome: 'Cosa impari' },
  { tipo: 'serve', nome: 'Ti serve' },
];
const TIPI = TIPI_RIQUADRO.map((x) => x.tipo);

export const RiquadroTitolo = Node.create({
  name: 'riquadroTitolo',
  content: 'text*',
  marks: '',
  defining: true,
  parseHTML() {
    return [{ tag: 'p.olo-riquadro-t', priority: 60 }];
  },
  renderHTML({ HTMLAttributes }) {
    return ['p', mergeAttributes(HTMLAttributes, { class: 'olo-riquadro-t' }), 0];
  },
});

export const Riquadro = Node.create({
  name: 'riquadro',
  group: 'block',
  content: 'riquadroTitolo block+',
  defining: true,
  addAttributes() {
    return {
      tipo: {
        default: 'nota',
        parseHTML: (el) => {
          const m = (el.getAttribute('class') || '').match(/olo-riquadro--([a-z]+)/);
          return m && TIPI.includes(m[1]) ? m[1] : 'nota';
        },
        renderHTML: (a) => ({ class: 'olo-riquadro olo-riquadro--' + (TIPI.includes(a.tipo) ? a.tipo : 'nota') }),
      },
    };
  },
  parseHTML() {
    return [{ tag: 'aside.olo-riquadro' }];
  },
  renderHTML({ HTMLAttributes }) {
    return ['aside', HTMLAttributes, 0];
  },
  addCommands() {
    return {
      /** Un riquadro nuovo dove sta il cursore, col suo titolo e un paragrafo vuoto dove scrivere. */
      inserisciRiquadro: (tipo, titolo) => ({ commands }) => commands.insertContent({
        type: 'riquadro',
        attrs: { tipo },
        content: [
          { type: 'riquadroTitolo', content: titolo ? [{ type: 'text', text: titolo }] : [] },
          { type: 'paragraph' },
        ],
      }),
      /** Toglie il riquadro attorno al cursore: il testo resta, senza il titolo. */
      togliRiquadro: () => ({ state, tr, dispatch }) => {
        const { $from } = state.selection;
        for (let d = $from.depth; d > 0; d -= 1) {
          const n = $from.node(d);
          if (n.type.name === 'riquadro') {
            const figli = [];
            n.forEach((c, _o, i) => {
              if (i > 0) figli.push(c);
            });
            if (dispatch) tr.replaceWith($from.before(d), $from.after(d), figli);
            return true;
          }
        }
        return false;
      },
    };
  },
});

export const Figura = Node.create({
  name: 'figura',
  group: 'block',
  content: 'text*',
  marks: 'bold italic link',
  draggable: true,
  isolating: true,
  addAttributes() {
    return {
      src: { default: null },
      alt: { default: '' },
      classe: { default: null },
    };
  },
  parseHTML() {
    return [{
      tag: 'figure',
      contentElement: (el) => el.querySelector('figcaption') || document.createElement('figcaption'),
      getAttrs: (el) => {
        const img = el.querySelector('img');
        if (!img) return false;
        return { src: img.getAttribute('src'), alt: img.getAttribute('alt') || '', classe: img.getAttribute('class') };
      },
    }];
  },
  renderHTML({ node }) {
    const img = { src: node.attrs.src, alt: node.attrs.alt || '' };
    if (node.attrs.classe) img.class = node.attrs.classe;
    return ['figure', { class: 'olo-figura' }, ['img', img], ['figcaption', 0]];
  },
  addCommands() {
    return {
      inserisciFigura: (attrs) => ({ commands }) => commands.insertContent({ type: 'figura', attrs }),
    };
  },
});
