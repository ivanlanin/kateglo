/**
 *
 */

-- Kelas kata

update lexical_class set lex_class_name = 'Nomina (kata benda)', sort_order = 1 where lex_class = 'n';
update lexical_class set lex_class_name = 'Verba (kata kerja)', sort_order = 2 where lex_class = 'v';
insert into lexical_class (lex_class, lex_class_name, sort_order) values ('adj', 'Adjektiva (kata sifat)', 3);
insert into lexical_class (lex_class, lex_class_name, sort_order) values ('adv', 'Adverbia (kata keterangan)', 4);
insert into lexical_class (lex_class, lex_class_name, sort_order) values ('pron', 'Pronomina (kata ganti)', 5);
insert into lexical_class (lex_class, lex_class_name, sort_order) values ('num', 'Numeralia (kata bilangan)', 6);
insert into lexical_class (lex_class, lex_class_name, sort_order) values ('l', 'Lain-lain (preposisi, artikula, dll)', 7);

-- Bidang

insert into discipline (discipline, discipline_name) values ('dok', 'Kedokteran');
insert into discipline (discipline, discipline_name) values ('huk', 'Hukum');
insert into discipline (discipline, discipline_name) values ('mgmt', 'Manajemen');

drop table if exists ref_source;

/*==============================================================*/
/* Table: ref_source                                            */
/*==============================================================*/
create table ref_source
(
   ref_source           varchar(16) not null,
   ref_source_name      varchar(255) not null,
   updated              datetime,
   updater              varchar(32) not null,
   primary key (ref_source)
)
comment = "Reference source";

-- Additional column for translation

alter table translation add ref_source varchar(16) after lang;
alter table translation add wpid varchar(255) after ref_source;
alter table translation add wpen varchar(255) after wpid;

-- Reference source

insert into ref_source (ref_source, ref_source_name) values ('Pusba', 'Pusat Bahasa');
insert into ref_source (ref_source, ref_source_name) values ('SM', 'Sofia Mansoor');
insert into ref_source (ref_source, ref_source_name) values ('Bahtera', 'Bahtera');

update translation set ref_source = 'Pusba' where isnull(ref_source);

-- Bidang

insert into discipline (discipline, discipline_name) values ('agri', 'Pertanian');
insert into discipline (discipline, discipline_name) values ('ars', 'Arsitektur');
insert into discipline (discipline, discipline_name) values ('asur', 'Asuransi');
insert into discipline (discipline, discipline_name) values ('bank', 'Perbankan');
insert into discipline (discipline, discipline_name) values ('edu', 'Pendidikan');
insert into discipline (discipline, discipline_name) values ('el', 'Elektronika');
insert into discipline (discipline, discipline_name) values ('foto', 'Fotografi');
insert into discipline (discipline, discipline_name) values ('geo', 'Geologi');
insert into discipline (discipline, discipline_name) values ('ikan', 'Perikanan');
insert into discipline (discipline, discipline_name) values ('kapal', 'Perkapalan');
insert into discipline (discipline, discipline_name) values ('konstruksi', 'Konstruksi');
insert into discipline (discipline, discipline_name) values ('kristen', 'Kristen');
insert into discipline (discipline, discipline_name) values ('lelang', 'Pelelangan');
insert into discipline (discipline, discipline_name) values ('keu', 'Keuangan');
insert into discipline (discipline, discipline_name) values ('migas', 'Minyak & gas');
insert into discipline (discipline, discipline_name) values ('mil', 'Militer');
insert into discipline (discipline, discipline_name) values ('ms', 'Mesin');
insert into discipline (discipline, discipline_name) values ('otomotif', 'Otomotif');
insert into discipline (discipline, discipline_name) values ('par', 'Pariwisata');
insert into discipline (discipline, discipline_name) values ('paten', 'Paten');
insert into discipline (discipline, discipline_name) values ('pajak', 'Pajak');
insert into discipline (discipline, discipline_name) values ('pelayaran', 'Pelayaran');
insert into discipline (discipline, discipline_name) values ('pol', 'Kepolisian');
insert into discipline (discipline, discipline_name) values ('psi', 'Psikologi');
insert into discipline (discipline, discipline_name) values ('religi', 'Agama');
insert into discipline (discipline, discipline_name) values ('saham', 'Saham');
insert into discipline (discipline, discipline_name) values ('stat', 'Statistika');
insert into discipline (discipline, discipline_name) values ('tek', 'Teknik');
insert into discipline (discipline, discipline_name) values ('ternak', 'Peternakan');
insert into discipline (discipline, discipline_name) values ('tmb', 'Tambang');
insert into discipline (discipline, discipline_name) values ('trans', 'Transportasi');
insert into discipline (discipline, discipline_name) values ('-belum', '*Belum berkategori*');

update translation set ref_source = 'SM' where isnull(ref_source);
