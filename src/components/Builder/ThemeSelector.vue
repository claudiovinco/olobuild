<template>
  <span style="display:none" ref="anchor"></span>
</template>

<script setup>
import { computed } from 'vue';
import { createThemePicker } from '../../theme-picker/themePicker.js';
import { t } from '@/i18n';
import { useToast } from '@/composables/useToast.js';
import { ricordaStileSostituito } from '@/utils/styleSnapshots';

const oloData = computed(() => window.oloData || {});
const toast = useToast();

let picker = null;

async function importTheme(theme) {
  if (!theme || !theme.id) return;
  const nome = theme.name || theme.id;
  // La conferma dice tutto ciò che cambia per il sito intero, non solo i template.
  if (!confirm(
    t('Importare il tema') + ' «' + nome + '»?\n\n'
    + t('Per tutto il sito cambiano: colori (anche della modalità scura), tipografia e font, spaziature, header, footer e pagina 404 attivi, cursore e mirino se il tema li porta, e la pagina iniziale, che mostrerà il template del tema. Si creano nuovi template e pagine; il menu del tema, se ne ha uno, prende il posto delle voci di un menu con lo stesso nome. Le pagine che esistono già non si duplicano: la pagina iniziale, quella degli articoli e le pagine che hanno già l\'indirizzo di una pagina del tema prendono il template e il titolo del tema e vengono pubblicate; se il tema ha una pagina degli articoli, diventa quella del sito.') + '\n\n'
    + t('Lo stile di prima resta fra le versioni dello stile: «Ripristina», subito dopo l\'import o da Configurazione › Palette › Versioni dello stile (da amministratore), rimette stile, header, footer, 404, cursore, pagina iniziale e degli articoli, e template, titolo e stato delle pagine che esistevano già. I template, le pagine nuove e le voci del menu restano.')
  )) return;

  picker && picker.setBusy(true);
  try {
    const res = await fetch(`${oloData.value.restUrl}themes/${theme.id}/import`, {
      method: 'POST',
      headers: { 'X-WP-Nonce': oloData.value.nonce, 'Content-Type': 'application/json' },
      credentials: 'same-origin'
    });
    const result = await res.json();
    if (result.templates) {
      picker && picker.close();
      // Il «Ripristina» compare dopo la ricarica qui sotto (App.vue → annunciaStileSostituito).
      ricordaStileSostituito(result.snapshot, nome);
      toast.success(t('Tema importato') + ': ' + result.templates.length + ' ' + t('template creati.'), 4000);
      // Genera subito le anteprime delle card (render REST → cattura), poi ricarica
      const ids = result.templates.map(tpl => tpl.id).filter(Boolean);
      if (ids.length && typeof window.oloGenerateMissingThumbs === 'function') {
        // Un solo toast col contatore visibile «i/total», aggiornato sul posto (è un
        // role=status: i lettori di schermo lo leggono). Resta fino alla ricarica qui sotto.
        const testo = t('Genero le anteprime dei template…');
        const avanzamento = toast.info(testo, 600000);
        try {
          await window.oloGenerateMissingThumbs(ids, {
            onProgress: (i, total) => { if (avanzamento) avanzamento.testo(testo + ' ' + i + '/' + total); },
          });
        } catch (e) { console.warn('thumb generation failed:', e); }
      }
      window.location.reload();
    } else {
      // Il selettore sta sopra i toast (z-index 999999, sfondo scuro e sfocato):
      // aperto, l'errore finiva sotto e non si leggeva. close() azzera `picker` (onClose).
      if (picker) picker.close();
      toast.error(t('Errore nell\'importazione') + (result && result.message ? ' — ' + result.message : ''), 6000);
    }
  } catch (e) {
    console.error('importTheme error:', e);
    if (picker) picker.close();
    toast.error(t('Errore nell\'importazione') + ' — ' + (e.message || t('errore di rete')), 6000);
  }
}

function open() {
  if (picker) { picker.close(); picker = null; return; }
  // Importazione disattivata (OLOBUILD_DISABLE_IMPORTS, come nella demo): i temi si sfogliano
  // soltanto. Il sottotitolo lo dice; un plugin esterno può darne uno suo
  // (oloExternalData.themesNotice, filtro olobuild_builder_localize_data).
  const soloSfoglia = !!oloData.value.importsDisabled;
  const avviso = (window.oloExternalData && typeof window.oloExternalData.themesNotice === 'string' && window.oloExternalData.themesNotice)
    || t('Su questo sito l\'importazione dei temi è disattivata: puoi sfogliarli e confrontarli.');
  picker = createThemePicker({
    mode: 'modal',
    card: { action: soloSfoglia ? 'browse' : 'import' },
    i18n: soloSfoglia ? { subtitle: avviso } : {},
    loadThemes: async () => {
      const res = await fetch(`${oloData.value.restUrl}themes`, {
        headers: { 'X-WP-Nonce': oloData.value.nonce },
        credentials: 'same-origin'
      });
      return await res.json();
    },
    onImport: importTheme,
    onClose: () => { picker = null; },
  });
  picker.open();
}

defineExpose({ open });
</script>
