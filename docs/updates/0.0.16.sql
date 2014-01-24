alter table phrase add wikipedia_updated datetime;

update phrase SET wikipedia_updated = NOW() where phrase in (select phrase from external_ref);

insert into external_ref (phrase, label, url, updated) values ('R', 'R - Wikipedia bahasa Indonesia', 'http://id.wikipedia.org/wiki/R', NOW());
insert into external_ref (phrase, label, url, updated) values ('R', 'R - Wikipedia bahasa Inggris', 'http://en.wikipedia.org/wiki/R', NOW());