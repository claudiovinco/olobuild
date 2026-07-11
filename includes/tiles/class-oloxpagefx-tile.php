<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * OLOX Page FX — decoratore a dimensione zero con gli effetti pagina fissi
 * del sito OLOtheme, tre varianti:
 * - scan: scanline ciano che segue lo scroll (pagina security).
 * - pano: panorama 360° fisso dietro tutta la pagina + bussola + gradi (pagina tour).
 * - xp:   barra XP fissa + toast "Level up!" (pagina tutor); somma i bonus del quiz.
 */
class Olobuild_OloxPageFx_Tile extends Olobuild_Olox_Base_Tile {

    protected $type     = 'oloxpagefx';
    protected $name     = 'OLOX — Effetti pagina (scan/pano/xp)';
    protected $icon     = 'dashicons-visibility';
    protected $category = 'marketing';
    protected $defaults = [
        'variant'   => 'scan',
        // pano
        'deg_label' => 'lo scroll ruota la vista',
        // xp
        'xp_label'  => 'corso · questa pagina',
        'xp_total'  => 540,
        'xp_cap'    => 630,
        'xp_step'   => 180,
    ];

    public function render( $settings, $style = [] ) {
        $s = wp_parse_args( $settings, $this->defaults );
        $this->olox_assets();
        $variant = $s['variant'] ?? 'scan';

        ob_start();
        echo $this->olox_open(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        if ( 'pano' === $variant ) :
            ?>
            <div class="ox-panobg" data-olox="pano">
                <div class="strip"></div>
                <div class="compassbar"><span class="cin"></span></div>
                <div class="degbox"><b class="ox-deg">0°</b><?php echo esc_html( $s['deg_label'] ); ?></div>
            </div>
        <?php elseif ( 'xp' === $variant ) : ?>
            <div class="xpfix" data-olox="xp"
                data-total="<?php echo (int) $s['xp_total']; ?>"
                data-cap="<?php echo (int) $s['xp_cap']; ?>"
                data-step="<?php echo (int) $s['xp_step']; ?>">
                <span><?php echo esc_html( $s['xp_label'] ); ?></span>
                <div class="bar"><i></i></div>
                <b><span class="ox-xpval">0</span> XP</b>
            </div>
            <div class="lvltoast">★ Level up!</div>
        <?php else : ?>
            <div class="ox-scan" data-olox="scan"></div>
        <?php endif;
        echo $this->olox_close(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return ob_get_clean();
    }
}
