import { ref } from 'vue';

/**
 * useGlobalPanels — i pannelli GLOBALI del sito aperti da dentro l'inspector.
 *
 * Gli stili tipografici globali si potevano creare soltanto in wp-admin
 * (Olobuild → Sistema → Tipografia): il builder aveva già il pannello
 * `GlobalTypographyPanel.vue`, completo e capace di salvare via REST, ma non era
 * importato da NESSUN file. Il select «Stile tipografico» elencava quindi set che
 * non c'era modo di creare da lì, e il popover rimandava a «Stili globali →
 * Tipografia», un posto che nel builder non esiste.
 *
 * Lo stato sta qui, fuori dai componenti, perché ad aprirlo sono i campi
 * (InspectorField, FieldTypography) mentre a mostrarlo è l'inspector: un
 * modale solo, non uno per campo.
 */
const typographyOpen = ref(false);

export function useGlobalPanels() {
  return {
    typographyOpen,
    openTypography() { typographyOpen.value = true; },
    closeTypography() { typographyOpen.value = false; },
  };
}
