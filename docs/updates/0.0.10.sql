drop table if exists sys_comment;

/*==============================================================*/
/* Table: sys_comment                                           */
/*==============================================================*/
create table sys_comment
(
   comment_id           int not null auto_increment,
   ses_id               varchar(32),
   sender_name          varchar(255) not null,
   sender_email         varchar(255) not null,
   comment_text         varchar(4000),
   primary key (comment_id)
);
