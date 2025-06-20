=== KeepSpace ===
Contributors: cottboy
Tags: space character, space, Preserve spaces, Keep spaces, Retain spaces
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.4
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

自动将空格转换为特殊字符空格，防止HTML省略空格，特别适合中文内容编辑。

== Description ==

KeepSpace是一款专为解决WordPress中空格被自动省略问题而设计的插件。WordPress默认会省略HTML中的连续空格和开头结尾空格，这在中文写作中非常不便。

**主要功能：**

* 自动将普通空格转换为特殊字符空格
* 支持标题、摘要、正文、评论四个独立开关
* 提供三种特殊空格类型选择
* 兼容经典编辑器和古腾堡编辑器
* 支持中英文界面

**三种特殊空格类型：**

1. **不断行空格 (\u00A0)** - 推荐
   * 只算1个字符，不影响摘要截取
   * 与&nbsp;显示效果相同
   * 兼容性最好

2. **中文全角空格 (\u3000)**
   * 只算1个字符
   * 在中文环境下非常自然
   * 宽度正好是一个中文字符

3. **HTML实体空格 (&nbsp;)**
   * 兼容性最好，所有浏览器都支持
   * 算5个字符，会影响摘要截取

**适用场景：**

* 中文博客和网站
* 需要精确控制空格显示的内容
* 段落缩进和格式化需求

== Installation ==

1. 上传插件文件到 `/wp-content/plugins` 目录
2. 在WordPress管理后台的"插件"菜单中激活插件
3. 进入"设置" > "KeepSpace"配置插件选项

== Frequently Asked Questions ==

= 插件会影响网站性能吗？ =

插件只在内容保存时进行处理，对网站性能影响非常小，几乎可以忽略不计。

= 更改空格类型后，现有内容会自动更新吗？ =

不会。更改特殊空格类型只对新保存的内容生效，现有内容中的特殊空格不会自动更改。

== Changelog ==

= 1.0.4 =
* 上线wordpress插件商店
* 支持标题、摘要、正文、评论空格保护
* 提供三种特殊空格类型选择

== Upgrade Notice ==

= 1.0.4 =
上线wordpress插件商店