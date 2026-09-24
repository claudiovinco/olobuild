<?php
/**
 * Olo Tema Prova — l'unico template: il loop dentro .entry-content, come un tema classico.
 * Il foglio di stile si collega qui (niente functions.php: al banco basta questo).
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<main id="contenuto">
<?php
while ( have_posts() ) :
	the_post();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;
?>
</main>
<?php wp_footer(); ?>
</body>
</html>
