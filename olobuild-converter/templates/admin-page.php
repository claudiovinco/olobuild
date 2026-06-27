<?php
/**
 * Admin page template: Tools > OloBuild Converter
 *
 * @var Olo_Converter_Interface[] $converters
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap olo-converter-wrap">
    <h1>OloBuild Converter</h1>
    <p class="description">Converti template da altri page builder nel formato OloBuild.</p>

    <!-- Builder tabs -->
    <nav class="nav-tab-wrapper olo-converter-tabs">
        <?php $first = true; ?>
        <?php foreach ( $converters as $slug => $converter ) : ?>
            <a href="#olo-tab-<?php echo esc_attr( $slug ); ?>"
               class="nav-tab <?php echo $first ? 'nav-tab-active' : ''; ?>"
               data-tab="<?php echo esc_attr( $slug ); ?>">
                <?php echo esc_html( $converter->get_source_name() ); ?>
                <?php if ( $converter->is_source_installed() ) : ?>
                    <span class="olo-badge olo-badge-ok">Installato</span>
                <?php else : ?>
                    <span class="olo-badge olo-badge-off">Non rilevato</span>
                <?php endif; ?>
            </a>
            <?php $first = false; ?>
        <?php endforeach; ?>
    </nav>

    <!-- Tab panels -->
    <?php $first = true; ?>
    <?php foreach ( $converters as $slug => $converter ) : ?>
        <div id="olo-tab-<?php echo esc_attr( $slug ); ?>"
             class="olo-tab-panel <?php echo $first ? 'olo-tab-active' : ''; ?>"
             data-builder="<?php echo esc_attr( $slug ); ?>">

            <h2><?php echo esc_html( $converter->get_source_name() ); ?></h2>

            <!-- Input mode -->
            <fieldset class="olo-input-mode">
                <legend>Sorgente dati</legend>
                <label>
                    <input type="radio" name="mode_<?php echo esc_attr( $slug ); ?>"
                           value="file" checked class="olo-mode-radio">
                    Carica file esportato (.json)
                </label>
                <label>
                    <input type="radio" name="mode_<?php echo esc_attr( $slug ); ?>"
                           value="db" class="olo-mode-radio"
                           <?php echo ! $converter->is_source_installed() ? 'disabled' : ''; ?>>
                    Leggi dal database
                    <?php if ( ! $converter->is_source_installed() ) : ?>
                        <span class="description">(richiede <?php echo esc_html( $converter->get_source_name() ); ?> attivo)</span>
                    <?php endif; ?>
                </label>
            </fieldset>

            <!-- File upload -->
            <div class="olo-file-section olo-mode-section" data-mode="file">
                <div class="olo-dropzone" id="dropzone-<?php echo esc_attr( $slug ); ?>">
                    <p>Trascina qui il file .json esportato da <?php echo esc_html( $converter->get_source_name() ); ?></p>
                    <p>oppure</p>
                    <button type="button" class="button olo-browse-btn">Scegli file</button>
                    <input type="file" accept=".json" class="olo-file-input" style="display:none">
                    <div class="olo-file-info" style="display:none">
                        <span class="olo-file-name"></span>
                        <span class="olo-file-size"></span>
                        <button type="button" class="button-link olo-file-remove">Rimuovi</button>
                    </div>
                </div>
            </div>

            <!-- DB selector -->
            <div class="olo-db-section olo-mode-section" data-mode="db" style="display:none">
                <label for="page-select-<?php echo esc_attr( $slug ); ?>">Seleziona pagina:</label>
                <select id="page-select-<?php echo esc_attr( $slug ); ?>"
                        class="olo-page-select" style="width:100%;max-width:500px;">
                    <option value="">— Caricamento... —</option>
                </select>
            </div>

            <!-- Convert button -->
            <p class="olo-actions">
                <button type="button" class="button button-primary button-hero olo-convert-btn" disabled>
                    Converti in OloBuild
                </button>
            </p>

            <!-- Progress -->
            <div class="olo-progress" style="display:none">
                <span class="spinner is-active"></span>
                <span class="olo-progress-text">Conversione in corso...</span>
            </div>

            <!-- Results -->
            <div class="olo-results" style="display:none">
                <div class="olo-result-summary notice notice-success">
                    <p class="olo-result-message"></p>
                </div>

                <!-- Report summary -->
                <table class="widefat olo-report-summary">
                    <thead>
                        <tr>
                            <th>Convertiti</th>
                            <th>Approssimati</th>
                            <th>Fallback HTML</th>
                            <th>Saltati</th>
                            <th>Avvisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="olo-count-converted">0</td>
                            <td class="olo-count-approximated">0</td>
                            <td class="olo-count-fallback">0</td>
                            <td class="olo-count-skipped">0</td>
                            <td class="olo-count-warnings">0</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Report details (expandable) -->
                <details class="olo-report-details">
                    <summary>Dettagli conversione</summary>
                    <table class="widefat striped olo-report-items">
                        <thead>
                            <tr>
                                <th>Sorgente</th>
                                <th>OloBuild</th>
                                <th>Stato</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </details>

                <!-- Actions -->
                <p class="olo-result-actions">
                    <button type="button" class="button button-primary olo-import-btn">
                        Importa in OloBuild
                    </button>
                    <button type="button" class="button olo-download-btn">
                        Scarica JSON
                    </button>
                </p>
            </div>
        </div>
        <?php $first = false; ?>
    <?php endforeach; ?>
</div>
