<?php
/**
 *
 */
class home extends page
{

    /**
     * Constructor
     */
    function home(&$db, &$auth, $msg)
    {
        parent::page(&$db, &$auth, $msg);
    }

    /**
     *
     */
    function show()
    {
        // statistics
        $searches = $this->db->get_rows('SELECT phrase FROM searched_phrase
            ORDER BY search_count DESC LIMIT 0, 5;');
        if ($searches)
        {
            $search_result = '';
            $tmp = '<strong>%1$s</strong> [<a href="?mod=dictionary&action=view&phrase=%1$s">%2$s</a>, '
                . '<a href="?mod=glossary&phrase=%1$s">%3$s</a>]';
            for ($i = 0; $i < $this->db->num_rows; $i++)
            {
                if ($this->db->num_rows > 2)
                    $search_result .= $search_result ? ', ' : '';
                if ($i == $this->db->num_rows - 1 && $this->db->num_rows > 1)
                    $search_result .= ' dan ';
                $search_result .= sprintf($tmp, $searches[$i]['phrase'],
                    $this->msg['dict_short'], $this->msg['glo_short']);
            }
        }
        else
            $search_result = $this->msg['no_most_searched'];

        // stat count
        $dict_count = $this->db->get_row_value('SELECT COUNT(*) FROM phrase;');
        $glo_count = $this->db->get_row_value('SELECT COUNT(*) FROM glossary;');
        $abbr_count = $this->db->get_row_value('SELECT COUNT(*) FROM abbr_entry;');
        $prv_count = $this->db->get_row_value('SELECT COUNT(*) FROM proverb WHERE prv_type = 1;');

        // welcome
        $ret .= '<div class="jumbotron" style="text-align:center;">' . LF;
        $ret .= sprintf($this->msg['welcome'] . LF, $dict_count, $glo_count, $prv_count, $abbr_count);
        $ret .= '</div>' . LF;
        $ret .= '<div style="text-align:center;">' . LF;

        // random lemma
        $query = 'SELECT phrase, lex_class FROM phrase
            WHERE (LEFT(phrase, 2) != \'a \' AND LEFT(phrase, 2) != \'b \')
            AND NOT ISNULL(updated) AND NOT ISNULL(lex_class)
            ORDER BY RAND() LIMIT 10;';
        $random_words = $this->db->get_rows($query);
        $url = './?mod=dictionary&action=view&phrase=';
        $ret .= '<p style="padding-top:10px;">' . LF;
        $ret .= '<strong>' . $this->msg['random_lemma'] . ':</strong><br />' . LF;
        foreach ($random_words as $random_word)
        {
            $ret .= '<span>';
            $ret .= sprintf('<a href="%1$s%2$s" class="label label-primary">%2$s</a>%3$s',
                $url,
                $random_word['phrase'],
                ''
            );
//              ($random_word['lex_class'] ? ' (' . $random_word['lex_class'] . ')' : '')
            $ret .= '</span>' . LF;
        }

        // random redirect
        $limit = 5;
        $query = 'SELECT actual_phrase, phrase FROM phrase
            WHERE LEFT(phrase, 1) != \'1\' AND LEFT(phrase, 1) != \'2\' AND LEFT(phrase, 1) != \'3\'
            AND LEFT(actual_phrase, 1) != \'1\' AND LEFT(actual_phrase, 1) != \'2\' AND LEFT(actual_phrase, 1) != \'3\'
            AND NOT ISNULL(actual_phrase)
            ORDER BY RAND() LIMIT ' . $limit . ';';
        $random_redirs = $this->db->get_rows($query);
        $url = './?mod=dictionary&action=view&phrase=';
        $ret .= '<p style="padding-top:10px;">' . LF;
        $ret .= '<strong>' . $this->msg['wrong_spelling'] . ':</strong><br />' . LF;
        $i = 0;
        foreach ($random_redirs as $random_redir)
        {
            $ret .= '<span style="padding:0px 5px; white-space:nowrap;">';
            $ret .= sprintf('<a href="%1$s%2$s" class="label label-success">%2$s</a> %4$s <a href="%1$s%3$s" class="label label-danger">%3$s</a>',
                $url,
                $random_redir['actual_phrase'],
                $random_redir['phrase'],
                $this->msg['not'],
                '');
            $ret .= '</span>' . LF;
            if ($i < $limit) $ret .= '<br />';
        }

        $ret .= '</p>' . LF;
        $ret .= '</div>' . LF;

        // return
        return($ret);
    }

    /**
     * Keywords
     */
    function get_keywords()
    {
        return('bahasa Indonesia, glosarium, kamus, tesaurus');
    }

    /**
     * Description
     */
    function get_description()
    {
        return('Kamus, tesaurus, dan glosarium bahasa Indonesia dari milis Bahtera');
    }

};
?>