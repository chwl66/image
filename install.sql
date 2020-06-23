SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for hipic_api
-- ----------------------------
DROP TABLE IF EXISTS `hipic_api`;
CREATE TABLE `hipic_api`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_ok` tinyint(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否使用',
  `weight` int(10) UNSIGNED NOT NULL DEFAULT 100 COMMENT '权重',
  `checked` tinyint(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否选择',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `key`(`key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 40 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_api
-- ----------------------------
INSERT INTO `hipic_api` VALUES (1, '本地', 'this', 1, 100, 1);
INSERT INTO `hipic_api` VALUES (2, '阿里图床', 'ali', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (3, 'Vim_Cn', 'vimcn', 1, 1000, 0);
INSERT INTO `hipic_api` VALUES (4, 'Upload_Cc', 'uploadcc', 1, 90, 0);
INSERT INTO `hipic_api` VALUES (6, 'SMMS', 'smms', 1, 70, 0);
INSERT INTO `hipic_api` VALUES (8, '搜狗', 'sougou', 1, 70, 0);
INSERT INTO `hipic_api` VALUES (9, '小米', 'xiaomi', 1, 1000, 0);
INSERT INTO `hipic_api` VALUES (10, 'Catbox', 'catbox', 1, 80, 0);
INSERT INTO `hipic_api` VALUES (11, '奇虎', 'qihoo', 1, 70, 0);
INSERT INTO `hipic_api` VALUES (12, '京东', 'jd', 1, 60, 0);
INSERT INTO `hipic_api` VALUES (13, '苏宁', 'suning', 1, 60, 0);
INSERT INTO `hipic_api` VALUES (15, '掘金论坛', 'juejin', 1, 50, 0);
INSERT INTO `hipic_api` VALUES (19, '网易', 'neteasy', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (20, '头条', 'toutiao', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (21, '牛图', 'niupic', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (22, 'FTP', 'ftp', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (24, '百度', 'baidu', 1, 70, 0);
INSERT INTO `hipic_api` VALUES (25, '葫芦侠', 'huluxia', 1, 80, 0);
INSERT INTO `hipic_api` VALUES (26, '腾讯', 'qpic', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (27, 'Chevereto图床', 'chevereto', 0, 100, 0);
INSERT INTO `hipic_api` VALUES (28, 'Qdoc', 'qdoc', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (29, 'Qcoral', 'qcoral', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (30, 'Bcebos', 'bcebos', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (31, 'bilibili', 'bilibili', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (33, 'bjbcebos', 'bjbcebos', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (34, 'Ouliu', 'ouliu', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (35, 'Postimages', 'postimages', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (36, 'UPYUN', 'upyun', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (37, 'Sina', 'sina', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (38, 'HidoveApi', 'phpcdn', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (39, 'Chaoxing', 'chaoxing', 1, 100, 0);
INSERT INTO `hipic_api` VALUES (40, 'v6直播', 'v6', 1, 100, 0);

-- ----------------------------
-- Table structure for hipic_api_request
-- ----------------------------
DROP TABLE IF EXISTS `hipic_api_request`;
CREATE TABLE `hipic_api_request`  (
  `id` int(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'api唯一标识',
  `create_time` int(20) NULL DEFAULT 0,
  `update_time` int(20) NULL DEFAULT 0,
  `total_request_times` int(20) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_api_request
-- ----------------------------

-- ----------------------------
-- Table structure for hipic_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `hipic_blacklist`;
CREATE TABLE `hipic_blacklist`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `referer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '来源域名',
  `reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `image` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(15) NOT NULL,
  `duration` int(15) NOT NULL DEFAULT 300,
  `fraction` int(11) NOT NULL COMMENT '图片评分',
  `release_time` int(15) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_blacklist
-- ----------------------------

-- ----------------------------
-- Table structure for hipic_folders
-- ----------------------------
DROP TABLE IF EXISTS `hipic_folders`;
CREATE TABLE `hipic_folders`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '上级文件夹ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '文件夹名称',
  `delete_time` int(11) NULL DEFAULT NULL COMMENT '删除时间',
  `update_time` int(11) NULL DEFAULT NULL COMMENT '更新时间',
  `create_time` int(11) NULL DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件夹表' ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_folders
-- ----------------------------

-- ----------------------------
-- Table structure for hipic_group
-- ----------------------------
DROP TABLE IF EXISTS `hipic_group`;
CREATE TABLE `hipic_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `capacity` bigint(255) UNSIGNED NOT NULL DEFAULT 1073741824 COMMENT '字节默认1GB',
  `storage` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '储存策略',
  `price` int(10) NOT NULL COMMENT '用户升级用户组消耗余额',
  `frequency` int(10) NOT NULL DEFAULT 0 COMMENT '1小时内上传图片数',
  `picture_process` int(1) NOT NULL DEFAULT 0 COMMENT '图片处理',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_group
-- ----------------------------
INSERT INTO `hipic_group` VALUES (1, '默认组', 10737418240, 'this', 0, -1, 1);
INSERT INTO `hipic_group` VALUES (2, '管理组', 107374182400, 'this', 2147483647, -1, 1);
INSERT INTO `hipic_group` VALUES (3, 'VIP组', 21474836480, 'zzidc', 10, 0, 1);
INSERT INTO `hipic_group` VALUES (6, 'VIP21组', 10737418240, 'this', 99999999, -1, 0);
INSERT INTO `hipic_group` VALUES (7, 'VIP41组', 10737418240, 'this', 99999999, -1, 0);

-- ----------------------------
-- Table structure for hipic_image
-- ----------------------------
DROP TABLE IF EXISTS `hipic_image`;
CREATE TABLE `hipic_image`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `signatures` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `storage_key` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'this',
  `url` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `pathname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '目录地址',
  `user_id` int(20) NOT NULL DEFAULT 0,
  `folder_id` int(15) UNSIGNED NOT NULL DEFAULT 0 COMMENT '目录',
  `filename` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `fraction` decimal(5, 2) NOT NULL DEFAULT 0.00 COMMENT '是否违规',
  `image_type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `mime` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `file_size` int(255) NOT NULL,
  `sha1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `md5` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(20) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(20) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.0.0.0',
  `today_request_times` int(20) NOT NULL DEFAULT 0,
  `total_request_times` int(20) NOT NULL DEFAULT 0 COMMENT '今日请求数',
  `final_request_time` int(20) NOT NULL DEFAULT 0 COMMENT '最后请求时间',
  `is_invalid` int(1) NOT NULL DEFAULT 0 COMMENT '是否失效',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `signatures`(`signatures`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_image
-- ----------------------------

-- ----------------------------
-- Table structure for hipic_image_request
-- ----------------------------
DROP TABLE IF EXISTS `hipic_image_request`;
CREATE TABLE `hipic_image_request`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `referer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '来源地址',
  `create_time` int(20) NOT NULL COMMENT '第一次请求时间',
  `final_request_time` int(20) NOT NULL COMMENT '最后一次请求时间',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.0.0.0' COMMENT '请求的ip地址',
  `today_request_times` int(20) NOT NULL COMMENT '今日请求次数',
  `total_request_times` int(20) NOT NULL DEFAULT 0 COMMENT '请求次数',
  PRIMARY KEY (`id`, `referer`) USING BTREE,
  UNIQUE INDEX `referer`(`referer`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_image_request
-- ----------------------------

-- ----------------------------
-- Table structure for hipic_recharge_card
-- ----------------------------
DROP TABLE IF EXISTS `hipic_recharge_card`;
CREATE TABLE `hipic_recharge_card`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卡密',
  `denomination` int(20) NOT NULL DEFAULT 0 COMMENT '面额',
  `user_id` int(11) NULL DEFAULT 0 COMMENT '使用者ID',
  `create_time` int(11) NULL DEFAULT 0 COMMENT '生成时间',
  `used_time` int(11) NULL DEFAULT 0 COMMENT '使用时间',
  PRIMARY KEY (`id`, `key`) USING BTREE,
  UNIQUE INDEX `key`(`key`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of hipic_recharge_card
-- ----------------------------

-- ----------------------------
-- Table structure for hipic_set
-- ----------------------------
DROP TABLE IF EXISTS `hipic_set`;
CREATE TABLE `hipic_set`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '键名',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '值',
  `group_id` int(20) NOT NULL DEFAULT 1,
  `decode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  `encode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 86 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of hipic_set
-- ----------------------------
INSERT INTO `hipic_set` VALUES (1, 'system.base.sitename', 'hidove', 1, '', '');
INSERT INTO `hipic_set` VALUES (2, 'system.base.subtitle', 'Simple Free Image Hosting', 1, '', '');
INSERT INTO `hipic_set` VALUES (3, 'system.base.touristsUpload', '1', 1, '', '');
INSERT INTO `hipic_set` VALUES (4, 'system.base.email', 'i#abcyun.cc', 1, '', '');
INSERT INTO `hipic_set` VALUES (5, 'system.base.cdnjs', '//lib.baomitu.com', 1, '', '');
INSERT INTO `hipic_set` VALUES (6, 'system.base.keywords', 'Hidove图床,免费图床,图床Api,图片外链', 1, '', '');
INSERT INTO `hipic_set` VALUES (7, 'system.base.description', 'Hidove图床, 免费公共图床, 提供图片上传和图片外链服务, 原图保存, 隐私相册, 全球CDN加速.', 1, '', '');
INSERT INTO `hipic_set` VALUES (8, 'system.base.footer_code', 'PGRpdiBzdHlsZT0iZGlzcGxheTogbm9uZTsiPgogICAgPHNjcmlwdCB0eXBlPSJ0ZXh0L2phdmFzY3JpcHQiIHNyYz0iaHR0cHM6Ly9zOS5jbnp6LmNvbS96X3N0YXQucGhwP2lkPTEyNzc4NzA3MjAmd2ViX2lkPTEyNzc4NzA3MjAiPjwvc2NyaXB0Pgo8L2Rpdj4=', 1, 'base64_decode', 'base64_encode');
INSERT INTO `hipic_set` VALUES (9, 'system.base.openRegistration', '1', 1, '', '');
INSERT INTO `hipic_set` VALUES (10, 'system.upload.imageType', 'jpg,png,gif,jpeg,ico,webp', 2, '', '');
INSERT INTO `hipic_set` VALUES (11, 'system.upload.maxImageSize', '5242880', 2, '', '');
INSERT INTO `hipic_set` VALUES (12, 'system.upload.maxFileCount', '10', 2, '', '');
INSERT INTO `hipic_set` VALUES (13, 'system.upload.suffix', 'jpg', 2, '', '');
INSERT INTO `hipic_set` VALUES (14, 'system.upload.rule', 'Y/m/d', 2, '', '');
INSERT INTO `hipic_set` VALUES (20, 'system.audit.switch', '0', 3, '', '');
INSERT INTO `hipic_set` VALUES (21, 'system.audit.fraction', '80', 3, '', '');
INSERT INTO `hipic_set` VALUES (22, 'system.audit.duration', '5000', 3, '', '');
INSERT INTO `hipic_set` VALUES (23, 'system.audit.action', '1', 3, '', '');
INSERT INTO `hipic_set` VALUES (24, 'system.audit.type', 'moderatecontent', 3, '', '');
INSERT INTO `hipic_set` VALUES (25, 'system.audit.moderatecontent.key', 'hidove', 3, '', '');
INSERT INTO `hipic_set` VALUES (26, 'system.audit.sightengine.api_user', '', 3, '', '');
INSERT INTO `hipic_set` VALUES (27, 'system.audit.sightengine.api_secret', '', 3, '', '');
INSERT INTO `hipic_set` VALUES (28, 'system.audit.baidu.client_id', '', 3, '', '');
INSERT INTO `hipic_set` VALUES (29, 'system.audit.baidu.client_secret', '', 3, '', '');
INSERT INTO `hipic_set` VALUES (30, 'system.audit.tencent.appid', '', 3, '', '');
INSERT INTO `hipic_set` VALUES (31, 'system.audit.tencent.appkey', '', 3, '', '');
INSERT INTO `hipic_set` VALUES (32, 'system.distribute.distribute', '', 4, '', '');
INSERT INTO `hipic_set` VALUES (33, 'system.distribute.suffix', '.jpg', 4, '', '');
INSERT INTO `hipic_set` VALUES (34, 'system.distribute.api', 'local', 4, '', '');
INSERT INTO `hipic_set` VALUES (35, 'system.distribute.httpCode', '200,301,302', 4, '', '');
INSERT INTO `hipic_set` VALUES (38, 'system.distribute.apiUrl', '', 4, '', '');
INSERT INTO `hipic_set` VALUES (39, 'system.imageEdit.imageWatermark.locate', '1', 5, '', '');
INSERT INTO `hipic_set` VALUES (40, 'system.imageEdit.imageWatermark.alpha', '0', 5, '', '');
INSERT INTO `hipic_set` VALUES (41, 'system.imageEdit.textWatermark.text', 'Hidove', 5, '', '');
INSERT INTO `hipic_set` VALUES (42, 'system.imageEdit.textWatermark.font', 'default.ttf', 5, '', '');
INSERT INTO `hipic_set` VALUES (43, 'system.imageEdit.textWatermark.size', '20', 5, '', '');
INSERT INTO `hipic_set` VALUES (44, 'system.imageEdit.textWatermark.color', '#00000000', 5, '', '');
INSERT INTO `hipic_set` VALUES (45, 'system.imageEdit.textWatermark.locate', '9', 5, '', '');
INSERT INTO `hipic_set` VALUES (46, 'system.imageEdit.textWatermark.offset', '0', 5, '', '');
INSERT INTO `hipic_set` VALUES (47, 'system.imageEdit.textWatermark.angle', '0', 5, '', '');
INSERT INTO `hipic_set` VALUES (48, 'system.imageEdit.process.interlace', '0', 5, '', '');
INSERT INTO `hipic_set` VALUES (49, 'system.imageEdit.process.quality', '100', 5, '', '');
INSERT INTO `hipic_set` VALUES (50, 'system.imageEdit.watermark.type', '2', 5, '', '');
INSERT INTO `hipic_set` VALUES (51, 'system.imageEdit.watermark.width', '587', 5, '', '');
INSERT INTO `hipic_set` VALUES (52, 'system.imageEdit.watermark.height', '367', 5, '', '');
INSERT INTO `hipic_set` VALUES (53, 'system.imageEdit.watermark.switch', '0', 5, '', '');
INSERT INTO `hipic_set` VALUES (54, 'system.other.notify', 'IHd3dy5oaWRvdmUuY24=', 10, 'base64_decode', 'base64_encode');
INSERT INTO `hipic_set` VALUES (55, 'system.other.superToken', 'hidove', 10, '', '');
INSERT INTO `hipic_set` VALUES (56, 'system.other.explore', '1', 10, '', '');
INSERT INTO `hipic_set` VALUES (57, 'system.other.apiRecord', '1', 10, '', '');
INSERT INTO `hipic_set` VALUES (58, 'system.other.retentionDomain', 'Ki5oaWRvdmUuY24=', 10, 'base64_decode', 'base64_encode');
INSERT INTO `hipic_set` VALUES (59, 'system.other.shopUrl', 'www.hidove.cn', 10, '', '');
INSERT INTO `hipic_set` VALUES (60, 'system.other.financeNotice', 'IHd3dy5oaWRvdmUuY24=', 10, 'base64_decode', 'base64_encode');
INSERT INTO `hipic_set` VALUES (61, 'system.other.authPath', 'admin', 10, '', '');
INSERT INTO `hipic_set` VALUES (62, 'system.loadBalance.switch', '0', 6, '', '');
INSERT INTO `hipic_set` VALUES (63, 'system.loadBalance.exception', 'github,sina,qpic', 6, '', '');
INSERT INTO `hipic_set` VALUES (64, 'system.loadBalance.node', '', 6, 'base64_decode', 'base64_encode');
INSERT INTO `hipic_set` VALUES (65, 'api.qpic.coral', '', 7, '', '');
INSERT INTO `hipic_set` VALUES (66, 'api.qpic.doc', '', 7, '', '');
INSERT INTO `hipic_set` VALUES (67, 'api.sina.username', '', 7, '', '');
INSERT INTO `hipic_set` VALUES (68, 'api.sina.password', '', 7, '', '');
INSERT INTO `hipic_set` VALUES (69, 'api.baidu.bcebos', '', 7, '', '');
INSERT INTO `hipic_set` VALUES (70, 'api.bilibili.drawImage', '', 7, '', '');
INSERT INTO `hipic_set` VALUES (71, 'system.email.host', 'smtp.mxhichina.com', 8, '', '');
INSERT INTO `hipic_set` VALUES (72, 'system.email.username', 'image@abcyun.cc', 8, '', '');
INSERT INTO `hipic_set` VALUES (74, 'system.email.password', '', 8, '', '');
INSERT INTO `hipic_set` VALUES (75, 'system.email.encryption', 'ssl', 8, '', '');
INSERT INTO `hipic_set` VALUES (76, 'system.email.port', '465', 8, '', '');
INSERT INTO `hipic_set` VALUES (77, 'system.email.sender', 'Hidove图床', 8, '', '');
INSERT INTO `hipic_set` VALUES (78, 'system.email.replyMail', 'admin@qq.com', 8, '', '');
INSERT INTO `hipic_set` VALUES (79, 'system.upload.duplicates.switch', '1', 2, '', '');
INSERT INTO `hipic_set` VALUES (80, 'system.upload.duplicates.time', '1800', 2, '', '');
INSERT INTO `hipic_set` VALUES (81, 'api.chaoxing.cookie', '', 7, '', '');
INSERT INTO `hipic_set` VALUES (82, 'system.upload.returnUrlType', '', 2, '', '');
INSERT INTO `hipic_set` VALUES (83, 'system.distribute.proxy', 'https://images.weserv.nl/?url=', 4, '', '');
INSERT INTO `hipic_set` VALUES (84, 'system.distribute.proxyNode', 'sougou,baidu', 4, '', '');
INSERT INTO `hipic_set` VALUES (85, 'system.loadBalance.min', '2', 6, '', '');

-- ----------------------------
-- Table structure for hipic_set_group
-- ----------------------------
DROP TABLE IF EXISTS `hipic_set_group`;
CREATE TABLE `hipic_set_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '组名，英文、数字',
  `mark` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '一个配置组' COMMENT '备注名，显示在前台',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of hipic_set_group
-- ----------------------------
INSERT INTO `hipic_set_group` VALUES (1, 'system.base', '网站配置');
INSERT INTO `hipic_set_group` VALUES (2, 'system.upload', '上传配置');
INSERT INTO `hipic_set_group` VALUES (3, 'system.audit', '鉴黄');
INSERT INTO `hipic_set_group` VALUES (4, 'system.distribute', '分发');
INSERT INTO `hipic_set_group` VALUES (5, 'system.imageEdit', '图片处理');
INSERT INTO `hipic_set_group` VALUES (10, 'system.other', '其他');
INSERT INTO `hipic_set_group` VALUES (6, 'system.loadBalance', '负载均衡');
INSERT INTO `hipic_set_group` VALUES (7, 'api', '图床接口配置');
INSERT INTO `hipic_set_group` VALUES (8, 'system.email', '发件邮箱');

-- ----------------------------
-- Table structure for hipic_storage
-- ----------------------------
DROP TABLE IF EXISTS `hipic_storage`;
CREATE TABLE `hipic_storage`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `cdn` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `data` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `driver` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(15) NULL DEFAULT NULL,
  `update_time` int(15) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `name`(`name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_storage
-- ----------------------------
INSERT INTO `hipic_storage` VALUES (1, 'ftp', '', '{}', 'ftp', 0, NULL);
INSERT INTO `hipic_storage` VALUES (2, 'ufile', '', '{}', 'ftp', 0, NULL);
INSERT INTO `hipic_storage` VALUES (3, 'cos', '', '{}', 'cos', 0, NULL);
INSERT INTO `hipic_storage` VALUES (4, 'oos', '', '{}', 'oos', 0, NULL);
INSERT INTO `hipic_storage` VALUES (5, 'upyun', '', '{}', 'upyun', 0, NULL);
INSERT INTO `hipic_storage` VALUES (6, 'qiniu', '', '{}', 'qiniu', 0, NULL);
INSERT INTO `hipic_storage` VALUES (7, 'zzidc', '', '{}', 'zzidc', 0, NULL);
INSERT INTO `hipic_storage` VALUES (8, 'github', '', '{}', 'github', 0, NULL);
INSERT INTO `hipic_storage` VALUES (10, 'this', '', '{}', 'this', 0, NULL);

-- ----------------------------
-- Table structure for hipic_user
-- ----------------------------
DROP TABLE IF EXISTS `hipic_user`;
CREATE TABLE `hipic_user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_time` int(20) UNSIGNED NOT NULL,
  `capacity_used` bigint(255) UNSIGNED NOT NULL COMMENT '已用容量',
  `api_folder_id` int(20) NOT NULL DEFAULT 0 COMMENT 'token接口上传的文件夹id',
  `group_id` int(20) UNSIGNED NOT NULL DEFAULT 1 COMMENT '权限级别',
  `reset_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `reset_time` int(15) UNSIGNED NULL DEFAULT NULL,
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_private` int(1) NOT NULL DEFAULT 0 COMMENT '是否在探索显示',
  `forbidden_node` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '[]' COMMENT '禁用节点',
  `is_whitelist` int(1) NULL DEFAULT 0 COMMENT '是否白名单',
  `finance` int(20) NULL DEFAULT 0 COMMENT '用户余额',
  `expiration_date` int(20) NULL DEFAULT 0 COMMENT 'Expiration date用户组过期时间',
  `watermark` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `storage` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE,
  UNIQUE INDEX `token`(`token`) USING BTREE,
  UNIQUE INDEX `email`(`email`) USING BTREE,
  UNIQUE INDEX `reset_key`(`reset_key`) USING BTREE COMMENT '密码重置key'
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of hipic_user
-- ----------------------------
INSERT INTO `hipic_user` VALUES (1, 'admin', '0d6f7e827ade39b9c886128f94b56e1d', 'loliconla@qq.com', '41accc912a75c5a94725021f044f85c9', 1590493513, 0, 1, 2, '95565b84dac5ad49e96e72ec60a96802', 1589984410, '0.0.0.0', 0, '[]', 1, 100202000, 1626344468, '{\"process\":{\"quality\":\"100\",\"interlace\":\"1\"},\"watermark\":{\"height\":\"1\",\"width\":\"1\",\"switch\":\"0\",\"type\":\"2\"},\"imageWatermark\":{\"pathname\":\"watermark\\/c4dfc2ef304c600f8856956891780f82\",\"alpha\":\"0\",\"locate\":\"5\"},\"textWatermark\":{\"text\":\"monica\",\"font\":\"default.ttf\",\"size\":\"20\",\"color\":\"#00000000\",\"offset\":\"0\",\"angle\":\"0\",\"locate\":\"1\"}}', '{}');

SET FOREIGN_KEY_CHECKS = 1;
