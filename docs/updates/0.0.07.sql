alter table phrase add phrase_type varchar(16) not null default 'r' comment 'r=root; f=affix; c=compond' after phrase;

create unique index relation_unique on relation
(
   root_phrase,
   related_phrase,
   rel_type
);

drop table if exists phrase_type;

/*==============================================================*/
/* Table: phrase_type                                           */
/*==============================================================*/
create table phrase_type
(
   phrase_type          varchar(16) not null comment 'r=root; f=affix; c=compond',
   phrase_type_name     varchar(255) not null,
   sort_order           tinyint not null default 1,
   updated              datetime,
   updater              varchar(32) not null,
   primary key (phrase_type)
);

update relation_type set rel_type_name = 'Gabungan' where rel_type = 'c';

insert into discipline (discipline, discipline_name) values ('agamaislam', 'Agama Islam');
insert into discipline (discipline, discipline_name) values ('antropologi', 'Antropologi');
insert into discipline (discipline, discipline_name) values ('arkeologi', 'Arkeologi');
insert into discipline (discipline, discipline_name) values ('farmasi', 'Farmasi');
insert into discipline (discipline, discipline_name) values ('filsafat', 'Filsafat');
insert into discipline (discipline, discipline_name) values ('kedokteranhewan', 'Kedokteran hewan');
insert into discipline (discipline, discipline_name) values ('komunikasimassa', 'Komunikasi massa');
insert into discipline (discipline, discipline_name) values ('perhutanan', 'Perhutanan');
insert into discipline (discipline, discipline_name) values ('sastra', 'Sastra');
insert into discipline (discipline, discipline_name) values ('sosiologi', 'Sosiologi');
insert into discipline (discipline, discipline_name) values ('teknikkimia', 'Teknik kimia');
update _pbglo set discipline='*umum*' where discipline='lain-lain';
update _pbglo set discipline='elektronika' where discipline='tekniklistrik';
update _pbglo set discipline='fotografi' where discipline='fotografidanfi';
update _pbglo set discipline='konstruksi' where discipline='tekniksipil';
update _pbglo set discipline='mesin' where discipline='teknikmesin';
update _pbglo set discipline='otomotif' where discipline='teknikautomotif';
update _pbglo set discipline='penerbangan' where discipline='teknikdirgantar';
update _pbglo set discipline='penerbangan' where discipline='teknikkapalter';
update _pbglo set discipline='pertambangan' where discipline='teknikpertamban';
update _pbglo set discipline='teknologiinformasi' where discipline='teknologiinform';

delete from translation where ref_source = 'Pusba';

insert into translation (phrase, translation, discipline, ref_source)
select phrase, translation, discipline, ref_source from _pbglo;

select a.discipline, count(b.tr_uid)
from discipline a left join translation b on a.discipline =  b.discipline
group by a.discipline
order by 2;

delete from discipline where discipline in ('administrasipublik', 'aviasi', 'seni');

-- 182396
/*
 * 1356 kamus
 * 150 sesi
 */