drop table if exists sys_abbrev;

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

insert into sys_abbrev (abbrev) select discipline from definition where discipline not regexp '[^[:alnum:]]' group by discipline order by discipline;