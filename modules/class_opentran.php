 <?php
require_once('XML/RPC.php');

class opentran
{
    function opentran()
    {
    }

    function translate($lemma)
    {
        $lemma = strtolower($lemma);
        $targetLang = 'en';
        $sourceLang = 'id';
        $params = array(
            new XML_RPC_Value($lemma, 'string'),
            new XML_RPC_Value($sourceLang, 'string'),
            new XML_RPC_Value($targetLang, 'string'),
            new XML_RPC_Value(20, 'int'),
        );
        $msg = new XML_RPC_Message('suggest3', $params);
        $cli = new XML_RPC_Client('/RPC2', 'open-tran.eu');
        $resp = $cli->send($msg);
        if ($resp) {
            if (!$resp->faultCode()) {
                $val = $resp->value();
                $data = XML_RPC_decode($val);
                foreach($data as $value)
                {
                    $matched = false;
                    $text = strtolower(strip_tags($value['text']));
                    if (is_array($value['projects']))
                    {
                        foreach($value['projects'] as $orig)
                        {
                            if (strtolower(strip_tags($orig['orig_phrase'])) == $text)
                            {
                                $matched = true;
                                break;
                            }
                        }
                    }
                    if ($matched)
                        $translation .= ($translation ? '; ' : '') . $text;
                }
            }
        }
        if ($translation)
            $ret = array(
                'ref_source'=>'open-tran.eu',
                'lemma'=>$lemma,
                'translation'=>$translation,
            );
//      var_dump($ret);
//      die('<br /><br />a');
        return($ret);
    }
};

?>