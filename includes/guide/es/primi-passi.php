<?php
/**
 * Guida «Primi passi» (spagnolo). Solo HTML: titolo, descrizione e minuti
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
		<li><a href="#configurazione">La primera configuración</a></li>
		<li><a href="#bacheca">El panel</a></li>
		<li><a href="#crea">Crear una página</a></li>
		<li><a href="#builder">El builder en 4 zonas</a></li>
		<li><a href="#elementi">Añadir y colocar los elementos</a></li>
		<li><a href="#pubblica">Guardar y publicar</a></li>
		<li><a href="#dispositivi">Ordenador, tableta y móvil</a></li>
		<li><a href="#scorciatoie">Atajos de teclado</a></li>
	</ol>
</nav>

<h2 id="configurazione">1. La primera configuración</h2>
<p>La primera vez que lo activas, Olobuild abre la configuración guiada, en 3 pasos. Si la has dejado para más tarde, el panel de bienvenida del Escritorio de WordPress la vuelve a abrir con <span class="ui">Iniciar la configuración</span>.</p>
<ol class="passi">
	<li><strong>Tema de WordPress.</strong> <span class="ui">Instalar y activar</span> pone Hello Olobuild, el tema ligero incluido en el plugin. Con <span class="ui">Usar el tema actual</span> mantienes el tema que ya tienes.</li>
	<li><strong>Elige un diseño.</strong> Elige uno de los 53 temas listos, o <span class="ui">Vacío</span>, y pulsa <span class="ui">Importar el tema elegido</span>. El tema crea páginas y menús, activa la cabecera y el pie, aplica sus colores y sus tipografías y fija la portada. Antes de importar, Olobuild guarda una instantánea del sitio, que puedes volver a poner con <span class="ui">Restaurar</span>.</li>
	<li><strong>Todo listo.</strong> <span class="ui">Abrir Olobuild</span> lleva al panel, <span class="ui">Ver el sitio</span> abre el sitio.</li>
</ol>
<div class="nota"><p>Con <strong>Vacío</strong> obtienes lo mínimo para empezar: una página Inicio fijada como portada, el menú «Menú principal», una «Cabecera básica» y un «Pie básico», ya activos.</p></div>

<h2 id="bacheca">2. El panel</h2>
<p>El panel se abre desde la entrada <span class="ui">Olobuild</span> del menú de WordPress. La barra superior es igual en todas las páginas de Olobuild y divide el trabajo en 4 áreas.</p>
<table>
	<thead><tr><th>Área</th><th>Qué contiene</th></tr></thead>
	<tbody>
		<tr><td><strong>Construye</strong></td><td>Bloques &amp; Páginas (las plantillas del sitio), Popups, Importar / Exportar</td></tr>
		<tr><td><strong>Medios</strong></td><td>Búsqueda de fotos y vídeos para poner en las páginas</td></tr>
		<tr><td><strong>Recoge</strong></td><td>Envíos de los formularios, Newsletter, Tracking &amp; Analytics</td></tr>
		<tr><td><strong>Sistema</strong></td><td>Configuración, Herramientas y esta Guía</td></tr>
	</tbody>
</table>
<p>En el panel encuentras <span class="ui">Continúa donde lo dejaste</span>, las <span class="ui">Acciones rápidas</span>, las fichas de <span class="ui">Gestión</span> y, a la derecha, el Centro de recursos con las novedades y las guías. El campo de búsqueda de arriba, o <kbd>Ctrl</kbd> <kbd>K</kbd>, busca páginas, plantillas y ajustes desde cualquier página de Olobuild.</p>

<h2 id="crea">3. Crear una página</h2>
<p><span class="ui">Crear página</span>, entre las Acciones rápidas, crea de una vez una plantilla «Sin título» y la página de WordPress conectada, las dos en borrador, y abre el builder. <span class="ui">Nueva página</span>, arriba en el panel, hace lo mismo.</p>
<p>También puedes empezar desde WordPress:</p>
<ul>
	<li>en la lista de Páginas, la acción <span class="ui">Crear con Olobuild</span> debajo del título de una página</li>
	<li>en la barra de administración, <span class="ui">Editar con Olobuild</span> mientras miras una página del sitio</li>
	<li>en el editor de WordPress, el recuadro <span class="ui">Olobuild</span>, donde también eliges la cabecera y el pie de la página</li>
</ul>
<div class="nota"><p>Ponle su título a la página antes de publicarla. El título se cambia con un clic en el nombre de la página, en la barra del builder. WordPress saca la dirección de la página del título en el momento de publicarla.</p></div>

<h2 id="builder">4. El builder en 4 zonas</h2>
<figure class="olo-schema">
	<div class="finestra" aria-hidden="true">
		<div class="z-barra"><span class="num">1</span>Barra superior</div>
		<div class="z-palette"><span class="num">2</span>Elementos<br>y Estructura</div>
		<div class="z-canvas"><span class="num">3</span>Canvas</div>
		<div class="z-inspector"><span class="num">4</span>Contenido · Estilo<br>· Avanzado</div>
	</div>
	<ol class="legenda">
		<li><span class="num">1</span><span><strong>Barra superior.</strong> Título, Ajustes de la página, dispositivos y zoom, Bloques &amp; Páginas, deshacer y rehacer, Vista previa, Guardar y Publicar.</span></li>
		<li><span class="num">2</span><span><strong>Panel izquierdo.</strong> <span class="ui">Elementos</span> reúne las tiles por categoría, con Recientes, Favoritos y Globales. <span class="ui">Estructura</span> muestra el árbol de la página.</span></li>
		<li><span class="num">3</span><span><strong>Canvas.</strong> La página como la verá el visitante, dibujada por el mismo motor que el sitio, con la cabecera y el pie.</span></li>
		<li><span class="num">4</span><span><strong>Panel derecho.</strong> Aparece cuando seleccionas un elemento: <span class="ui">Contenido</span> para textos, imágenes y enlaces, <span class="ui">Estilo</span> para el aspecto, <span class="ui">Avanzado</span> para visibilidad, efectos y posicionamiento.</span></li>
	</ol>
	<figcaption>Las 4 zonas del builder. El panel derecho también se abre con Ajustes de la página.</figcaption>
</figure>
<p>Cada página está hecha de secciones. Una sección contiene filas, una fila contiene columnas, y en las columnas están los elementos, que en Olobuild se llaman tiles.</p>
<figure class="olo-schema">
	<div class="albero" aria-hidden="true">
		<div class="l1">Sección <small>franja a todo el ancho, con su fondo</small></div>
		<div class="l2">Fila <small>divide la sección en columnas</small></div>
		<div class="l3">Columna <small>un ancho distinto para cada dispositivo</small></div>
		<div class="l4">Tile <small>título, texto, imagen, botón, galería…</small></div>
	</div>
	<figcaption>La jerarquía de una página, como la muestra el panel Estructura.</figcaption>
</figure>
<p>En el Estilo de una tile los controles están divididos en dos bloques. <strong>Elemento</strong> actúa sobre lo que la tile dibuja, por ejemplo el botón. <strong>Contenedor</strong> actúa sobre el recuadro que la tile ocupa en la cuadrícula, con sus espacios y su fondo.</p>

<h2 id="elementi">5. Añadir y colocar los elementos</h2>
<ol class="passi">
	<li><strong>Añade.</strong> Arrastra una tile del panel Elementos al canvas, o haz clic en su ficha: la tile se inserta después del elemento seleccionado, o al final de la página. En una página vacía usa <span class="ui">Añadir una tile</span> o <span class="ui">Elegir layout</span>.</li>
	<li><strong>Busca.</strong> <kbd>Ctrl</kbd> <kbd>K</kbd> abre el Finder, que encuentra una tile por su nombre sin recorrer las categorías.</li>
	<li><strong>Inserta una sección.</strong> El botón <span class="ui">+</span> entre dos secciones abre <span class="ui">Insertar una tile o una fila</span>, con una tile nueva, una fila vacía o un bloque listo de la biblioteca.</li>
	<li><strong>Mueve y duplica.</strong> Al pasar por encima de un elemento aparecen los comandos para arrastrarlo, subirlo o bajarlo, duplicarlo y eliminarlo. El botón derecho abre el menú completo, con Copiar, Pegar, Copiar estilo y Pegar estilo.</li>
	<li><strong>Escribe.</strong> Haz doble clic en un texto para editarlo directamente en el canvas. <kbd>Esc</kbd> cancela la edición en curso.</li>
</ol>
<div class="nota"><p>Junto al título de un campo o de una sección puede aparecer una <strong>(i)</strong>. Ábrela para leer para qué sirve el control: las explicaciones están ahí, así el panel queda ordenado.</p></div>

<h2 id="pubblica">6. Guardar y publicar</h2>
<p><span class="ui">Guardar</span> se activa cuando hay algo que guardar, y guarda a la vez la página, la cabecera y el pie si los has cambiado en el canvas. Olobuild no guarda solo: si cierras la pestaña con cambios sin guardar, el navegador pide confirmación.</p>
<p><span class="ui">Publicar</span> pone en línea la plantilla y la página de WordPress conectada, con su título.</p>
<div class="nota attenzione"><p>Después de publicar, cada <strong>Guardar</strong> va enseguida en línea: el sitio muestra siempre la última versión guardada. Para probar un cambio sin tocar el sitio, duplica la página en Bloques &amp; Páginas y trabaja en la copia.</p></div>
<p>Cada vez que guardas, Olobuild conserva la versión anterior. Las encuentras en <span class="ui">Historial de revisiones</span>, en la barra superior. La guía <a href="<?php echo esc_url( Olobuild_Guida::url( 'template' ) . '#revisioni' ); ?>">Plantillas</a> explica cómo recuperar una.</p>
<p><span class="ui">Vista previa</span> oculta los paneles y muestra la página como la ve el visitante. <span class="ui">Real</span> abre la página de verdad en una pestaña nueva. <kbd>Esc</kbd> vuelve a la edición.</p>

<h2 id="dispositivi">7. Ordenador, tableta y móvil</h2>
<p>Los botones de los dispositivos en la barra superior cambian el tamaño del canvas: Pantalla ancha, Escritorio, Tableta y Móvil. Tableta horizontal y Móvil horizontal se encienden en <span class="ui">Sistema</span> › <span class="ui">Configuración</span> › <span class="ui">Dispositivos del editor</span>. Muchos controles tienen un valor para cada dispositivo. Cuando cambias uno, cambias el valor del dispositivo elegido en la barra.</p>
<p>El zoom, junto a los dispositivos, encoge o amplía el canvas sin cambiar la página.</p>

<h2 id="scorciatoie">8. Atajos de teclado</h2>
<p>En Mac usa <kbd>Cmd</kbd> en lugar de <kbd>Ctrl</kbd>. En el builder la lista está siempre a mano: es el icono con forma de teclado de la barra superior.</p>
<table>
	<thead><tr><th>Teclas</th><th>Acción</th></tr></thead>
	<tbody>
		<tr><td><kbd>Ctrl</kbd> <kbd>S</kbd></td><td>Guardar</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Z</kbd> · <kbd>Ctrl</kbd> <kbd>Mayús</kbd> <kbd>Z</kbd></td><td>Deshacer · Rehacer, hasta 100 pasos</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>V</kbd> · <kbd>Ctrl</kbd> <kbd>D</kbd></td><td>Copiar · Pegar · Duplicar el elemento seleccionado</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>C</kbd> · <kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>V</kbd></td><td>Copiar estilo · Pegar estilo</td></tr>
		<tr><td><kbd>Supr</kbd> o <kbd>Retroceso</kbd></td><td>Eliminar el elemento seleccionado</td></tr>
		<tr><td><kbd>Ctrl</kbd> + clic</td><td>Seleccionar varios elementos</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>↑</kbd> · <kbd>Alt</kbd> <kbd>↓</kbd></td><td>Mover arriba · Mover abajo</td></tr>
		<tr><td><kbd>Alt</kbd> <kbd>←</kbd> · <kbd>Alt</kbd> <kbd>→</kbd></td><td>Mover a la columna de al lado</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>K</kbd></td><td>Buscar una tile (Finder)</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Alt</kbd> <kbd>P</kbd></td><td>Vista previa. <kbd>Esc</kbd> para salir</td></tr>
		<tr><td><kbd>Ctrl</kbd> <kbd>Mayús</kbd> <kbd>A</kbd></td><td>Asistente de IA, si está configurado</td></tr>
	</tbody>
</table>
