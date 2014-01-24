select a.phrase, count(*) from phrase a, definition b where a.phrase = b.phrase group by a.phrase order by 2 desc;

select phrase, def_count from phrase where def_count = 0 order by phrase;

delete from relation where root_phrase = related_phrase;
delete from phrase where phrase like 'perusahaan%' and def_count = 0;

-- clean up functions
delete from definition where phrase not in (select phrase from phrase);
delete from relation where related_phrase not in (select phrase from phrase);
delete from relation where root_phrase not in (select phrase from phrase);

alter table phrase add def_count int not null default 0 after ref_source;

create index phrase on definition (phrase);

update phrase set def_count = 0;
update phrase a
set a.def_count = (SELECT COUNT(b.def_uid) FROM definition b WHERE a.phrase = b.phrase);

-- gelembung
delete from phrase where phrase like '-gelembung%';
delete from definition where phrase like '-gelembung%';
delete from relation where root_phrase like '-gelembung%';
delete from relation where related_phrase like '-gelembung%';
delete from phrase where phrase like '-rektor%';
delete from definition where phrase like '-rektor%';
delete from relation where root_phrase like '-rektor%';
delete from relation where related_phrase like '-rektor%';