alter table sys_comment add url varchar(255) after sender_email;
alter table sys_comment add status tinyint not null default 0 comment '0=hidden; 1=show' after url;
alter table sys_comment add sent_date datetime not null after status;
alter table sys_comment add response varchar(4000) after comment_text;

update discipline a
set a.glossary_count = (SELECT COUNT(b.glo_uid) FROM glossary b WHERE a.discipline = b.discipline)