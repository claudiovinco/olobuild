<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<style>
    #wpwrap { background: #f5f0eb; }
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
        overflow-y: auto;
    }
</style>
<div id="olobuilder-app"></div>
<script>
// bfcache busting: se il browser serve questa pagina dalla back-forward cache,
// forziamo un hard reload. Altrimenti dopo un deploy l'utente che torna alla pagina
// builder vedrebbe la versione cached con vecchio builder.js?ver=X.
window.addEventListener('pageshow', function(e) {
    if (e.persisted) window.location.reload();
});
</script>
