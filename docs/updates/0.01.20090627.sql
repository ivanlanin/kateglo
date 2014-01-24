-- update phrase class based on the actual phrase

update phrase a, phrase b set a.lex_class = b.lex_class where a.actual_phrase = b.phrase and not isnull(a.actual_phrase);