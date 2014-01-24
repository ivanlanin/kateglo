-- merge derived word

insert into relation_type (rel_type, rel_type_name) values ('d', 'Turunan');
delete from relation_type where rel_type in ('f', 'c');
update relation set rel_type = 'd' where rel_type in ('f', 'c');

insert into phrase_type (phrase_type, phrase_type_name, sort_order) values ('d', 'Kata turunan', 2);
delete from phrase_type where phrase_type in ('f', 'c');
update phrase set phrase_type = 'd' where phrase_type in ('f', 'c');

-- new type: affix

insert into phrase_type (phrase_type, phrase_type_name, sort_order) values ('a', 'Imbuhan', 3);
update phrase set phrase_type = 'a' where lex_class = 'l' and phrase regexp '-';
update phrase set phrase_type = 'd' where phrase regexp '[[:alnum:]]-[[:alnum:]]';

-- delete error

delete from phrase where phrase = 'ber- (be-';
delete from definition where phrase = 'ber- (be-';
delete from relation where root_phrase = 'ber- (be-';
delete from relation where related_phrase = 'ber- (be-';

-- find error

SELECT b.root_phrase, a.phrase FROM `phrase` a, relation b WHERE a.phrase = b.related_phrase AND a.phrase regexp '[^[:alpha:] -]';

SELECT b.*, a.actual_phrase from phrase a, definition b where a.phrase = b.phrase and not isnull(a.actual_phrase) and a.actual_phrase <> b.see;

-- flag on ref_source

alter table ref_source add dictionary tinyint not null default 0 after ref_source_name;
alter table ref_source add glossary tinyint not null default 0 after dictionary;
alter table ref_source add translation tinyint not null default 0 after glossary;

update ref_source set dictionary = 1, glossary = 1;

-- translation table

drop table if exists translation;

create table translation
(
   lemma                varchar(255) not null,
   ref_source           varchar(16) not null,
   translation          varchar(4000),
   primary key (lemma, ref_source)
);
