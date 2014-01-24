select a.* from new_lemma a  left join phrase b on a.new_lemma = b.phrase
where isnull(b.phrase) order by glossary_count desc;