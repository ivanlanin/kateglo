update phrase_type set phrase_type_name = 'Kata majemuk' where phrase_type = 'c';
update phrase_type set phrase_type_name = concat(upper(substr(phrase_type_name, 1, 1)), substr(phrase_type_name, 2));
update lexical_class set lex_class_name = concat(upper(substr(lex_class_name, 1, 1)), substr(lex_class_name, 2));
