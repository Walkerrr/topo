CREATE TABLE `t_demo` (
	`KeyField` CHAR(10) NOT NULL COMMENT '�����ֶ�',
	`Field1` CHAR(20) NULL DEFAULT NULL COMMENT '�����ֶ�1',
	`Field2` CHAR(20) NULL DEFAULT NULL COMMENT '�����ֶ�2',
	`DeleteFlag` CHAR(1) NULL DEFAULT NULL COMMENT '��¼ɾ����־',
	PRIMARY KEY (`KeyField`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;