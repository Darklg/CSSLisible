<?php
define('DIR_SITE', dirname(__FILE__));
include DIR_SITE . '/inc/header.php';
?>
<!doctype html>
<html lang="<?php echo $lang; ?>">
    <head>
        <meta charset="utf-8"/>
        <title><?php echo TITRE_SITE . ' - ' . _(SLOGAN_SITE); ?></title>
        <meta name="viewport" content="width=790" />
        <link rel="stylesheet" href="<?php echo URL_SITE; ?>css/main.css?t=201208181531" type="text/css" />
        <?php foreach($more_languages as $llang => $language){
            $urlLang = URL_SITE . '?lang=' . $llang;
            ?><link rel="alternate" hreflang="<?php echo $llang; ?>" href="<?php echo $urlLang; ?>" /><?php
        } ?>
    </head>
    <body class="no-js">
    <div id="main-container">
        <div id="header">
            <?php include DIR_SITE . '/inc/tpl/lang_menu.php'; ?>
            <h1><?php echo TITRE_SITE . ' - ' . _(SLOGAN_SITE); ?></h1>
        </div>
        <form id="main-form" action="" method="post" enctype="multipart/form-data">

            <?php include DIR_SITE . '/inc/tpl/text.php'; ?>
            <?php include DIR_SITE . '/inc/tpl/errors.php'; ?>
            <?php include DIR_SITE . '/inc/tpl/tabs.php'; ?>

            <div id="options_block">
                <div class="the_grid gri-4-3-3">
                    <fieldset>
                        <legend id="titre-formatage"><?php echo _('Formatage'); ?></legend>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/distance_selecteurs.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/type_indentation.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/type_separateur.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/colors_format.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/hex_colors_format.php'; ?>
                        &nbsp;</fieldset>
                    <fieldset>
                        <legend id="titre-presentation"><?php echo _('Présentation'); ?></legend>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/selecteurs_multiples_separes.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/valeurs_multiples_separees.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/supprimer_selecteurs_vides.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/raccourcir_valeurs.php'; ?>
                    &nbsp;</fieldset>
                    <fieldset>
                        <legend><?php echo _('Options avancées'); ?></legend>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/tout_compresse.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/selecteur_par_ligne.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/return_file.php'; ?>
                        <?php include DIR_SITE . '/inc/tpl/formulaire/add_header.php'; ?>
                    &nbsp;</fieldset>
                </div>
            </div>
            <?php include DIR_SITE . '/inc/tpl/submit.php'; ?>
        </form>
    </div>
    <div id="footer">
        <?php $readme_suffix = (isset($_GET['lang']) && $_GET['lang'] == 'en') ? '_en' : ''; ?>
        <a target="_blank" href="https://github.com/Darklg/CSSLisible/blob/master/README<?php echo $readme_suffix; ?>.md"><?php echo _('Documentation'); ?></a> -
        <?php echo _('Source disponible sur'); ?>
        <a target="_blank" href="http://github.com/darklg/CSSLisible">Github</a> -
        <?php echo _('Contributeurs : '); ?>
        <a target="_blank" href="http://github.com/Darklg">Darklg</a>,
        <a target="_blank" href="http://github.com/NumEricR">NumEricR</a>
    </div>

    <?php
    // Fichier conditionnel pour charger boutons like, google analytics, etc.
    $hollow_file = realpath(DIR_SITE).'/inc/hollow-file.php';
    if(file_exists($hollow_file)){
        include $hollow_file;
    }
    ?>

    <script src="<?php echo URL_SITE; ?>js/ZeroClipboard.min.js?1363440036"></script>
    <script src="<?php echo URL_SITE; ?>js/events.js?t=201208181531" type="text/javascript" charset="utf-8"></script>

    </body>
</html>
