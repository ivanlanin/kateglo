<?php
$base_url = 'http://' .
    $_SERVER['SERVER_NAME'] .
    ($_SERVER['SERVER_PORT'] == '80' ? '' : ':' . $_SERVER['SERVER_PORT']) .
    str_replace('opensearch_desc.php', '', $_SERVER['SCRIPT_NAME']);
header('Content-type: text/xml');
echo('<?xml version="1.0"?>' . "\n");
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
<ShortName>Kateglo (Kamus)</ShortName>
<Description>Cari entri kamus di Kateglo Bahtera</Description>
<Image height="16" width="16" type="image/x-icon"><?php echo($base_url); ?>images/favicon.ico</Image>
<Url type="text/html" method="get" template="<?php echo($base_url); ?>?mod=dictionary&amp;phrase={searchTerms}" />
<Url type="application/opensearchdescription+xml" rel="self" template="<?php echo($base_url); ?>/opensearch_desc.php" />
</OpenSearchDescription>