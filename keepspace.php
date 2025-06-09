<?php
/**
 * Plugin Name: KeepSpace
 * Description: 自动将空格转换为特殊字符空格，防止HTML省略空格
 * Version: 1.0.4
 * Author: cottboy
 * Author URI: https://github.com/cottboy
 * Plugin URI: https://github.com/cottboy/keepspace
 * Text Domain: keepspace
 * Domain Path: /languages
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// 防止直接访问
if (!defined('ABSPATH')) {
    exit;
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
        // 验证nonce
        if (!isset($_POST['keepspace_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['keepspace_nonce'])), 'keepspace_settings_action')) {
            wp_die(esc_html(__('安全验证失败，请重试。', 'keepspace')));
        }
        
        // 检查用户权限
        if (!current_user_can('manage_options')) {
            wp_die(esc_html(__('您没有权限执行此操作。', 'keepspace')));
        }
        
        update_option('keepspace_title', isset($_POST['keepspace_title']) ? '1' : '0');
        update_option('keepspace_excerpt', isset($_POST['keepspace_excerpt']) ? '1' : '0');
        update_option('keepspace_content', isset($_POST['keepspace_content']) ? '1' : '0');
        update_option('keepspace_comment', isset($_POST['keepspace_comment']) ? '1' : '0');
        
        if (isset($_POST['keepspace_space_type'])) {
            $space_type = sanitize_text_field(wp_unslash($_POST['keepspace_space_type']));
            // 白名单验证：只允许预定义的空格类型
            $allowed_space_types = array('unicode_nbsp', 'fullwidth_space', 'html_nbsp');
            if (in_array($space_type, $allowed_space_types, true)) {
                update_option('keepspace_space_type', $space_type);
            }
        }
        
        echo '<div class="notice notice-success"><p>' . esc_html(__('设置已保存！', 'keepspace')) . '</p></div>';
    }
    
    $title_enabled = get_option('keepspace_title', '1');
    $excerpt_enabled = get_option('keepspace_excerpt', '1');
    $content_enabled = get_option('keepspace_content', '1');
    $comment_enabled = get_option('keepspace_comment', '1');
    $space_type = get_option('keepspace_space_type', 'unicode_nbsp');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(__('KeepSpace 设置', 'keepspace')); ?></h1>
        
        <p style="color: #111; font-weight: bold; margin-bottom: 5px;">
            <?php echo esc_html(__('重要提醒：更改特殊空格类型只对新保存的内容生效，现有内容中的特殊空格不会自动更改。', 'keepspace')); ?>
        </p>
        
        <form method="post" action="">
            <?php wp_nonce_field('keepspace_settings_action', 'keepspace_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo esc_html(__('特殊空格类型', 'keepspace')); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="radio" name="keepspace_space_type" value="unicode_nbsp" <?php checked($space_type, 'unicode_nbsp'); ?> />
                                <strong><?php echo esc_html(__('不断行空格 (\u00A0)', 'keepspace')); ?></strong> - <?php echo esc_html(__('推荐', 'keepspace')); ?>
                            </label>
                            <p class="description">
                                <strong><?php echo esc_html(__('优点：', 'keepspace')); ?></strong><?php echo esc_html(__('与普通空格显示效果相同；只算1个字符，不影响摘要截取和字符统计', 'keepspace')); ?><br>
                                <strong><?php echo esc_html(__('缺点：', 'keepspace')); ?></strong><?php echo esc_html(__('在极少数老旧系统中可能显示异常', 'keepspace')); ?><br>
                                <strong><?php echo esc_html(__('适用：', 'keepspace')); ?></strong><?php echo esc_html(__('大部分情况的最佳选择', 'keepspace')); ?>
                            </p>
                            <br>
                            
                            <label>
                                <input type="radio" name="keepspace_space_type" value="fullwidth_space" <?php checked($space_type, 'fullwidth_space'); ?> />
                                <strong><?php echo esc_html(__('中文全角空格 (\u3000)', 'keepspace')); ?></strong>
                            </label>
                            <p class="description">
                                <strong><?php echo esc_html(__('优点：', 'keepspace')); ?></strong><?php echo esc_html(__('只算1个字符；在中文环境下非常自然；宽度正好是一个中文字符', 'keepspace')); ?><br>
                                <strong><?php echo esc_html(__('缺点：', 'keepspace')); ?></strong><?php echo esc_html(__('比普通空格宽，在英文中会显得突兀', 'keepspace')); ?><br>
                                <strong><?php echo esc_html(__('适用：', 'keepspace')); ?></strong><?php echo esc_html(__('纯中文内容，特别是段落缩进', 'keepspace')); ?>
                            </p>
                            <br>
                            
                            <label>
                                <input type="radio" name="keepspace_space_type" value="html_nbsp" <?php checked($space_type, 'html_nbsp'); ?> />
                                <strong><?php echo esc_html(__('&amp;nbsp; - HTML实体空格', 'keepspace')); ?></strong>
                            </label>
                            <p class="description">
                                <strong><?php echo esc_html(__('优点：', 'keepspace')); ?></strong><?php echo esc_html(__('兼容性最好；所有浏览器都支持', 'keepspace')); ?><br>
                                <strong><?php echo esc_html(__('缺点：', 'keepspace')); ?></strong><?php echo esc_html(__('算5个字符，会影响摘要截取和字符统计', 'keepspace')); ?><br>
                                <strong><?php echo esc_html(__('适用：', 'keepspace')); ?></strong><?php echo esc_html(__('对兼容性要求极高，不在意字符计数的情况', 'keepspace')); ?>
                            </p>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html(__('功能开关', 'keepspace')); ?></th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="keepspace_title" value="1" <?php checked($title_enabled, '1'); ?> />
                                <?php echo esc_html(__('启用标题空格保护', 'keepspace')); ?>
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="keepspace_excerpt" value="1" <?php checked($excerpt_enabled, '1'); ?> />
                                <?php echo esc_html(__('启用摘要空格保护', 'keepspace')); ?>
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="keepspace_content" value="1" <?php checked($content_enabled, '1'); ?> />
                                <?php echo esc_html(__('启用正文空格保护', 'keepspace')); ?>
                            </label><br><br>
                            <label>
                                <input type="checkbox" name="keepspace_comment" value="1" <?php checked($comment_enabled, '1'); ?> />
                                <?php echo esc_html(__('启用评论空格保护', 'keepspace')); ?>
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
        // 验证文章编辑的nonce - 加强验证逻辑
        $post_id = isset($_POST['post_ID']) ? intval($_POST['post_ID']) : 0;
        if ($post_id > 0 && isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'update-post_' . $post_id)) {
            // 额外检查用户权限
            if (current_user_can('edit_post', $post_id)) {
                if (get_option('keepspace_title', '1') == '1' && isset($_POST['post_title'])) {
                    $_POST['post_title'] = keepspace_replace_spaces_simple(sanitize_text_field(wp_unslash($_POST['post_title'])));
                }
            }
        }
    }
    
    // 处理评论提交 - 添加安全验证
    if (isset($_POST['comment']) && !empty($_POST['comment']) && get_option('keepspace_comment', '1') == '1') {
        // 校验自定义nonce
        if (!isset($_POST['keepspace_comment_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['keepspace_comment_nonce'])), 'keepspace_comment_action')) {
            wp_die(esc_html(__('安全验证失败，请刷新页面重试。', 'keepspace')));
        }
        // 安全获取评论内容（仅过滤危险标签，不去除首尾空格）
        $raw_comment = wp_unslash($_POST['comment']); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- 已用keepspace_safe_comment过滤
        // 检查是否是评论提交 - 修复REQUEST_URI消毒
        $request_uri = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $is_comment_submission = (
            isset($_POST['comment_post_ID']) || 
            isset($_POST['submit']) || 
            (strpos($request_uri, 'wp-comments-post.php') !== false)
        );
        if ($is_comment_submission) {
            // 基本的安全检查：确保comment_post_ID是有效的数字
            if (isset($_POST['comment_post_ID']) && is_numeric($_POST['comment_post_ID'])) {
                $post_id = intval($_POST['comment_post_ID']);
                // 验证文章是否存在且允许评论
                $post = get_post($post_id);
                if ($post && comments_open($post_id)) {
                    // 先替换空格，再进行自定义安全处理（不使用trim的函数）
                    $comment_content = keepspace_replace_spaces_simple($raw_comment);
                    $comment_content = keepspace_safe_comment($comment_content);
                    $_POST['comment'] = $comment_content;
                }
            }
        }
    }
}

// 处理评论保存 - 使用WordPress标准钩子作为备用
add_filter('preprocess_comment', 'keepspace_process_comment_with_verification', 10);
function keepspace_process_comment_with_verification($commentdata) {
    // 检查设置是否启用
    if (get_option('keepspace_comment', '1') == '1' && !empty($commentdata['comment_content'])) {
        // WordPress的preprocess_comment钩子已经有内置的验证机制
        // 包括spam检查、权限验证等，所以这里是安全的
        $commentdata['comment_content'] = keepspace_replace_spaces_simple($commentdata['comment_content']);
    }
    return $commentdata;
}

// 处理古腾堡编辑器的REST API请求
add_filter('rest_pre_insert_post', 'keepspace_process_rest_title', 10, 2);
function keepspace_process_rest_title($prepared_post, $request) {
    // 检查用户权限 - 必须能编辑文章
    if (!current_user_can('edit_post', $prepared_post->ID ?? 0) && !current_user_can('edit_posts')) {
        return $prepared_post;
    }
    
    if (get_option('keepspace_title', '1') == '1' && isset($prepared_post->post_title)) {
        $prepared_post->post_title = keepspace_replace_spaces_simple($prepared_post->post_title);
    }
    return $prepared_post;
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

// 在评论表单插入自定义nonce字段
add_action('comment_form', 'keepspace_comment_nonce_field');
function keepspace_comment_nonce_field() {
    wp_nonce_field('keepspace_comment_action', 'keepspace_comment_nonce');
}

/**
 * 只过滤危险字符，不去除首尾空格，确保安全同时保留开头结尾空格
 */
function keepspace_safe_comment($comment) {
    $allowed_tags = array(
        'a' => array('href' => array(), 'title' => array()),
        'em' => array(),
        'strong' => array(),
        'code' => array(),
        'blockquote' => array('cite' => array()),
        'br' => array(),
        'p' => array(),
    );
    return wp_kses($comment, $allowed_tags);
} 