CREATE TABLE `t_user` (
	`username` CHAR(50) NOT NULL COMMENT '�û�ID',
	`fullname` CHAR(30) NOT NULL COMMENT '�û�ȫ��',
	`email` CHAR(50) NOT NULL COMMENT '��������',
	`mobilephone` CHAR(16) NOT NULL COMMENT '�ƶ��绰',
	`pwdhash` CHAR(128) NULL DEFAULT NULL COMMENT '�û�����ļ���hashֵ',
	`department` CHAR(128) NULL DEFAULT NULL COMMENT '��������',
	`level` CHAR(2) NOT NULL COMMENT '�û�����',
	`isadmin` CHAR(1) NULL DEFAULT NULL COMMENT '����Ա��־',
	`sessionid` CHAR(100) NULL DEFAULT NULL COMMENT '�û���ǰ�ỰID',
	`lastupdate` DATETIME NULL DEFAULT NULL COMMENT '�û����һ�β�������ʱ��',
	`temppwdhash` CHAR(128) NULL DEFAULT NULL COMMENT '������������ʱ��ϣֵ',
	`defaultregion` CHAR(11) NULL DEFAULT NULL COMMENT 'Ĭ�ϵĹ�������',
	`enabled` CHAR(1) NULL DEFAULT NULL COMMENT '�û�����״̬��"0"--δ���� "1"--���� "9"--ɾ��',
	`lastresettime` DATETIME NULL DEFAULT NULL COMMENT '���һ�ο�������ʱ��',
	PRIMARY KEY (`username`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;