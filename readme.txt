=== KeepSpace ===
Contributors: cottboy
Tags: space-character, space, Preserve-spaces, Keep-spaces, Retain-spaces
Requires at least: 5.0
Tested up to: 6.8
Stable tag: 1.0.5
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

自动将普通空格转换为特殊字符空格，防止被省略。

== Description ==

WordPress默认会省略连续空格和开头结尾空格，KeepSpace能自动将普通空格转换为特殊字符空格，防止被省略。

**主要功能：**

* 自动将普通空格转换为特殊字符空格
* 支持标题、摘要、正文、评论四个独立开关
* 提供三种特殊空格类型选择

**三种特殊空格类型：**

1. **不断行空格 (\u00A0)** - 推荐
   * 与&nbsp;显示效果相同
   * 只算1个字符，不影响摘要截取

2. **中文全角空格 (\u3000)**
   * 在中文环境下非常自然
   * 宽度正好是一个中文字符

3. **HTML实体空格 (&nbsp;)**
   * 兼容性最好，所有浏览器都支持
   * 算5个字符，会影响摘要截取

== Installation ==

1. 上传插件文件到 `/wp-content/plugins` 目录
2. 在WordPress管理后台"插件"菜单中激活插件
3. 进入"设置" > "KeepSpace"配置插件选项

== Frequently Asked Questions ==

= 更改空格类型后，现有内容会自动更新吗？ =

不会。更改特殊空格类型只对新保存的内容生效，现有内容中的特殊空格不会自动更改。

== Changelog ==

= 1.0.5（2025-10-13） =
* 优化空格替换逻辑，减少误拦截其他表单的可能性

= 1.0.4（2025-6-20） =
* 上线wordpress插件商店
* 支持标题、摘要、正文、评论空格保护
* 提供三种特殊空格类型选择

== Upgrade Notice ==

= 1.0.5 =
* 优化空格替换逻辑，减少误拦截其他表单的可能性
