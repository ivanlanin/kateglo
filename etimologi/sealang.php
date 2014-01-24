<?php
/**
 * SEAlang Class to connect to sealang.net
 */
//$base_dir = 'D:\www\localhost\kateglo2';
//ini_set('include_path', $base_dir . '/pear/');
//require_once($base_dir . '/classes/class_db.php');
//$dsn['user'] = 'k6978368_tbi';
//$dsn['pass'] = '2196f9b85a';
//$dsn['host'] = 'localhost';
//$dsn['name'] = 'k6978368_tbi';
//$db = new db;
//$db->connect($dsn);
//$rows = $db->get_rows('SELECT * FROM loan_words');
//
//foreach ($rows as $row) {
//    $html = $row['lw_content'];
//    get_entries($html);
//}

// immediate donor, earlier donor
// TODO: pragmatik konstatasi
class sealang
{
    public $raw_html; // Raw HTML retrieved
    public $entries; // Array of entries
    public $languages; // Display fields

    function __construct()
    {
        $this->fields = array('flag', 'entry', 'lang', 'word', 'def', 'raw');
        $this->languages = array(
            "American-English" => "Ing",
            "Amoy" => "Cn",
            "Arabic" => "Ar",
            "Cantonese" => "Cn",
            "Chiangchiu" => "Cn",
            "Chiangchiu/A" => "Cn",
            "Dutch" => "Bld",
            "English" => "Ing",
            "French" => "Pr",
            "German" => "Jm",
            "Greek" => "Yn",
            "Hindi" => "Hi",
            "Italian" => "It",
            "Japanese" => "Jp",
            "Latin" => "Lt",
            "Persian" => "Par",
            "Portuguese" => "Prt",
            "Sanskrit" => "Skt",
            "Tong'an" => "Cn",
            );

    }

    // Flag and sanity check
    function sanity_check()
    {
        if (!$this->entries) return;
        foreach ($this->entries as $key => $entry) {
            if ($entry['lang']) {
                // $this->languages[$entry['lang']] = '';
                if (array_key_exists($entry['lang'], $this->languages)) {
                    $this->entries[$key]['lang'] = $this->languages[$entry['lang']];
                }
            }
            $ref_pattern = '/[A-Z]+[0-9]+\:[0-9]+/';
            $ref_pattern = '/[-\w]+[0-9]+[-\:][0-9.]+/';
            if (preg_match($ref_pattern, $entry['word'])) {
                $this->entries[$key]['word'] = trim(preg_replace($ref_pattern, '', $entry['word']));
            }
            foreach ($this->fields as $field) {
                if ($entry['flag'] != 'multi' && trim($entry[$field]) == '') {
                    $this->entries[$key]['flag'] = 'incomplete';
                }
            }
        }
    }

    function read_etymology()
    {
        foreach (glob("lwim/*.xml") as $filename) {
            $this->raw_html = file_get_contents($filename);
            $this->parse_etymology();
        }
        $this->sanity_check();
    }

    function curl_etymology($phrase)
    {
        $url = 'http://sealang.net/lwim/search.pl?dict=lwim&ignoreDiacritic=1&orth=' . $phrase;
        $agent = "Mozilla/5.0 (Windows; U; Windows NT 5.0; en; rv:1.9.0.4) Gecko/2009011913 Firefox/3.0.6";
        $domain = 'http://' . parse_url($url, PHP_URL_HOST);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_REFERER, $domain);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        $this->raw_html = curl_exec($curl);
        curl_close($curl);
        $this->parse_etymology();
    }

    function parse_etymology()
    {
        $html = $this->raw_html;
        $dom = new DOMDocument;
        $dom->loadHTML($html);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//entry');

        foreach ($nodes as $node) {
            $id = $node->getAttribute('id');
            if ($id) {
                $i++;
                $root_path = sprintf('//entry[@id="%s"]', $id);
                $entry = $node->getAttribute('orthtarget');
                $entry = trim(strip_tags($entry));
                $entry = str_replace('é', 'e', $entry);
                $entry = str_replace('’', '\'', $entry);
                $entry = str_replace('ʿ', '\'', $entry);
                $def = $xpath->query($root_path . '//sense/def')->item(0)->nodeValue;
                $lang = $xpath->query($root_path . '//etym/lang')->item(0)->nodeValue;
                $word = $xpath->query($root_path . '//etym/mentioned')->item(0)->nodeValue;

                $raw_xml = $dom->saveHTML($node);
                $this->entries[$id]['raw_xml'] = mb_convert_encoding($raw_xml, 'HTML-ENTITIES', 'UTF-8');
                $this->entries[$id]['num'] = $i;
                $this->entries[$id]['id'] = $id;
                $this->entries[$id]['entry'] = $entry;
                $this->entries[$id]['lang'] = strip_tags($lang);
                $this->entries[$id]['word'] = strip_tags($word);
                $this->entries[$id]['raw'] = strip_tags($def);

                if ($lang) $this->entries[$id]['flag'] = '0';

                if (strpos($def, '] from:')) {
                    $def = str_replace('] from:', '] {from:', $def);
                }
                if (strpos($def, '] {')) {
                    $def = str_replace('[', '', $def);
                    $def = str_replace('] {', ' [', $def);
                    $def = str_replace('from:', '< ', $def);
                    $this->entries[$entry]['raw'] =  $def;
                }
                $this->get_def($def, $this->entries[$id]);
            }
        }
        $this->sanity_check();
    }

    // ekofrasia
    // galiung
    // golkarisasi
    // hipergami
    // muon
    // dabaran
    function get_def($def, &$entry)
    {
        $def = strip_tags($def);
        $def = str_replace('based on ', '< ', $def);
        $def = preg_replace('/\bf\b/', '<', $def);
        $def = preg_replace('/\bEng\b/', 'English', $def);
        $def = preg_replace('/\bGk\b/', 'Greek', $def);
        $def = trim($def);

        // Check multiple. Filter model like (... + ...)
        if (strpos($def, '+') !== false) {
            if (!preg_match('/\([^\)]+\+[^\)]+\)/', $def)) {
                $is_multi = true;
                $entry['flag'] = 'multi';
                if (preg_match('/([^\[]+)\[(.+)\]/', $def, $meaning_source)) {
                    $def = trim($meaning_source[1]);
                    $word = trim($meaning_source[2]);
                    $word = trim(preg_replace('/\bfrom\b/', '<', $word));
                    $multiparts = explode('+', $word);
                    unset($word);
                    foreach ($multiparts as $part) {
                        // remove first "< "
                        $part = preg_replace('/^< /', '', trim($part));
                        // remove later root
                        $part = preg_replace('/\(< ([^\)]+)\)( \(.+\))?/', '($1)', trim($part));
                        // rearrange inverted
                        if (preg_match('/^([A-Z]\w+) ([^\(]+)/', $part, $inverted)) {
                            $part = sprintf('%s (%s)', $inverted[2], $inverted[1]);
                        }
                        // remove "or" and "loan"
                        $part = preg_replace('/\((\w+) or < .+\)/', '($1)', trim($part));
                        $part = preg_replace('/^loan /', '', trim($part));
                        if ($part == '<') continue(1);
                        if (!preg_match('/\([^\)]+\)/', $part)) $part = ''; // koagel
                        $part = preg_replace('/^([^\(]+)\(([^\)]+)\) or .+/', '$1 ($2)', trim($part));
                        $word .= trim($word) ? '<br />' : '';
                        $word .= trim($part);
                    }
                }
            }
        }
        if ($is_multi) {
            if ($lang) $entry['lang'] = $lang;
            if ($word) $entry['word'] = $word;
            $entry['def'] = $def;
            return;
        }

        // Meaning and source
        if (preg_match('/([^\[]+)\[(.+)\]/', trim($def), $meaning_source)) {
            $entry['flag'] = '1';
            $def = trim($meaning_source[1]);
            $word = trim($meaning_source[2]);
            if (preg_match('/< (\w+) (.+) \((.+)\)/', $word, $source_root)) {
                $entry['flag'] = '1.1';
                $lang = trim($source_root[1]);
                $word = trim($source_root[2]);
                if (preg_match('/(.+) or (.+)/', $word, $source_alt)) {
                    $entry['flag'] = '1.1.1';
                    $word = trim($source_alt[1]);
                    if (preg_match('/< [A-Z]\w+/', $word, $source_alt2)) { // intima
                        $entry['flag'] = '1.1.1.1';
                        $word = trim(preg_replace('/< [A-Z]\w+/', '', $source_alt[2]));
                    }
                } else {
                    $entry['flag'] = '1.1.2';
                    if (strpos($word, '<') !== false) {
                        if (preg_match('/or < \w+ (.+)/', $word, $alt_has_source)) {
                            $entry['flag'] = '1.1.2.1';
                            $word = trim($alt_has_source[1]);
                        }
                    }
                }
            } else {
                // aris
                $entry['flag'] = '1.2';
                if (preg_match('/< (\w+) (.+)/', $word, $source_word)) {
                    $entry['flag'] = '1.2.1';
                    $lang = trim($source_word[1]);
                    $word = trim($source_word[2]);
                    // galiung
                    $word = preg_replace('/ or < .+/', '', $word);
                }
            }
        }

        if ($lang) $entry['lang'] = $lang;
        if ($word) $entry['word'] = $word;
        $entry['def'] = $def;
    }
}
