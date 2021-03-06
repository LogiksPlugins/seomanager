CREATE TABLE `do_seo` (
   `id` int(11) not null auto_increment,
   `guid` varchar(100),
   `page_URI` varchar(255),
   `page_slug` varchar(255),
   `title` varchar(100) not null,
   `descs` varchar(200) DEFAULT '',
   `keywords` varchar(155) DEFAULT '',
   `robots` varchar(70) DEFAULT '',
   `featured_image` varchar(250) DEFAULT '',
   `featured_video` varchar(250) DEFAULT '',
   `blocked` enum('false','true') not null default 'false',
   `created_on` datetime not null,
   `created_by` varchar(255) not null,
   `edited_on` datetime not null,
   `edited_by` varchar(255) not null,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
