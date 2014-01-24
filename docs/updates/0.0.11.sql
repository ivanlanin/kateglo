alter table lexical_class add lex_class_ref varchar(255) after lex_class_name;
alter table discipline add glossary_count int not null default 0 after discipline_name;
alter table ref_source add glossary_count int not null default 0 after ref_source_name;

update lexical_class set lex_class_ref = 'nomina' where lex_class = 'n';
update lexical_class set lex_class_ref = 'verba' where lex_class = 'v';
update lexical_class set lex_class_ref = 'adjektiva' where lex_class = 'adj';
update lexical_class set lex_class_ref = 'adverbia' where lex_class = 'adv';
update lexical_class set lex_class_ref = 'numeralia' where lex_class = 'num';
update lexical_class set lex_class_ref = 'pronomina' where lex_class = 'pron';

update discipline a
set a.glossary_count = (SELECT COUNT(b.tr_uid) FROM translation b WHERE a.discipline = b.discipline);

update ref_source a
set a.glossary_count = (SELECT COUNT(b.tr_uid) FROM translation b WHERE a.ref_source = b.ref_source);

delete from phrase where phrase = '(pd) umum nya';
delete from definition where phrase = '(pd) umum nya';
delete from relation where related_phrase = '(pd) umum nya';

delete from phrase where phrase = '3 a';
update definition set phrase = 'ligatur' where phrase = '3 a';
delete from relation where related_phrase = '3 a';

-- wan

update definition set phrase = '-wan' where phrase = '-man';
delete from definition where phrase = '-wan';
delete from relation where root_phrase = '-wan';

-- apa

delete from phrase where phrase = 'buat';
delete from definition where phrase = 'buat';
delete from relation where related_phrase = 'buat';
delete from relation where root_phrase = 'buat';

update phrase set phrase = 'apa boleh buat' where phrase = 'apa boleh';
update definition set phrase = 'apa boleh buat' where phrase = 'apa boleh';
update relation set related_phrase = 'apa boleh buat' where related_phrase = 'apa boleh';
update relation set root_phrase = 'apa boleh buat' where root_phrase = 'apa boleh';

-- barometer

delete from relation where related_phrase = 'mutlak' and ;
update phrase set phrase = 'apa boleh buat' where phrase = 'apa boleh';
update definition set phrase = 'apa boleh buat' where phrase = 'apa boleh';
update relation set related_phrase = 'apa boleh buat' where related_phrase = 'apa boleh';
update relation set root_phrase = 'apa boleh buat' where root_phrase = 'apa boleh';
