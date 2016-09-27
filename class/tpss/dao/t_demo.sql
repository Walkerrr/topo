CREATE TABLE `t_demo` (
	`KeyField` CHAR(10) NOT NULL COMMENT 'Ö÷¼ü×Ö¶Î',
	`Field1` CHAR(20) NULL DEFAULT NULL COMMENT 'ÑùÀý×Ö¶Î1',
	`Field2` CHAR(20) NULL DEFAULT NULL COMMENT 'ÑùÀý×Ö¶Î2',
	`DeleteFlag` CHAR(1) NULL DEFAULT NULL COMMENT '¼ÇÂ¼É¾³ý±êÖ¾',
	PRIMARY KEY (`KeyField`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;