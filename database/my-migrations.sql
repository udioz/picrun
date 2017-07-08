alter table word_images add md5_duplicate_helper varchar(255);
update word_images set md5_duplicate_helper = md5(id);
alter table word_images add unique key idx_md5_duplicate_helper (md5_duplicate_helper);
