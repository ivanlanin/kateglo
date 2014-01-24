<?php
/**

*/
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
define(NL, "\r\n");

$title = 'Etimologi';
include_once('sealang.php');
$sea = new sealang();

$phrase = trim($_GET['q']);
if ($phrase) {
    $sea->curl_etymology($phrase);
    if ($sea->entries) {
        $body .= '<dl>' . NL;
        foreach ($sea->entries as $key => $entry) {
            $body .= sprintf('<dt>%s<dt>', $entry['entry']). NL;
            $body .= sprintf('<dd><em>%s</em> (%s) %s<dd>',
                $entry['word'], $entry['lang'], $entry['def']). NL;
        }
        $body .= '</dl>' . NL;
    } else {
        $body .= '<p>Tidak ditemukan</p>' . NL;
    }
}

//$sea->read_etymology();

//foreach ($sea->languages as $lang => $val) {
//    $body .= $lang . '<br />';
//}
//if ($sea->entries) {
//    $body .= '<table>' . NL;
//    $body .= '<tr>'. NL;
//    foreach ($sea->fields as $field) {
//        $body .= sprintf('<th valign="top">%s</th>', $field) . NL;
//    }
//    $body .= '</tr>'. NL;
//    foreach ($sea->entries as $key => $entry) {
//        $body .= '<tr>'. NL;
//        foreach ($sea->fields as $field) {
//            $bg = trim($entry[$field]) == '' ? ' style="background:#ff0;"' : '';
//            $body .= sprintf('<td valign="top"%s>%s</td>', $bg, $entry[$field]) . NL;
//        }
//        $body .= '</tr>'. NL;
//    }
//    $body .= '</table>' . NL;
//}
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo($title); ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./bootstrap/bootstrap.min.css">
<style>
body { margin-top: 30px; }
#search_form { margin-bottom: 20px; }
</style>
</head>
<body>
<div class="container text-center">
<form id="search_form" name="search_form" method="get" action="./">
<input id="q" name="q" type="text" value="<?php echo($phrase); ?>">
</form>
<?php echo($body); ?>
</div>
<script src="./bootstrap/jquery.min.js"></script>
<script src="./bootstrap/bootstrap.min.js"></script>
</body>
</html>