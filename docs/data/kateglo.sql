-- 2009-06-27 12:00

drop table if exists definition;

drop table if exists discipline;

drop table if exists external_ref;

drop table if exists glossary;

drop table if exists kbbi;

drop table if exists language;

drop table if exists lexical_class;

drop table if exists new_lemma;

drop table if exists phrase;

drop table if exists phrase_type;

drop table if exists proverb;

drop table if exists ref_source;

drop table if exists relation;

drop table if exists relation_type;

drop table if exists roget_class;

drop table if exists searched_phrase;

drop table if exists sys_abbrev;

drop table if exists sys_action;

drop table if exists sys_cache;

drop table if exists sys_comment;

drop table if exists sys_session;

drop table if exists sys_user;

drop table if exists translation;

/*==============================================================*/
/* Table: definition                                            */
/*==============================================================*/
create table definition
(
   def_uid              int not null auto_increment,
   phrase               varchar(255) not null,
   def_num              int not null default 1,
   lex_class            varchar(16),
   discipline           varchar(16),
   def_text             varchar(4000) not null,
   sample               varchar(4000),
   see                  varchar(255),
   updated              datetime,
   updater              varchar(32),
   primary key (def_uid)
);

/*==============================================================*/
/* Index: phrase                                                */
/*==============================================================*/
create index phrase on definition
(
   phrase
);

/*==============================================================*/
/* Table: discipline                                            */
/*==============================================================*/
create table discipline
(
   discipline           varchar(16) not null,
   discipline_name      varchar(255) not null,
   glossary_count       int not null default 0,
   updated              datetime,
   updater              varchar(32),
   primary key (discipline)
);

/*==============================================================*/
/* Table: external_ref                                          */
/*==============================================================*/
create table external_ref
(
   ext_uid              int not null auto_increment,
   phrase               varchar(255) not null,
   label                varchar(255),
   url                  varchar(255) not null,
   updated              datetime,
   updater              varchar(32),
   primary key (ext_uid)
);

/*==============================================================*/
/* Table: glossary                                              */
/*==============================================================*/
create table glossary
(
   glo_uid              int not null auto_increment,
   phrase               varchar(255) not null,
   original             varchar(255) not null,
   discipline           varchar(16),
   lang                 varchar(16) not null default 'en',
   ref_source           varchar(16),
   wpid                 varchar(255),
   wpen                 varchar(255),
   updated              datetime,
   updater              varchar(32),
   wikipedia_updated    datetime,
   primary key (glo_uid)
);

/*==============================================================*/
/* Index: phrase                                                */
/*==============================================================*/
create index phrase on glossary
(
   phrase
);

/*==============================================================*/
/* Index: original_phrase                                       */
/*==============================================================*/
create index original_phrase on glossary
(
   original
);

/*==============================================================*/
/* Index: discipline                                            */
/*==============================================================*/
create index discipline on glossary
(
   discipline
);

/*==============================================================*/
/* Index: ref_source                                            */
/*==============================================================*/
create index ref_source on glossary
(
   ref_source
);

/*==============================================================*/
/* Table: kbbi                                                  */
/*==============================================================*/
create table kbbi
(
   lemma                varchar(255) collate latin1_bin not null,
   content              text,
   primary key (lemma)
);

/*==============================================================*/
/* Table: language                                              */
/*==============================================================*/
create table language
(
   lang                 varchar(16) not null,
   lang_name            varchar(255),
   updated              datetime,
   updater              varchar(32),
   primary key (lang)
);

/*==============================================================*/
/* Table: lexical_class                                         */
/*==============================================================*/
create table lexical_class
(
   lex_class            varchar(16) not null,
   lex_class_name       varchar(255) not null,
   lex_class_ref        varchar(255) comment 'Referensi ke nama kelas',
   sort_order           int not null default 1,
   updated              datetime,
   updater              varchar(32),
   primary key (lex_class)
);

/*==============================================================*/
/* Table: new_lemma                                             */
/*==============================================================*/
create table new_lemma
(
   new_lemma            varchar(255) not null,
   glossary_count       int not null default 0,
   is_exists            tinyint not null default 0,
   is_valid             tinyint not null default 0,
   primary key (new_lemma)
);

/*==============================================================*/
/* Table: phrase                                                */
/*==============================================================*/
create table phrase
(
   phrase               varchar(255) not null,
   phrase_type          varchar(16) not null default 'r' comment 'r=root; f=affix; c=compond',
   lex_class            varchar(16) not null,
   roget_class          varchar(16),
   pronounciation       varchar(4000),
   etymology            varchar(4000),
   ref_source           varchar(16),
   def_count            int not null default 0,
   actual_phrase        varchar(255),
   info                 varchar(255) comment 'Additional information',
   notes                varchar(4000) comment 'Additional notes',
   updated              datetime,
   updater              varchar(32),
   created              datetime,
   creator              varchar(32),
   proverb_updated      datetime,
   wikipedia_updated    datetime,
   kbbi_updated         datetime,
   primary key (phrase)
);

/*==============================================================*/
/* Table: phrase_type                                           */
/*==============================================================*/
create table phrase_type
(
   phrase_type          varchar(16) not null comment 'r=root; f=affix; c=compond',
   phrase_type_name     varchar(255) not null,
   sort_order           int not null default 1,
   updated              datetime,
   updater              varchar(32),
   primary key (phrase_type)
);

/*==============================================================*/
/* Table: proverb                                               */
/*==============================================================*/
create table proverb
(
   prv_uid              int not null auto_increment,
   phrase               varchar(255) not null,
   proverb              varchar(4000) not null,
   meaning              varchar(4000),
   prv_type             int default 0 comment '0=wrong; 1=proverb; 2=kiasan',
   updated              datetime,
   updater              varchar(32),
   primary key (prv_uid)
);

/*==============================================================*/
/* Table: ref_source                                            */
/*==============================================================*/
create table ref_source
(
   ref_source           varchar(16) not null,
   ref_source_name      varchar(255) not null,
   dictionary           tinyint default 0,
   glossary             tinyint default 0,
   translation          tinyint default 0,
   glossary_count       int not null default 0,
   updated              datetime,
   updater              varchar(32),
   primary key (ref_source)
)
comment = "Reference source";

/*==============================================================*/
/* Table: relation                                              */
/*==============================================================*/
create table relation
(
   rel_uid              int not null auto_increment,
   root_phrase          varchar(255) not null,
   related_phrase       varchar(255) not null,
   rel_type             varchar(16) not null,
   updated              datetime,
   updater              varchar(32),
   primary key (rel_uid),
   key AK_relation_unique ()
);

/*==============================================================*/
/* Index: relation_unique                                       */
/*==============================================================*/
create unique index relation_unique on relation
(
   root_phrase,
   related_phrase,
   rel_type
);

/*==============================================================*/
/* Table: relation_type                                         */
/*==============================================================*/
create table relation_type
(
   rel_type             varchar(16) not null comment 's=synonym, a=antonym, o=other',
   rel_type_name        varchar(255) not null,
   sort_order           int not null default 1,
   updated              datetime,
   updater              varchar(32),
   primary key (rel_type)
);

/*==============================================================*/
/* Table: roget_class                                           */
/*==============================================================*/
create table roget_class
(
   roget_class          varchar(16) not null,
   number               varchar(16),
   suffix               varchar(16),
   roget_name           varchar(255),
   english_name         varchar(255),
   asterix              varchar(16),
   caret                varchar(16),
   class_num            int,
   division_num         int,
   section_num          int,
   primary key (roget_class)
);

/*==============================================================*/
/* Table: searched_phrase                                       */
/*==============================================================*/
create table searched_phrase
(
   phrase               varchar(255) not null,
   phrase_type          varchar(16) not null comment 'r=root; f=affix; c=compond',
   search_count         int not null default 0,
   last_searched        datetime not null,
   primary key (phrase, phrase_type)
);

/*==============================================================*/
/* Table: sys_abbrev                                            */
/*==============================================================*/
create table sys_abbrev
(
   abbrev               varchar(16) not null,
   label                varchar(255),
   type                 varchar(16) comment 'lang, discipline, usage',
   updated              datetime,
   updater              varchar(32),
   primary key (abbrev)
);

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
   primary key (cache_uid),
   key phrase_ak (cache_type, phrase)
);

/*==============================================================*/
/* Table: sys_comment                                           */
/*==============================================================*/
create table sys_comment
(
   comment_id           int not null auto_increment,
   ses_id               varchar(32),
   sender_name          varchar(255) not null,
   sender_email         varchar(255) not null,
   url                  varchar(255),
   status               tinyint not null default 0 comment '0=hidden; 1=show',
   sent_date            datetime not null,
   comment_text         varchar(4000),
   response             varchar(4000),
   primary key (comment_id)
);

/*==============================================================*/
/* Table: sys_session                                           */
/*==============================================================*/
create table sys_session
(
   ses_id               varchar(32) not null,
   ip_address           varchar(16) not null,
   user_id              varchar(32),
   user_agent           varchar(255),
   started              datetime,
   ended                datetime,
   last                 datetime,
   page_view            int not null default 0,
   primary key (ses_id)
);

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
   updater              varchar(32),
   primary key (user_id)
);

/*==============================================================*/
/* Table: translation                                           */
/*==============================================================*/
create table translation
(
   lemma                varchar(255) not null,
   ref_source           varchar(16) not null,
   translation          varchar(4000),
   primary key (lemma, ref_source)
);
