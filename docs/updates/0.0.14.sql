alter table phrase add proverb_updated datetime;

update phrase set proverb_updated = NOW() where isnull(proverb_updated) AND phrase IN (select phrase from proverb);

drop table if exists proverb;

/*==============================================================*/
/* Table: proverb                                               */
/*==============================================================*/
create table proverb
(
   prv_uid              int not null auto_increment,
   phrase               varchar(255) not null,
   proverb              varchar(4000) not null,
   meaning              varchar(4000),
   updated              datetime,
   updater              varchar(32) not null,
   prv_type             int default 0 comment '0=wrong; 1=proverb; 2=kiasan',
   primary key (prv_uid)
);

update proverb set prv_type = 0;
update proverb set prv_type = 2 where proverb regexp ', ki$';
update proverb set prv_type = 1 where prv_type != 2 and not isnull(meaning);

update proverb set proverb = replace(proverb, '-', phrase) where proverb regexp '^-' and prv_type = 1