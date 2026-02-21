/**
 * Composable for WordPress Media Library integration.
 * Requires wp_enqueue_media() in PHP.
 */
export function useMediaPicker() {

  function openSingleImage(callback) {
    if (!window.wp || !window.wp.media) {
      alert('Libreria Media di WordPress non disponibile.');
      return;
    }

    const frame = wp.media({
      title: 'Seleziona immagine',
      button: { text: 'Usa questa immagine' },
      multiple: false,
      library: { type: 'image' },
    });

    frame.on('select', () => {
      const attachment = frame.state().get('selection').first().toJSON();
      callback({
        url: attachment.url,
        alt: attachment.alt || '',
        id: attachment.id,
      });
    });

    frame.open();
  }

  function openGallery(callback) {
    if (!window.wp || !window.wp.media) {
      alert('Libreria Media di WordPress non disponibile.');
      return;
    }

    const frame = wp.media({
      title: 'Seleziona immagini',
      button: { text: 'Aggiungi alla galleria' },
      multiple: true,
      library: { type: 'image' },
    });

    frame.on('select', () => {
      const images = frame.state().get('selection').map((att) => {
        const json = att.toJSON();
        return {
          url: json.url,
          alt: json.alt || '',
          id: json.id,
        };
      });
      callback(images);
    });

    frame.open();
  }

  return { openSingleImage, openGallery };
}
