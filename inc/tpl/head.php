<meta charset="utf-8"/>
<title><?php echo TITRE_SITE . ' - ' . _(SLOGAN_SITE); ?></title>
<meta name="viewport" content="width=790" />
<link rel="stylesheet" href="<?php echo URL_SITE; ?>css/main.css?t=201208181531" type="text/css" />
<?php foreach($more_languages as $llang => $language){
    $urlLang = URL_SITE . '?lang=' . $llang;
    if(URL_REWRITING){
        $urlLang = URL_SITE . $llang . '/';
    }
    ?><link rel="alternate" hreflang="<?php echo $llang; ?>" href="<?php echo $urlLang; ?>" /><?php
} ?>
<script>var url_site = '<?php echo URL_SITE; ?>';</script>