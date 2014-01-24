/**
 *
 */

drop table if exists sys_user;

/*==============================================================*/
/* Table: sys_user                                              */
/*==============================================================*/
create table sys_user
(
   user_id              varchar(32) not null,
   pass_key             varchar(32) not null,
   full_name            varchar(255),
   last_access          datetime,
   updated              datetime,
   updater              varchar(32) not null,
   primary key (user_id)
);

alter table definition modify updater varchar(32);
alter table derivation modify updater varchar(32);
alter table derivation_type modify updater varchar(32);
alter table discipline modify updater varchar(32);
alter table lexical_class modify updater varchar(32);
alter table phrase modify updater varchar(32);
alter table relation modify updater varchar(32);
alter table relation_type modify updater varchar(32);

drop table if exists sys_session;

/*==============================================================*/
/* Table: sys_session                                           */
/*==============================================================*/
create table sys_session
(
   ses_id               varchar(32) not null,
   ip_address           varchar(16) not null,
   user_id              varchar(32),
   started              datetime,
   ended                datetime,
   primary key (ses_id)
);

drop table if exists sys_action;

/*==============================================================*/
/* Table: sys_action                                            */
/*==============================================================*/
create table sys_action
(
   ses_id               varchar(32) not null,
   action_time          datetime not null,
   action_type          varchar(16),
   module               varchar(16),
   description          varchar(4000),
   primary key (action_time, ses_id)
);

insert into relation_type (rel_type, rel_type_name, sort_order) values ('f', 'Imbuhan', 4);
insert into relation_type (rel_type, rel_type_name, sort_order) values ('c', 'Majemuk', 5);
update relation_type set rel_type_name = 'Berkaitan' where rel_type = 'r';

delete from relation where rel_type in ('f', 'c');

insert into relation (root_phrase, related_phrase, rel_type)
select root_phrase, derived_phrase, 'f' from derivation where drv_type = 'a';

insert into relation (root_phrase, related_phrase, rel_type)
select root_phrase, derived_phrase, 'c' from derivation where drv_type = 'c';

drop table if exists derivation_type;
drop table if exists derivation;

truncate table discipline;
insert into discipline (discipline, discipline_name) values ('admpub', 'Administrasi publik');
insert into discipline (discipline, discipline_name) values ('avi', 'Aviasi');
insert into discipline (discipline, discipline_name) values ('bio', 'Biologi');
insert into discipline (discipline, discipline_name) values ('ekon', 'Ekonomi');
insert into discipline (discipline, discipline_name) values ('fis', 'Fisika');
insert into discipline (discipline, discipline_name) values ('kim', 'Kimia');
insert into discipline (discipline, discipline_name) values ('ling', 'Linguistik');
insert into discipline (discipline, discipline_name) values ('mat', 'Matematika');
insert into discipline (discipline, discipline_name) values ('or', 'Olahraga');
insert into discipline (discipline, discipline_name) values ('par', 'Pariwisata');
insert into discipline (discipline, discipline_name) values ('pol', 'Politik');
insert into discipline (discipline, discipline_name) values ('seni', 'Seni');
insert into discipline (discipline, discipline_name) values ('ti', 'Teknologi informasi');

drop table if exists translation;

/*==============================================================*/
/* Table: translation                                           */
/*==============================================================*/
create table translation
(
   tr_uid               int not null auto_increment,
   phrase               varchar(255) not null,
   translation          varchar(255) not null,
   discipline           varchar(16),
   lang                 varchar(16) not null default 'en',
   updated              datetime,
   updater              varchar(32) not null,
   primary key (tr_uid)
);

/*==============================================================*/
/* Index: phrase                                                */
/*==============================================================*/
create index phrase on translation
(
   phrase
);

/*==============================================================*/
/* Index: translation                                           */
/*==============================================================*/
create index translation on translation
(
   translation
);

drop table if exists language;

/*==============================================================*/
/* Table: language                                              */
/*==============================================================*/
create table language
(
   lang                 varchar(16) not null,
   lang_name            varchar(255),
   updated              datetime,
   updater              varchar(32) not null,
   primary key (lang)
);

insert into language (lang, lang_name) values ('en', 'Bahasa Inggris');
insert into language (lang, lang_name) values ('id', 'Bahasa Indonesia');
