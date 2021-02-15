<?php
/**
 * This is the template that displays all of the <head> section.
 *
 * @link https://livecomposerplugin.com/themes/
 *
 * @package LC Blank
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="ultima homenagem covid-19 covid corona coronavirus prefeitura" />
	<meta name="description" content="A Última Homenagem - iniciativa da Prefeitura de São Paulo para homenagear as vítimas acometidas pela COVID-19">

	<title><?php lct_title('|'); ?></title>

	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php if (is_user_logged_in()) {
		echo "<div class='link-painel'><a href='".get_home_url()."/painel'><span>Painel de moderação de homenagens</span></a></div>";
	} ?>
	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>

<?php

if ( function_exists( 'dslc_hf_get_header' ) ) {
	echo dslc_hf_get_header();
}

?>
