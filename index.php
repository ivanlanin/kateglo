<?php
// header('Location: http://kateglo.bahtera.org');
if ($_SERVER['SERVER_NAME'] == 'kateglo.situswebku.net') {
    header('Location: http://kateglo.com');
}

/**
 * Entry point of application
 */
// base dir
$base_dir = dirname(__FILE__);
ini_set('include_path', $base_dir . '/pear/');

// includes
require_once($base_dir . '/config/settings.php');
require_once($base_dir . '/config/config.php');
require_once($base_dir . '/config/messages.php');
require_once('common.php');
require_once('Auth.php');
require_once($base_dir . '/classes/class_db.php');
require_once($base_dir . '/classes/class_form.php');
require_once($base_dir . '/classes/class_logger.php');
require_once($base_dir . '/classes/class_page.php');

// initialization
$db = new db;
$db->connect($dsn);
$db->msg = $msg;

// authentication & and logging
$auth = new Auth(
    'MDB2', array(
        'dsn' => $db->dsn,
        'table' => "sys_user",
        'usernamecol' => "user_id",
        'passwordcol' => "pass_key"
    ), 'login');
$auth->start();
$logger = new logger($db, $auth);
$logger->log();

// define mod
$mods = array(
    'user', 'dictionary', 'glossary', 'home', 'doc', 'proverb', 'abbr', 'dict2'
);
$_GET['mod'] = strtolower($_GET['mod']);
if ($_GET['mod'] == 'dict') $_GET['mod'] = 'dictionary'; // backward
if ($_GET['mod'] == 'glo') $_GET['mod'] = 'glossary'; // backward
if (!in_array($_GET['mod'], $mods)) $_GET['mod'] = 'home';
$mod = $_GET['mod'];

// process
require_once($base_dir . '/modules/class_' . $mod . '.php');
$page = new $mod($db, $auth, $msg);
$page->process();

// display
$body .= $page->show();
$title = ($mod == 'home') ? APP_NAME : APP_SHORT;
if (!$page->title && $mod != 'home')
{
    if ($msg[$mod]) $page->title = $msg[$mod];
    if ($_GET['phrase']) $page->title = $_GET['phrase'] . ' ~ ' . $page->title;
}
$title = $page->title ? $page->title . ' ~ ' . $title : $title;
$keywords = $page->get_keywords();
$description = $page->get_description();
$padding_top = ($mod == 'home') ? 70 : 50;
?>
<!DOCTYPE html>
<html>
<head>
<title><?php echo($title); ?></title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="google-site-verification" content="zmQCyhcE8Z9CoamZld4k96jVIhDONOmLkiQeFJWjK-w" />
<?php if ($keywords) { ?><meta name="keywords" content="<?php echo($keywords); ?>" /><? } ?>
<?php if ($description) { ?><meta name="description" content="<?php echo($description); ?>" /><? } ?>
<link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
<link rel="icon" href="./images/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="./images/favicon.ico" type="image/x-icon" />
<link rel="search" type="application/opensearchdescription+xml" href="./opensearch_desc.php" title="Kateglo" />
<link rel="publisher" href="https://plus.google.com/108624568192580015442" />
<style>
body { min-height: 1000px; padding-top: <?php echo($padding_top); ?>px; }
.footer { padding-top: 20px; }
.search_param { white-space:nowrap; margin:0px 20px 10px 0px; display: inline-block; }
.sample { font-style: italic; color: #999999; }
</style>
</head>
<body>
<div class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
    <a href="./"><img src="images/kateglo40.png" width="129" height="40" border="0" alt="Kateglo" title="Kateglo" /></a>
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Ubah navigasi</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>
    <div class="collapse navbar-collapse">
    <form class="navbar-form navbar-left" role="search" method="get" action="./" id="navsearch" name="navsearch">
      <div class="form-group">
        <input name="phrase" class="form-control" type="text" placeholder="Pencarian" value="<?php echo($_GET['phrase']); ?>">
      </div>
      <div class="form-group">
        <select name="mod" class="form-control">
<?php
    $mods = array('dictionary' => 'Kamus', 'glossary' => 'Glosarium', 'proverb' => 'Peribahasa', 'abbr' => 'Singkatan');
    foreach ($mods as $key => $val) {
        $selected = ($key == $_GET['mod'] ? ' selected' : '');
        echo(sprintf('<option value="%s"%s>%s</option>', $key, $selected, $val));
    }
?>
        </select>
      </div>
        <button type="submit" class="btn btn-default">Cari</button>
    </form>
    </div>
  </div>
</div>

<?php
unset($ret);
if ($mod == 'home') $ret .= '<div class="container">' . LF;
//$ret .= show_header();
$ret .= '<div class="container">' . LF;
$ret .= $body;
$ret .= '</div>' . LF;
$ret .= show_footer();
if ($mod == 'home') $ret .= '</div>' . LF;

// stats
if ($allow_stat) $ret .= get_external_stat();
echo($ret);
?>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-2254800-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="./bootstrap/js/bootstrap.min.js"></script>
<a rel="me" href="https://plus.google.com/105052701746386878138?rel=author"></a>
</body>
</html>