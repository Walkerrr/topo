CREATE TABLE `t_user` (
	`username` CHAR(50) NOT NULL COMMENT '用户ID',
	`fullname` CHAR(30) NOT NULL COMMENT '用户全名',
	`email` CHAR(50) NOT NULL COMMENT '电子邮箱',
	`mobilephone` CHAR(16) NOT NULL COMMENT '移动电话',
	`pwdhash` CHAR(128) NULL DEFAULT NULL COMMENT '用户密码的加盐hash值',
	`department` CHAR(128) NULL DEFAULT NULL COMMENT '归属部门',
	`level` CHAR(2) NOT NULL COMMENT '用户级别',
	`isadmin` CHAR(1) NULL DEFAULT NULL COMMENT '管理员标志',
	`sessionid` CHAR(100) NULL DEFAULT NULL COMMENT '用户当前会话ID',
	`lastupdate` DATETIME NULL DEFAULT NULL COMMENT '用户最近一次操作更新时间',
	`temppwdhash` CHAR(128) NULL DEFAULT NULL COMMENT '重置密码后的临时哈希值',
	`defaultregion` CHAR(11) NULL DEFAULT NULL COMMENT '默认的管理区域',
	`enabled` CHAR(1) NULL DEFAULT NULL COMMENT '用户启用状态："0"--未启用 "1"--启用 "9"--删除',
	`lastresettime` DATETIME NULL DEFAULT NULL COMMENT '最近一次口令重置时间',
	PRIMARY KEY (`username`)
)
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;