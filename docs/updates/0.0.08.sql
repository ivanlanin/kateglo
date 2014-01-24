alter table phrase add created datetime;
alter table phrase add creator varchar(32) not null;
alter table phrase add ref_source varchar(16) after etymology;
update phrase set ref_source = 'Pusba';