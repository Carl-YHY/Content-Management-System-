
# 创建数据库
CREATE DATABASE `itcast_cms`;

# 选择数据
USE `itcast_cms`;

# 文章表
CREATE TABLE `cms_article`(
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `cid` INT UNSIGNED NOT NULL COMMENT '栏目ID',
  `title` VARCHAR(80) NOT NULL COMMENT '标题',
  `author` VARCHAR(15) NOT NULL COMMENT '作者',
  `thumb` VARCHAR(255) NOT NULL COMMENT '封面图',
  `show` ENUM('yes','no') DEFAULT 'yes' NOT NULL COMMENT '是否发布',
  `views` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '点击量',
  `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT '创建时间',
  `content` TEXT NOT NULL COMMENT '内容',
  `keywords` VARCHAR(150) NOT NULL COMMENT '关键字',
  `description` VARCHAR(255) NOT NULL COMMENT '内容简介'
)DEFAULT CHARSET=utf8;

# 栏目表
CREATE TABLE `cms_category`(
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `pid` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID',
  `name` VARCHAR(15) NOT NULL COMMENT '名称',
  `sort` INT NOT NULL DEFAULT 0 COMMENT '排序'
)DEFAULT CHARSET=utf8;

# 管理员表
CREATE TABLE `cms_admin`(
  `id` INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(10) NOT NULL UNIQUE COMMENT '用户名',
  `password` CHAR(32) NOT NULL COMMENT '密码',
  `salt` CHAR(6) NOT NULL COMMENT '密钥'
)DEFAULT CHARSET=utf8;

# 添加管理员数据
INSERT INTO `cms_admin` VALUES(1, 'admin', MD5(CONCAT(MD5('123456'),'itCAst')), 'itCAst');

# 添加默认栏目数据
INSERT INTO `cms_category` VALUES 
(1, 0, '生活', 0),
(2, 0, '资讯', 1),
(3, 0, '编程', 2),
(4, 0, '互联网', 3),
(5, 0, '科技', 4),
(6, 3, 'PHP', 0),
(7, 3, 'Java', 1),
(8, 3, 'Android', 2),
(9, 2, '新闻资讯', 0),
(10, 2, '教育动态', 0),
(11, 2, '学术观点', 0),
(12, 2, '实事点评', 0),
(16, 4, '网络理财', 0),
(13, 1, '厨艺', 0),
(14, 1, '时装', 0),
(15, 5, '硬件', 0);

# 添加默认文章数据
INSERT INTO `cms_article` VALUES
(1, 6, '这是第一篇文章', '传智播客', '', 'yes', '0', now(),
 '<p>欢迎使用 PHP内容管理系统！</p><p>这是一篇系统自动生成的文章，您可以修改或删除。</p>',
 'PHP,内容,管理','欢迎使用 PHP内容管理系统。'),
(2, 6, '最涨薪PHP项目—PHP微信公众平台开发', '博学谷', '2016-05/16/ed27a1ba3b93801cde7a4d0f2ff26958.png', 'yes', '0', now(),'','','在“智能手机”时代，没有人不识微信！'),
(3, 6, 'PHP进阶：要想提高PHP的编程效率，你必须知道的49个要点', '博学谷', '', 'yes', '0', now(),'','',''),
(4, 6, '想少走弯路，就看看这个贴：PHPer职业发展规划与技能需求！', '博学谷', '', 'yes', '0', now(),'','',''),
(5, 6, '前端必学框架Bootstrap，3天带你从入门到精通，免费分享！', '博学谷', '', 'yes', '0', now(),'','',''),
(6, 6, 'PHP学科：MySQL手册免费分享', '博学谷', '', 'yes', '0', now(),'','','');
