alter table phrase add info varchar(255) comment 'Additional information' after actual_phrase;
alter table phrase add notes varchar(4000) comment 'Additional information' after info;
alter table phrase add kbbi_updated datetime after wikipedia_updated;
alter table glossary add wikipedia_updated datetime;

drop table if exists sys_cache;

/*==============================================================*/
/* Table: sys_cache                                             */
/*==============================================================*/
create table sys_cache
(
   cache_uid            int not null auto_increment,
   cache_type           varchar(16) not null comment 'kbbi',
   updated              datetime not null,
   phrase               varchar(255),
   content              text,
   primary key (cache_uid)
);


-- clean up phrase with number in front

-- atribut
-- 2a: gnomon, rombong, sudu
-- 4a: atribut
-- 1a: tarung
delete from phrase where phrase like '2lapak%';
delete from definition where phrase like '2lapak%';
delete from relation where related_phrase like '2lapak%';
delete from relation where root_phrase like '2lapak%';

delete from phrase where phrase in ('1lin', '2lin');
delete from definition where phrase in ('1lin', '2lin');
delete from relation where related_phrase in ('1lin', '2lin');
delete from relation where root_phrase in ('1lin', '2lin');

delete from phrase where phrase in ('2 a', '2 a hati');
delete from definition where phrase in ('2 a', '2 a hati');
delete from relation where related_phrase in ('2 a', '2 a hati');
delete from relation where root_phrase in ('2 a', '2 a hati');

delete from phrase where phrase in ('4 a');
delete from definition where phrase in ('4 a');
delete from relation where related_phrase in ('4 a');
delete from relation where root_phrase in ('4 a');

delete from phrase where phrase in ('1kah', '1auto');
delete from definition where phrase in ('1kah', '1auto');
delete from relation where related_phrase in ('1kah', '1auto');
delete from relation where root_phrase in ('1kah', '1auto');

delete from phrase where phrase in ('1 a');
delete from definition where phrase in ('1 a');
delete from relation where related_phrase in ('1 a');
delete from relation where root_phrase in ('1 a');

delete from phrase where phrase in ('1 bertarung dng');
delete from definition where phrase in ('1 bertarung dng');
delete from relation where related_phrase in ('1 bertarung dng');
delete from relation where root_phrase in ('1 bertarung dng');

delete from phrase where phrase in ('2');

select * from phrase where phrase regexp '[^[:alpha:]| |-]';