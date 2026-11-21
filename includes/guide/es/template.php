<?php
/**
 * Guida «Template» (spagnolo). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#cosa">Plantillas y páginas</a></li>
		<li><a href="#tipi">Los tipos de plantilla</a></li>
		<li><a href="#elenco">Bloques &amp; Páginas</a></li>
		<li><a href="#header-footer">Qué cabecera ve una página</a></li>
		<li><a href="#regole">Reglas de asignación</a></li>
		<li><a href="#revisioni">Revisiones</a></li>
		<li><a href="#pronti">Bloques listos, temas y elementos globales</a></li>
		<li><a href="#import">Importar y exportar</a></li>
	</ol>
</nav>

<h2 id="cosa">1. Plantillas y páginas</h2>
<p>Una plantilla es el contenido construido con Olobuild: secciones, filas, columnas y tiles. Una página de WordPress muestra la plantilla conectada a ella, y la conexión se crea sola cuando creas la página desde Olobuild.</p>
<p>Una plantilla también se puede insertar en cualquier sitio donde WordPress acepte un shortcode. En Bloques &amp; Páginas un clic en el shortcode lo copia, con la forma <code>[olo_template id="12"]</code>.</p>
<div class="nota"><p>Si la página de WordPress acaba en la papelera, la próxima vez que guardes la plantilla Olobuild crea una nueva, conectada a la misma plantilla.</p></div>

<h2 id="tipi">2. Los tipos de plantilla</h2>
<table>
	<thead><tr><th>Tipo</th><th>Para qué sirve</th></tr></thead>
	<tbody>
		<tr><td><strong>Página</strong></td><td>El contenido de una página del sitio.</td></tr>
		<tr><td><strong>Header</strong></td><td>La cabecera con el logo y el menú, arriba de las páginas.</td></tr>
		<tr><td><strong>Footer</strong></td><td>El pie, al final de las páginas.</td></tr>
		<tr><td><strong>Single</strong></td><td>El diseño común a todos los contenidos de un tipo, por ejemplo cada artículo o cada producto. Se crea desde Plantilla Single, eligiendo el tipo de contenido.</td></tr>
		<tr><td><strong>Mega Panel</strong></td><td>El contenido de un panel del mega menú.</td></tr>
		<tr><td><strong>Widget</strong></td><td>Un contenido reutilizable dentro de fichas, pestañas y sliders.</td></tr>
		<tr><td><strong>404</strong></td><td>La página que aparece cuando una dirección no existe.</td></tr>
	</tbody>
</table>

<h2 id="elenco">3. Bloques &amp; Páginas</h2>
<p><span class="ui">Construye</span> › <span class="ui">Bloques &amp; Páginas</span> enumera todas las plantillas del sitio. Los filtros de arriba muestran un tipo cada vez, y cada ficha dice su estado: Borrador, Publicado o Activo. Desde las acciones de una ficha puedes editar, duplicar, exportar y eliminar la plantilla.</p>
<div class="nota attenzione"><p><strong>Eliminar</strong> borra la plantilla para siempre. La página de WordPress conectada se queda, pero ya no muestra el contenido hecho con Olobuild.</p></div>

<h2 id="header-footer">4. Qué cabecera ve una página</h2>
<p>Cada página muestra una cabecera y un pie. Olobuild los elige en este orden, y se detiene en la primera respuesta que encuentra.</p>
<figure class="olo-schema">
	<div class="flusso">
		<div><strong>1. La elección de la página</strong>El recuadro Olobuild en el editor de WordPress, con los menús Header y Footer.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>2. Las reglas de asignación</strong>Por ejemplo una cabecera distinta para los artículos del blog.</div>
		<div class="freccia" aria-hidden="true"><?php echo Olobuild_Guida::icona( 'freccia', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG statico. ?></div>
		<div><strong>3. Cabecera y pie activos</strong>Los que valen para todo el sitio.</div>
	</div>
	<figcaption>Con «Predeterminado (global)» en el recuadro de la página, deciden las reglas o, si no hay, la cabecera y el pie activos.</figcaption>
</figure>
<p>Para activar una cabecera o un pie, ábrelo en el builder, publícalo y pulsa <span class="ui">Activar</span> en la barra de arriba. Lo mismo vale para una plantilla Single y para la página 404.</p>
<p>En el canvas de una página, la cabecera y el pie dicen de qué plantilla vienen y si también los usan otras páginas. <span class="ui">Abrir la plantilla</span> los abre en el builder, y luego un botón te devuelve a la página de la que partiste.</p>

<h2 id="regole">5. Reglas de asignación</h2>
<p>Las reglas deciden qué cabecera, pie, Single o archivo usar en una parte del sitio. Están en <span class="ui">Sistema</span> › <span class="ui">Configuración</span> › <span class="ui">Contenidos &amp; Plantillas</span> › <span class="ui">Asignación de plantillas</span>.</p>
<ol class="passi">
	<li><strong>Crea la regla.</strong> Pulsa <span class="ui">Nueva regla</span>, elige el <span class="ui">Contexto</span> (Header, Footer, Single o Archive) y la plantilla que se usará.</li>
	<li><strong>Añade las condiciones.</strong> Con <span class="ui">Añadir condición</span> eliges dónde vale la regla, y en cada condición si incluir o excluir. El valor se elige de un menú.</li>
	<li><strong>Combina.</strong> <span class="ui">Operador de condiciones</span> dice si se tienen que cumplir todas las condiciones o al menos una.</li>
	<li><strong>Da la prioridad.</strong> Cuando dos reglas valen para la misma página, gana la que tiene el número de <span class="ui">Prioridad</span> más bajo. El valor de serie es 10.</li>
</ol>
<p>Las condiciones disponibles son: Todo el sitio, Solo la portada, Todas las páginas individuales, Página, Artículo, Tipo de contenido, Archivo, Categoría, Etiqueta, Usuario conectado, Usuario no conectado, Rol de usuario, Página 404, Página de búsqueda, y 4 condiciones para WooCommerce.</p>
<div class="nota"><p>Sin reglas valen la cabecera y el pie activos. Una regla solo hace falta donde quieres algo distinto.</p></div>

<h2 id="revisioni">6. Revisiones</h2>
<p>Cada vez que guardas, Olobuild conserva la versión anterior de la plantilla, hasta 50 versiones. Las encuentras en el builder, en <span class="ui">Historial de revisiones</span>, con la fecha y el peso.</p>
<ol class="passi">
	<li>Abre <span class="ui">Historial de revisiones</span> desde la barra de arriba.</li>
	<li>Pulsa <span class="ui">Restaurar</span> en la versión que necesitas: el canvas la carga.</li>
	<li>Comprueba la página y pulsa <span class="ui">Guardar</span>. Mientras no guardes, el sitio sigue como estaba.</li>
</ol>

<h2 id="pronti">7. Bloques listos, temas y elementos globales</h2>
<p>El botón <span class="ui">Bloques &amp; Páginas</span> de la barra del builder abre la biblioteca: 89 bloques, 58 páginas enteras y las secciones que has guardado tú con <span class="ui">Guardar sección como plantilla</span> desde el botón derecho. Una página entera se puede usar de dos formas: <span class="ui">Reemplazar todo</span> o <span class="ui">Añadir al final</span>.</p>
<p><span class="ui">Temas del sitio</span> abre los mismos temas de la primera configuración. Un tema cambia todo el sitio, y antes de que confirmes la ventana enumera todo lo que va a cambiar.</p>
<p>Un elemento se puede hacer <strong>global</strong> desde el botón derecho, con <span class="ui">Guardar como global</span>. Un elemento global se inserta desde la categoría Globales del panel Elementos y es el mismo en todas las páginas que lo usan. <span class="ui">Desvincular global</span> lo convierte en una copia independiente.</p>

<h2 id="import">8. Importar y exportar</h2>
<p><span class="ui">Construye</span> › <span class="ui">Importar / Exportar</span> lleva el trabajo de un sitio a otro con archivos <code>.json</code>.</p>
<ul>
	<li><strong>Exportar plantilla</strong> guarda una plantilla, también con sus imágenes si eliges <span class="ui">Incluir medios</span>.</li>
	<li><strong>Exportar sitio completo</strong> guarda plantillas, páginas, portada, menús, medios, estilos y opciones globales.</li>
	<li><strong>Importar</strong> carga un archivo exportado desde otro sitio Olobuild.</li>
</ul>
<div class="nota attenzione"><p>Importar un sitio completo <strong>sustituye</strong> los colores, las tipografías y las opciones globales del sitio que lo recibe. Exporta antes el sitio actual, si quieres poder volver atrás.</p></div>
<p>En Bloques &amp; Páginas, <span class="ui">Seleccionar</span> y luego <span class="ui">Exportar tema</span> ponen varias plantillas en un solo archivo. En el builder, la importación de un archivo JSON crea una plantilla nueva en borrador y deja intacta la que está abierta.</p>
