// La torcia della tile Blend Text (modalità «Torcia», 'spotlight': disco che segue il puntatore su tutta la
// pagina) è diventata l'effetto del mouse di ogni elemento: Avanzate → Effetti mouse → Spotlight cursore, con
// l'Ambito. Una tile salvata così si converte quando il builder la apre (preparaContenuto in stores/tiles.js):
// la torcia passa nelle Avanzate con ambito «Tutta la pagina» e gli stessi valori, e il testo torna normale
// (in modalità Torcia il blend non stava sul testo, lo portava il disco). Il PHP disegna la torcia di prima
// finché la pagina non si salva convertita: stesso motore (data-olo-spotlight), stessa resa.

const DA_TORCIA = {
  spotlight_size: 'cursor_spotlight_size',
  spotlight_softness: 'cursor_spotlight_softness',
  spotlight_blend: 'cursor_spotlight_blend',
  spotlight_color: 'cursor_spotlight_color',
  spotlight_easing: 'cursor_spotlight_easing',
};

export function migraTorciaBlendText(nodi) {
  if (!Array.isArray(nodi)) return;
  for (const n of nodi) {
    if (!n || typeof n !== 'object') continue;
    const s = n.settings;
    if (n.type === 'blendtext' && s && s.mode === 'spotlight') {
      if (!n.advanced || Array.isArray(n.advanced)) n.advanced = {};
      const a = n.advanced;
      // una torcia già impostata nelle Avanzate vince: non la si sovrascrive
      if (!a.cursor_spotlight) {
        a.cursor_spotlight = true;
        a.cursor_spotlight_scope = 'page';
        for (const [da, a2] of Object.entries(DA_TORCIA)) {
          if (s[da] !== undefined && s[da] !== '') a[a2] = s[da];
        }
      }
      for (const da of Object.keys(DA_TORCIA)) delete s[da];
      s.mode = 'text';
      s.blend_mode = 'normal';
    }
    if (Array.isArray(n.children)) migraTorciaBlendText(n.children);
  }
}
