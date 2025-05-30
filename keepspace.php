<?php
/**
 * Plugin Name: KeepSpace
 * Description: 自动将空格转换为特殊字符空格，防止HTML省略空格
 * Version: 1.0.1
 * Author: cottboy
 * Author URI: https://github.com/cottboy
 * Plugin URI: https://github.com/cottboy/keepspace
 * Text Domain: keepspace
 * Domain Path: /languages
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
}

// 初始化国际化
add_action('plugins_loaded', 'keepspace_load_textdomain');
function keepspace_load_textdomain() {
    load_plugin_textdomain('keepspace', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// 插件激活时设置默认选项
register_activation_hook(__FILE__, 'keepspace_activate');
function keepspace_activate() {
    add_option('keepspace_title', '1');
    add_option('keepspace_excerpt', '1');
    add_option('keepspace_content', '1');
    add_option('keepspace_comment', '1');
    add_option('keepspace_space_type', 'unicode_nbsp'); // 默认使用不断行空格
}

// 插件卸载时清理选项
register_uninstall_hook(__FILE__, 'keepspace_uninstall');
function keepspace_uninstall() {
    delete_option('keepspace_title');
    delete_option('keepspace_excerpt');
    delete_option('keepspace_content');
    delete_option('keepspace_comment');
    delete_option('keepspace_space_type');
}

// 添加后台菜单
add_action('admin_menu', 'keepspace_admin_menu');
function keepspace_admin_menu() {
    add_options_page(
        __('KeepSpace 设置', 'keepspace'),
        __('KeepSpace', 'keepspace'),
        'manage_options',
        'keepspace-settings',
        'keepspace_settings_page'
    );
}

// 后台设置页面
function keepspace_settings_page() {
    if (isset($_POST['submit'])) {
        update_option('keepspace_title', isset($_POST['keepspace_title']) ? '1' : '0');
        update_option('keepspace_excerpt', isset($_POST['keepspace_excerpt']) ? '1' : '0');
        update_option('keepspace_content', isset($_POST['keepspace_content']) ? '1' : '0');
        update_option('keepspace_comment', isset($_POST['keepspace_comment']) ? '1' : '0');
        update_option('keepspace_space_type', sanitize_text_field($_POST['keepspace_space_type']));
        echo '<div class="notice notice-success"><p>' . __('设置已保存！', 'keepspace') . '</p></div>';
    }
    
    $title_enabled = get_option('keepspace_title', '1');
    $excerpt_enabled = get_option('keepspace_excerpt', '1');
    $content_enabled = get_option('keepspace_content', '1');
    $comment_enabled = get_option('keepspace_comment', '1');
    $space_type = get_option('keepspace_space_type', 'unicode_nbsp');
    ?>
    <div class="wrap">
        <h1><?php echo __('KeepSpace 设置', 'keepspace'); ?></h1>
        
        <p style="color: #111; font-weight: bold; margin-bottom: 5px;">
            <?php echo __('重要提醒：更改特殊空格类型只对新保存的内容生效，现有内容中的特殊空格不会自动更改。', 'keepspace'); ?>
        </p>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo __('特殊空格类型', 'keepspace'); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="keepspace_space_type" value="unicode_nbsp" <?php checked($space_type, 'unicode_nbsp'); ?> />
                                <strong><?php echo __('不断行空格 (\u00A0)', 'keepspace'); ?></strong> - <?php echo __('推荐', 'keepspace'); ?>
                            </label>
                            <p class="description">
                                <strong><?php echo __('优点：', 'keepspace'); ?></strong><?php echo __('只算1个字符，不影响摘要截取；与&nbsp;显示效果相同；兼容性最好', 'keepspace'); ?><br>
                                <strong><?php echo __('缺点：', 'keepspace'); ?></strong><?php echo __('在极少数老旧系统中可能显示异常', 'keepspace'); ?><br>
                                <strong><?php echo __('适用：', 'keepspace'); ?></strong><?php echo __('大部分情况的最佳选择', 'keepspace'); ?>
                            </p>
                            <br>
                            
                            <label>
                                <input type="radio" name="keepspace_space_type" value="fullwidth_space" <?php checked($space_type, 'fullwidth_space'); ?> />
                                <strong><?php echo __('中文全角空格 (\u3000)', 'keepspace'); ?></strong>
                            </label>
                            <p class="description">
                                <strong><?php echo __('优点：', 'keepspace'); ?></strong><?php echo __('只算1个字符；在中文环境下非常自然；宽度正好是一个中文字符', 'keepspace'); ?><br>
                                <strong><?php echo __('缺点：', 'keepspace'); ?></strong><?php echo __('比普通空格宽，在英文中会显得突兀', 'keepspace'); ?><br>
                                <strong><?php echo __('适用：', 'keepspace'); ?></strong><?php echo __('纯中文内容，特别是段落缩进', 'keepspace'); ?>
                            </p>
                            <br>
                            
                            <label>
                                <input type="radio" name="keepspace_space_type" value="html_nbsp" <?php checked($space_type, 'html_nbsp'); ?> />
                                <strong><?php echo __('&amp;nbsp; - HTML实体空格', 'keepspace'); ?></strong>
                            </label>
                            <p class="description">
                                <strong><?php echo __('优点：', 'keepspace'); ?></strong><?php echo __('兼容性最好；所有浏览器都支持', 'keepspace'); ?><br>
                                <strong><?php echo __('缺点：', 'keepspace'); ?></strong><?php echo __('算5个字符，会影响摘要截取和字符统计', 'keepspace'); ?><br>
                                <strong><?php echo __('适用：', 'keepspace'); ?></strong><?php echo __('对兼容性要求极高，不在意字符计数的情况', 'keepspace'); ?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo __('功能开关', 'keepspace'); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="keepspace_title" value="1" <?php checked($title_enabled, '1'); ?> />
                                <?php echo __('启用标题空格保护', 'keepspace'); ?>
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="keepspace_excerpt" value="1" <?php checked($excerpt_enabled, '1'); ?> />
                                <?php echo __('启用摘要空格保护', 'keepspace'); ?>
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="keepspace_content" value="1" <?php checked($content_enabled, '1'); ?> />
                                <?php echo __('启用正文空格保护', 'keepspace'); ?>
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="keepspace_comment" value="1" <?php checked($comment_enabled, '1'); ?> />
                                <?php echo __('启用评论空格保护', 'keepspace'); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('保存设置', 'keepspace')); ?>
        </form>
    </div>
    <?php
}

// 处理文章保存
add_filter('wp_insert_post_data', 'keepspace_process_post_data', 5, 2);
function keepspace_process_post_data($data, $postarr) {
    // 处理标题 - 使用更强力的处理
    if (get_option('keepspace_title', '1') == '1' && !empty($data['post_title'])) {
        $data['post_title'] = keepspace_replace_spaces_simple($data['post_title']);
    }
    
    // 处理摘要
    if (get_option('keepspace_excerpt', '1') == '1' && !empty($data['post_excerpt'])) {
        $data['post_excerpt'] = keepspace_replace_spaces($data['post_excerpt']);
    }
    
    // 处理正文
    if (get_option('keepspace_content', '1') == '1' && !empty($data['post_content'])) {
        $data['post_content'] = keepspace_replace_spaces($data['post_content']);
    }
    
    return $data;
}

// 最直接的方法：在WordPress开始处理之前就修改POST数据
add_action('init', 'keepspace_modify_post_data');
function keepspace_modify_post_data() {
    // 只在保存文章时处理
    if (isset($_POST['action']) && ($_POST['action'] == 'editpost' || $_POST['action'] == 'post-quickpress-publish')) {
        if (get_option('keepspace_title', '1') == '1' && isset($_POST['post_title'])) {
            $_POST['post_title'] = keepspace_replace_spaces_simple($_POST['post_title']);
        }
    }
    
    // 处理评论提交时的POST数据
    if (isset($_POST['comment']) && get_option('keepspace_comment', '1') == '1') {
        $_POST['comment'] = keepspace_replace_spaces_simple($_POST['comment']);
    }
}

// 处理古腾堡编辑器的REST API请求
add_filter('rest_pre_insert_post', 'keepspace_process_rest_title', 10, 2);
function keepspace_process_rest_title($prepared_post, $request) {
    if (get_option('keepspace_title', '1') == '1' && isset($prepared_post->post_title)) {
        $prepared_post->post_title = keepspace_replace_spaces_simple($prepared_post->post_title);
    }
    return $prepared_post;
}

// 处理评论保存 - 多重钩子确保处理
add_filter('pre_comment_content', 'keepspace_process_comment', 10);
function keepspace_process_comment($comment_content) {
    if (get_option('keepspace_comment', '1') == '1' && !empty($comment_content)) {
        return keepspace_replace_spaces_simple($comment_content);
    }
    return $comment_content;
}

// 额外的评论处理钩子，确保万无一失
add_filter('wp_insert_comment', 'keepspace_process_comment_before_insert', 10, 1);
function keepspace_process_comment_before_insert($commentdata) {
    if (get_option('keepspace_comment', '1') == '1' && !empty($commentdata['comment_content'])) {
        $commentdata['comment_content'] = keepspace_replace_spaces_simple($commentdata['comment_content']);
    }
    return $commentdata;
}

// 智能空格替换函数
function keepspace_replace_spaces($content) {
    // 获取用户选择的空格类型
    $space_type = get_option('keepspace_space_type', 'unicode_nbsp');
    
    // 根据空格类型确定替换字符
    switch ($space_type) {
        case 'unicode_nbsp':
            $replacement = "\u{00A0}"; // 不断行空格 Unicode字符
            break;
        case 'fullwidth_space':
            $replacement = "\u{3000}"; // 中文全角空格
            break;
        case 'html_nbsp':
        default:
            $replacement = '&nbsp;'; // HTML实体空格
            break;
    }
    
    // 分离HTML标签和文本内容
    $parts = preg_split('/(<[^>]*>|<!--.*?-->)/s', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
    
    for ($i = 0; $i < count($parts); $i++) {
        // 只处理非HTML标签和非注释的部分（即纯文本）
        if (!preg_match('/^<.*>$|^<!--.*-->$/s', $parts[$i])) {
            $parts[$i] = str_replace(' ', $replacement, $parts[$i]);
        }
    }
    
    return implode('', $parts);
}

// 简单空格替换函数（专门用于标题）
function keepspace_replace_spaces_simple($content) {
    // 获取用户选择的空格类型
    $space_type = get_option('keepspace_space_type', 'unicode_nbsp');
    
    // 根据空格类型确定替换字符
    switch ($space_type) {
        case 'unicode_nbsp':
            $replacement = "\u{00A0}"; // 不断行空格 Unicode字符
            break;
        case 'fullwidth_space':
            $replacement = "\u{3000}"; // 中文全角空格
            break;
        case 'html_nbsp':
        default:
            $replacement = '&nbsp;'; // HTML实体空格
            break;
    }
    
    // 直接替换所有空格，适用于标题等纯文本内容
    return str_replace(' ', $replacement, $content);
} 