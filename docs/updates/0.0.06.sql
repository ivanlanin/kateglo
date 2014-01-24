drop table if exists roget_class;

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
   class_num            tinyint,
   division_num         tinyint,
   section_num          tinyint,
   primary key (roget_class)
);


alter table phrase add roget_class varchar(16) after lex_class;
alter table sys_session add user_agent varchar(255) after user_id;
alter table sys_session add last datetime;
alter table sys_session add page_view tinyint not null default 0;

insert into ref_source (ref_source, ref_source_name) values ('DS', 'Daisy Subakti');

update translation set ref_source = 'DS', discipline = 'huk' where isnull(ref_source); -- 6428
