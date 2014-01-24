<?php
/**
 * Steps:
 * 1. check if exists
 * 2. if exists, get content
 * 3. get other language
 */
class mediawiki
{
    var $lang = 'id';
    var $project = 'wikipedia';
    var $api_url;

    /*
     * Constructor
     */
    function mediawiki($lang = null, $project = null)
    {
        if ($lang) $this->lang = $lang;
        if ($project) $this->project = $project;
        $this->api_url = sprintf('http://%1$s.%2$s.org/w/api.php?format=php&',
            $this->lang, $this->project);
    }

    /*
     * Check if a page exists in wikipedia
     * 0 = not exist
     * 1 = exist
     * 2 = a redirect
     */
    function get_page_info($titles)
    {
        if (!is_array($titles)) return;
        foreach ($titles as $title)
        {
            $title_var .= $title_var ? '|' : '';
            $title_var .= urlencode($title);
        }
        $url = $this->api_url . 'action=query&prop=info&redirects=1&titles=' . $title_var;
        $curl_ret = $this->curl($url);

        if (is_array($curl_ret))
        {
            // get redirects
            if ($curl_ret['query']['redirects'])
            {
                $has_redirects = true;
                foreach ($curl_ret['query']['redirects'] as $key => $value)
                {
                    $redirects[$value['from']] = $value['to'];
                }
            }

            // get normalized value
            if ($curl_ret['query']['normalized'])
            {
                $has_normalized = true;
                foreach ($curl_ret['query']['normalized'] as $key => $value)
                {
                    $normalized[$value['from']] = $value['to'];
                    if ($has_redirects)
                    {
                        if (array_key_exists($value['to'], $redirects))
                            $normalized[$value['from']] = $redirects[$value['to']];
                    }
                }
            }

            // get pages
            // get normalized value
            if ($curl_ret['query']['pages'])
            {
                foreach ($curl_ret['query']['pages'] as $key => $value)
                {
                    $val = array_key_exists('missing', $value) ? 0 : 1;
                    $pages[$value['title']] = array('id' => $key, 'status' => $val);
                    if ($val == 1)
                    {
                        $page_var .= $page_var ? '|' : '';
                        $page_var .= urlencode($value['title']);
                    }
                }
            }

            // get language links
            if ($page_var)
            {
                $url = $this->api_url . 'action=query&prop=langlinks&lllimit=500&titles=' . $title_var;
                $lang_ret = $this->curl($url);
                if ($lang_ret['query']['pages'])
                {
                    foreach ($lang_ret['query']['pages'] as $key => $value)
                    {
                        if ($value['langlinks'] !== null)
                        {
                            foreach ($value['langlinks'] as $lang)
                                $pages[$value['title']]['langlinks'][$lang['lang']] = $lang['*'];
                        }
                    }
                }
            }


            // set the normalized title (if exists) as title
            foreach ($titles as $title)
            {
                $key = $title;
                if (is_array($normalized))
                    if (array_key_exists($key, $normalized))
                        $key = $normalized[$key];
                if (array_key_exists($key, $pages))
                {
                    $ret[$title] = array(
                        'from' => $title,
                        'to' => $key,
                        'status' => $pages[$key]['status'],
                        'langlinks' => $pages[$key]['langlinks']
                    );
                }
            }
        }
        return($ret);
    }

    /*
     * Get content
     */
    function get_content($title)
    {
        // $url = $this->api_url . 'action=parse&prop=text&page=' . $title;
        $url = $this->api_url . 'action=parse&prop=text&page=' . $title;
        if ($ret_array = $this->curl($url))
        {
            $ret_txt = $ret_array['parse']['text']['*'];
            preg_match_all('/<p>.+<\/p>/U', $ret_txt, $matches);
            $ret = strip_tags($matches[0][0]);
        }
        return($ret);
    }

    /*
     * Curl
     */
    function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $cr = curl_exec($ch);
        curl_close($ch);

        // return
        $ret = false;
        if ($cr) $ret = unserialize($cr);
        return($ret);
    }
};
?>