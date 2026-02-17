<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style>
    #wpwrap { background: #1e1e2e; }
    #wpcontent { padding-left: 0 !important; }
    #wpbody-content { padding-bottom: 0; }
    #wpfooter { display: none; }
    #adminmenumain { display: none; }
    #wpadminbar { display: none; }
    html.wp-toolbar { padding-top: 0 !important; }
    #olobuilder-app {
        width: 100vw;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 99999;
    }
</style>
<div id="olobuilder-app"></div>
