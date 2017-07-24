alter table word_images add md5_duplicate_helper varchar(255);
update word_images set md5_duplicate_helper = md5(id);
alter table word_images add unique key idx_md5_duplicate_helper (md5_duplicate_helper);

/* 17/7 */
alter table word_images add image_type char(1);
update word_images set image_type='g' where url like '%.gif%';
update word_images set image_type='i' where image_type is null;

/* 21/7 */
alter table dictionary modify is_noun tinyint(1);
