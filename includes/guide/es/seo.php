<?php
/**
 * Guida «SEO e Open Graph» (spagnolo). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#dove">Dónde se configura</a></li>
		<li><a href="#titolo">Título y descripción</a></li>
		<li><a href="#analisi">Análisis de la página</a></li>
		<li><a href="#social">Vista previa en redes sociales</a></li>
		<li><a href="#robots">Indexación y canonical</a></li>
		<li><a href="#schema">Datos estructurados</a></li>
		<li><a href="#redirect">Redirecciones y 404</a></li>
		<li><a href="#sitemap">Sitemap</a></li>
		<li><a href="#verifica">Search Console y Bing</a></li>
		<li><a href="#plugin">Con Yoast, Rank Math y similares</a></li>
	</ol>
</nav>

<h2 id="dove">1. Dónde se configura</h2>
<p>Olobuild escribe en la cabecera de cada página del sitio el título, la descripción, la vista previa social y los datos estructurados. Los configuras página por página, en dos sitios que guardan los mismos datos.</p>
<ul>
	<li><strong>En el builder:</strong> <span class="ui">Ajustes de la página</span> › <span class="ui">SEO</span>, con las pestañas Básico, Social, Robots, Schema y FAQ. El grupo se guarda solo, sin pulsar Guardar, y aparece cuando la plantilla está conectada a una página.</li>
	<li><strong>En el editor de WordPress:</strong> el recuadro <span class="ui">Olobuild SEO</span>, presente en páginas, entradas y los demás contenidos públicos, con la vista previa del resultado en Google.</li>
</ul>
<p>En las listas de páginas y entradas, la columna SEO muestra con un punto cuántos de los campos principales están rellenados.</p>
<p>Los valores que valen para todo el sitio están en <span class="ui">Sistema</span> › <span class="ui">Configuración</span> › <span class="ui">SEO &amp; Privacidad</span> › <span class="ui">SEO global</span>: el patrón y el separador del título, la descripción predeterminada, la imagen para las redes sociales, el perfil de X y el tipo de quien publica el sitio. Una página con sus campos rellenados gana siempre.</p>

<h2 id="titolo">2. Título y descripción</h2>
<p><span class="ui">SEO Title</span> es el título que aparece en la pestaña del navegador y en los resultados de búsqueda. El contador indica 60 caracteres, la longitud que Google muestra entera. Si lo dejas vacío, vale el patrón de la pestaña SEO global: de serie, el título de la página, el separador y el nombre del sitio.</p>
<p><span class="ui">Meta Description</span> es el texto debajo del título en los resultados, con un contador de 160 caracteres. Si falta, Olobuild usa las primeras 30 palabras del resumen de la página, luego la descripción predeterminada de la pestaña SEO global y, si también falta esa, el lema del sitio.</p>
<p><span class="ui">Focus keyword</span> es la palabra clave principal de la página, es decir, las palabras que usaría quien la busca. El análisis de la página comprueba dónde aparece.</p>

<h2 id="analisi">3. Análisis de la página</h2>
<p>Debajo de la Focus keyword, en la pestaña Básico, <span class="ui">Análisis de la página</span> lee la página como la ven Google y los asistentes de IA: el texto de las tiles, los títulos, las imágenes y los enlaces. La cabecera y el pie quedan fuera. Los cambios cuentan enseguida, incluso antes de guardar; el botón con la flecha vuelve a hacer el análisis.</p>
<ul>
	<li><strong>Por corregir</strong>: lo que quita la página de los resultados o la deja sin título principal, como el noindex encendido, todo el sitio oculto para los buscadores, ningún título H1.</li>
	<li><strong>Por mejorar</strong>: título o descripción demasiado largos o cortos, más de un H1, títulos que se saltan un nivel (de H2 a H4), poco texto, imágenes sin texto alternativo, ningún enlace hacia las otras páginas del sitio, la palabra clave ausente de los puntos que cuentan o ya usada por otra página.</li>
	<li><strong>Superados</strong> y <strong>Títulos de la página</strong> se abren con un clic: el segundo es el índice de los títulos H1–H6, con los que hay que arreglar marcados.</li>
</ul>
<p>La (i) junto a cada elemento explica cómo arreglarlo. El análisis no cuenta cuántas veces repites la palabra clave: Google ya no lo mira, lo que cuenta es que la página hable de verdad de eso. En el recuadro <span class="ui">Olobuild SEO</span> del editor de WordPress está el mismo análisis, sobre la página guardada.</p>

<h2 id="social">4. Vista previa en redes sociales</h2>
<p>Cuando alguien comparte la página en Facebook, LinkedIn, WhatsApp o X, la vista previa nace de los datos Open Graph. Los encuentras en la pestaña Social.</p>
<ul>
	<li><span class="ui">OG Title</span> y <span class="ui">OG Description</span> cambian el título y el texto solo para las redes sociales. Vacíos, valen los de la pestaña Básico.</li>
	<li><span class="ui">OG Image</span> es la imagen de la vista previa. El tamaño adecuado es 1200 × 630 píxeles.</li>
	<li>Los campos para <span class="ui">Twitter / X</span> solo sirven si quieres un texto distinto: vacíos, X usa los de Open Graph.</li>
</ul>
<p>Sin OG Image, Olobuild usa la imagen destacada de la página, luego la imagen predeterminada de la pestaña SEO global y por último el logo del sitio. El formato de la vista previa de X se elige en la misma pestaña. Sin imagen, la vista previa es compacta de todos modos.</p>
<div class="nota"><p>Las redes sociales conservan la vista previa durante días. Después de cambiarla, actualízala con la herramienta de depuración de la plataforma, por ejemplo el Sharing Debugger de Facebook.</p></div>

<h2 id="robots">5. Indexación y canonical</h2>
<p><span class="ui">Robots default</span> de la pestaña SEO global vale para todo el sitio y es la misma elección que Ajustes › Lectura de WordPress. Para una sola página, en la pestaña Robots, <span class="ui">noindex</span> pide a los buscadores que no muestren la página en los resultados. Los enlaces de la página se siguen igualmente, salvo que actives también <span class="ui">nofollow</span>. Usa noindex para páginas de servicio, como una página de agradecimiento después de un formulario.</p>
<p><span class="ui">Canonical URL</span> indica la dirección principal de una página que existe en varias versiones. Vacía, vale la dirección de la propia página, y es la elección correcta casi siempre.</p>

<h2 id="schema">6. Datos estructurados</h2>
<p>Los datos estructurados describen la página a los buscadores en un formato que leen sin interpretar el texto. Olobuild los escribe solo:</p>
<ul>
	<li>en todas las páginas, quién publica el sitio, con el logo y los perfiles sociales: el tipo (organización, persona, hotel…) se elige en la pestaña SEO global</li>
	<li>en la portada también el propio sitio, con su búsqueda interna</li>
	<li>en las demás páginas, la ruta de navegación (breadcrumb)</li>
	<li>en las entradas el tipo Artículo, en las páginas el tipo Página, en los productos de WooCommerce el tipo Producto</li>
</ul>
<p>Todos estos datos salen como un solo grafo, conectados entre sí: la página forma parte del sitio, el artículo es el contenido de la página, el sitio y el artículo los publica quien publica el sitio. Así es como los buscadores y las IA reconocen quién eres.</p>
<p>En la pestaña FAQ, <span class="ui">Añadir FAQ</span> reúne preguntas y respuestas que se convierten en datos de tipo FAQ, leídos por buscadores y asistentes de IA. Google solo muestra las FAQ enriquecidas en los resultados para sitios gubernamentales y sanitarios. La tile Acordeón tiene el mismo interruptor, <span class="ui">Schema FAQ (SEO)</span>, y la tile Mapa puede describir el lugar con <span class="ui">Schema.org JSON-LD (SEO)</span>.</p>
<p>En <span class="ui">Schema.org type</span> dos tipos tienen sus propios campos, que aparecen debajo de la elección:</p>
<ul>
	<li><strong>Event</strong>, para un concierto, un espectáculo o un curso: inicio y fin, presencial o en línea, lugar y dirección, estado (programado, aplazado, cancelado), organizador, precio de la entrada y dónde se reserva.</li>
	<li><strong>SoftwareApplication</strong>, para un programa o una app: categoría, en qué funciona, versión y precio, 0 si es gratis.</li>
</ul>
<p>El análisis de la página señala los campos que faltan, como un evento sin fecha de inicio.</p>
<div class="nota"><p>Los demás tipos, por ejemplo Recipe o HowTo, producen solo el nombre, la descripción y la imagen. Para los datos que hacen falta para los resultados enriquecidos de Google, escríbelos en <span class="ui">JSON-LD personalizado</span>, que Olobuild comprueba antes de publicarlo.</p></div>

<h2 id="redirect">7. Redirecciones y 404</h2>
<p>Cuando una página cambia de dirección, una redirección lleva a quien llega por el enlace antiguo a la página nueva. La gestión está en <span class="ui">Sistema</span> › <span class="ui">Configuración</span> › <span class="ui">SEO &amp; Privacidad</span> › <span class="ui">Redirecciones &amp; 404</span>, y el panel señala las direcciones en 404 que todavía no tienen redirección.</p>
<ol class="passi">
	<li><strong>Añade la redirección.</strong> <span class="ui">Añadir redirección</span>, luego la dirección antigua en <span class="ui">Desde la URL</span> y la nueva en <span class="ui">A la URL</span>.</li>
	<li><strong>Elige el tipo.</strong> 301 para un traslado definitivo, el caso más común. 302 y 307 para uno temporal. 410 para decir que la página se ha quitado y no volverá.</li>
	<li><strong>Revisa los 404.</strong> El registro de 404 enumera las direcciones pedidas que no existen, hasta 500. <span class="ui">Crear redirección</span> en una fila prepara una redirección 301: corrige el destino antes de guardarla.</li>
</ol>
<p>Para trasladar un grupo de direcciones, empieza <span class="ui">Desde la URL</span> con <code>~</code> y escribe una expresión regular. <code>$1</code> en el destino recoge la parte capturada.</p>
<div class="nota attenzione"><p>Una redirección vale también cuando la dirección de partida es una página que existe. Antes de crear una en una dirección en uso, comprueba que esa página ya no haga falta.</p></div>
<p>La vista IndexNow avisa a Bing y a los demás buscadores que participan cuando publicas o actualizas una página, también desde el builder. <span class="ui">Generar</span> crea una clave, y Olobuild publica por su cuenta el archivo de verificación que los buscadores comprueban: el enlace aparece debajo de la clave después de guardar.</p>

<h2 id="sitemap">8. Sitemap</h2>
<p>El sitemap enumera las páginas del sitio para los buscadores. Es el de WordPress, en la dirección <code>/wp-sitemap.xml</code>: Olobuild quita de él las páginas en noindex, y el interruptor Sitemap XML de la pestaña SEO global lo apaga. Indícalo en Google Search Console. Si usas un plugin de SEO, vale el suyo.</p>

<h2 id="verifica">9. Search Console y Bing</h2>
<p>Google Search Console y Bing Webmaster Tools dicen en qué búsquedas sale el sitio, cuántas veces se hace clic en él y si hay páginas que los buscadores no consiguen leer. Bing vale también para Copilot. Para entrar tienes que demostrar que el sitio es tuyo, y hay dos caminos.</p>
<p><strong>Con el DNS, el mejor camino.</strong> En la <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Search Console</a> elige la propiedad «Dominio» y escribe el dominio, por ejemplo <code>misitio.es</code>. Google te da un registro TXT que hay que añadir en el DNS del dominio, desde el panel de quien te lo gestiona. Una vez verificado, vale para todo el dominio, subdominios y carpetas incluidos, y en Olobuild no hay nada que rellenar. Cuando Olobuild encuentra ese código en el DNS, lo escribe en <span class="ui">Sistema</span> › <span class="ui">Configuración</span> › <span class="ui">SEO &amp; Privacidad</span> › <span class="ui">SEO global</span> › <span class="ui">Verificación de los buscadores</span>, debajo del campo de Google: «Código de Google encontrado en el DNS».</p>
<p><strong>Con el código en la cabecera, si no puedes tocar el DNS.</strong></p>
<ol class="passi">
	<li><strong>Añade el sitio.</strong> En la Search Console elige «Prefijo de la URL», escribe la dirección del sitio y como verificación «Etiqueta HTML».</li>
	<li><strong>Copia el código.</strong> Google muestra una metaetiqueta: cópiala entera, o solo el código entre las comillas de content.</li>
	<li><strong>Pégalo.</strong> En <span class="ui">Verificación de los buscadores</span>, en el campo Google Search Console, y luego Guardar. Olobuild se queda solo con el código y lo escribe en la cabecera de cada página.</li>
	<li><strong>Verifica.</strong> Vuelve a la Search Console y pulsa Verificar.</li>
</ol>
<p>En los dos casos, luego añade el sitemap, <code>/wp-sitemap.xml</code>, en la sección Sitemaps de la Search Console. Si el sitio tiene otros sitios WordPress en carpetas, como <code>misitio.es/tienda</code>, cada uno tiene su sitemap: añádelos todos.</p>
<p>Para <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a> el camino más corto es importar el sitio ya verificado desde la Search Console: se lleva también los sitemaps. Si no, está el campo Bing, como para Google.</p>

<h2 id="plugin">10. Con Yoast, Rank Math y similares</h2>
<p>Con Yoast SEO, Rank Math, All in One SEO o SEOPress activos, Olobuild se aparta: no escribe el título, la descripción, Open Graph, el canonical ni los datos estructurados, para que la página no los reciba dos veces. En ese caso rellena los campos en el panel del plugin de SEO. Los de Olobuild se quedan guardados pero no se usan.</p>
<p>Las versiones de la página en otros idiomas y sus enlaces para los buscadores son tarea de OLOlang.</p>
