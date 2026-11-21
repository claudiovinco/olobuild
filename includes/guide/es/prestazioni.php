<?php
/**
 * Guida «Prestazioni» (spagnolo). Solo HTML: titolo, descrizione e minuti
 * li stampa Olobuild_Guida::render_guida() dall'elenco delle guide.
 *
 * @package Olobuild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<nav class="olo-guida-toc" aria-label="En esta guía">
	<strong>En esta guía</strong>
	<ol>
		<li><a href="#da-solo">Lo que Olobuild hace solo</a></li>
		<li><a href="#impostazioni">Los ajustes Performance &amp; Cache</a></li>
		<li><a href="#cache">La caché de página completa</a></li>
		<li><a href="#esterne">Cachés externas y limpieza</a></li>
		<li><a href="#pagina">Una página rápida, en la práctica</a></li>
		<li><a href="#misura">Medir</a></li>
	</ol>
</nav>

<h2 id="da-solo">1. Lo que Olobuild hace solo</h2>
<p>Algunas optimizaciones están siempre activas y no tienen interruptor.</p>
<ul>
	<li><strong>Solo lo necesario.</strong> Las hojas de estilo y los scripts de Olobuild se cargan solo en las páginas hechas con Olobuild, y los scripts de una tile, por ejemplo los del mapa, solo si la tile está en la página.</li>
	<li><strong>Tipografías desde tu servidor.</strong> Las Google Fonts que usa el sitio se descargan una vez y las sirve el sitio, sin llamadas a Google en cada visita. Si la descarga falla, la página usa las tipografías del sistema.</li>
	<li><strong>Contenido bajo demanda.</strong> Después de la primera sección, el contenido se monta cuando está a punto de entrar en la pantalla. Las tiles de la primera sección, en cualquier caso las 3 primeras, los formularios, los mapas y los menús están siempre listos. A los buscadores y a los de las IA la página les llega entera: muchos no ejecutan JavaScript y no verían el contenido bajo demanda.</li>
	<li><strong>Imágenes perezosas.</strong> Las imágenes se cargan cuando están a punto de verse.</li>
	<li><strong>Vídeos ligeros.</strong> La tile Vídeo, con <span class="ui">Lazy Load (Facade)</span> activo como viene de serie, muestra la portada y carga el reproductor de YouTube o Vimeo solo al hacer clic.</li>
	<li><strong>Animaciones sin peso.</strong> Las animaciones de entrada empiezan cuando el elemento entra en la pantalla. Quien ha pedido a su sistema operativo que reduzca el movimiento ve los elementos enseguida, sin animación.</li>
</ul>

<h2 id="impostazioni">2. Los ajustes Performance &amp; Cache</h2>
<p>Están en <span class="ui">Sistema</span> › <span class="ui">Configuración</span> › <span class="ui">Rendimiento &amp; Servicios</span> › <span class="ui">Performance &amp; Cache</span>, y en la ficha Performance del panel.</p>
<table>
	<thead><tr><th>Ajuste</th><th>Qué hace</th><th>De serie</th></tr></thead>
	<tbody>
		<tr><td><strong>Defer JavaScript</strong></td><td>Los scripts de Olobuild se descargan sin frenar la página y arrancan cuando está lista.</td><td>Activo</td></tr>
		<tr><td><strong>Caché CSS en archivos estáticos</strong></td><td>Pone el estilo de cada plantilla en un archivo que el navegador conserva, en vez de reescribirlo en la página.</td><td>Activo</td></tr>
		<tr><td><strong>Minificar CSS</strong></td><td>Quita espacios y comentarios de las hojas de estilo de Olobuild.</td><td>Activo</td></tr>
		<tr><td><strong>Preload de tipografías propias</strong></td><td>Empieza enseguida la descarga de las tipografías del texto y de los títulos, con el peso usado: el texto aparece con su tipografía, sin cambiar de aspecto un instante después.</td><td>Activo</td></tr>
		<tr><td><strong>fetchpriority hero image</strong></td><td>La primera imagen de la primera sección se carga enseguida y con prioridad alta. Las demás siguen siendo perezosas.</td><td>Activo</td></tr>
		<tr><td><strong>Lazy load de vídeos alojados</strong></td><td>Los vídeos subidos al sitio, en reproducción automática y sin sonido, arrancan cuando entran en la pantalla.</td><td>Activo</td></tr>
		<tr><td><strong>Caché de página completa</strong></td><td>Guarda las páginas ya listas y se las sirve a los visitantes sin reconstruirlas. Lee el capítulo 3 antes de activarla.</td><td>Apagado</td></tr>
		<tr><td><strong>UIkit subset</strong></td><td>Reduce la hoja de estilo de UIkit a las partes que las páginas usan de verdad. Aprende visitando las páginas y se corrige solo.</td><td>Apagado</td></tr>
		<tr><td><strong>CSS por tile</strong></td><td>Carga el estilo de algunas familias de tiles solo donde hacen falta, como mapas, galerías y menús.</td><td>Apagado</td></tr>
		<tr><td><strong>Caché del navegador para medios (.htaccess)</strong></td><td>Le dice al navegador que guarde mucho tiempo imágenes, tipografías, CSS y JS. Funciona en servidores Apache y LiteSpeed.</td><td>Apagado</td></tr>
		<tr><td><strong>DNS prefetch &amp; preconnect automáticos</strong></td><td>Prepara por adelantado la conexión a YouTube y Vimeo en las páginas que los contienen. Los dominios escritos en Dominios propios salen siempre.</td><td>Activo</td></tr>
		<tr><td><strong>Limpieza del head</strong></td><td>Quita scripts y estilos de WordPress que muchos sitios no usan: jQuery Migrate, emoji, estilos de los bloques y del tema clásico.</td><td>Apagado</td></tr>
		<tr><td><strong>Critical CSS</strong></td><td>Escribe en la página el estilo de las primeras secciones (2, en <span class="ui">Secciones above-the-fold</span>) y carga el resto justo después. Se rehace pasados los días de <span class="ui">Duración de la caché</span> y cuando guardas la plantilla. <span class="ui">Regenerar critical CSS</span> lo reconstruye enseguida.</td><td>Apagado</td></tr>
	</tbody>
</table>
<div class="nota attenzione"><p><strong>Quitar el CSS de bloques</strong> quita también los estilos globales de WordPress. Con un tema de bloques, después de activarlo comprueba que el sitio siga teniendo sus colores y sus tipografías.</p></div>
<p>Arriba de la ficha, <span class="ui">Estado de la caché</span> cuenta las páginas en la caché de página completa, los archivos de estilo y las páginas con Critical CSS, y dice cuándo se vació la caché por última vez. <span class="ui">Vaciar todo</span> los borra todos a la vez, con el subset de UIkit y los datos temporales de Olobuild.</p>

<h2 id="cache">3. La caché de página completa</h2>
<p>La caché de página completa guarda cada página ya lista y se la sirve a los visitantes sin reconstruirla. Es el ajuste que más cuenta en un sitio con muchas visitas.</p>
<ul>
	<li>No vale para quien ha iniciado sesión, para el carrito y la caja de WooCommerce, para los sitemaps ni para las direcciones con parámetros distintos de los de seguimiento.</li>
	<li>Guarda versiones separadas para ordenador y móvil, y por idioma.</li>
	<li>Cada página se queda en caché 8 horas.</li>
	<li>Se vacía sola cuando guardas en el builder o en WordPress, cambias menús, tema, cabecera o pie activos, colores y tipografías globales, reglas de asignación o ajustes Performance, y con cada actualización de Olobuild.</li>
</ul>
<div class="nota attenzione"><p>No la actives si el sitio ya usa un plugin de caché como LiteSpeed Cache o WP Rocket. En ese caso Olobuild no la instala, y junto al interruptor la ficha dice por qué.</p></div>

<h2 id="esterne">4. Cachés externas y limpieza</h2>
<p>Olobuild no controla las cachés externas. Si el sitio usa LiteSpeed Cache, WP Rocket, la caché del alojamiento o Cloudflare, vacíala tú después de cambiar colores, tipografías, cabecera o pie: si no, los visitantes pueden seguir viendo la versión anterior.</p>
<p><span class="ui">Sistema</span> › <span class="ui">Herramientas</span> › <span class="ui">Caché de Olobuild</span> › <span class="ui">Borrar archivos y datos</span> borra los archivos de estilo generados, la caché de página completa y los datos temporales de Olobuild, que se vuelven a crear en la visita siguiente. Sirve cuando una página muestra un estilo antiguo. Hace lo mismo que <span class="ui">Vaciar todo</span> en la ficha Performance.</p>

<h2 id="pagina">5. Una página rápida, en la práctica</h2>
<ol class="passi">
	<li><strong>Imágenes del tamaño justo.</strong> Sube imágenes como mucho tan anchas como el espacio que ocupan, 2000 píxeles para una foto a todo el ancho, en WebP o JPEG comprimido.</li>
	<li><strong>La primera imagen enseguida.</strong> Con <span class="ui">fetchpriority hero image</span> activo, como viene de serie, la primera imagen de la primera sección arranca sola con prioridad alta. Si la foto principal está más abajo, en su tile Imagen abre <span class="ui">Avanzado</span> › <span class="ui">SEO &amp; Accesibilidad</span> y elige Eager en <span class="ui">Carga de la imagen</span> y High en <span class="ui">Fetch Priority</span>.</li>
	<li><strong>Pocas tipografías.</strong> Bastan 2 familias, con 3 o 4 pesos en total. Cada peso de más es un archivo más que descargar.</li>
	<li><strong>Vídeos con portada.</strong> Usa la tile Vídeo con Lazy Load (Facade) activo, en vez de pegar el código de YouTube en una tile HTML.</li>
	<li><strong>Efectos donde sirven.</strong> Los fondos animados, el parallax y los vídeos de fondo pesan más que una foto. Úsalos donde cuentan algo.</li>
</ol>

<h2 id="misura">6. Medir</h2>
<p>Mide la página con PageSpeed Insights de Google o con Lighthouse, en las herramientas para desarrolladores de Chrome. Haz la prueba en una ventana privada: con la sesión iniciada ves la página sin caché de página completa, más lenta que la de los visitantes.</p>
<p>Repite la medición 2 o 3 veces y mira sobre todo la carga del elemento más grande (LCP) y la estabilidad del diseño (CLS). La puntuación cambia de una prueba a otra, y de un móvil a otro.</p>
