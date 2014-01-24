/**
 *
 */
drop table if exists searched_phrase;

/*==============================================================*/
/* Table: searched_phrase                                       */
/*==============================================================*/
create table searched_phrase
(
   phrase               varchar(255) not null,
   search_count         int not null default 0,
   last_searched        datetime not null,
   primary key (phrase)
);
