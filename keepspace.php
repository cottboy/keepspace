<?php
/**
 * Plugin Name: KeepSpace
 * Description: 自动将空格转换为特殊字符空格，防止被自动省略。
 * Version: 1.0.5
 * Author: cottboy
 * Author URI: https://www.joyfamily.top
 * Text Domain: keepspace
 * Domain Path: /languages
 * License: GPLv3 or later
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
        __('KeepSpace', 'keepspace'),
        __('KeepSpace', 'keepspace'),
        'manage_options',
        'keepspace-settings',
        'keepspace_settings_page'
    );
}

// 在插件列表页面添加"设置"链接
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'keepspace_add_settings_link');
function keepspace_add_settings_link($links) {
    // 创建设置链接，指向插件设置页面
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=keepspace-settings')) . '">' . esc_html(__('设置', 'keepspace')) . '</a>';
    // 将设置链接添加到数组开头，这样它会显示在"禁用"按钮左边
    array_unshift($links, $settings_link);
    return $links;
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
        <h1><?php echo esc_html(__('KeepSpace', 'keepspace')); ?></h1>

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

// 处理文章保存（标题、摘要、正文）
add_filter('wp_insert_post_data', 'keepspace_process_post_data', 5, 2);
function keepspace_process_post_data($data, $postarr) {
    // 只处理文章(post)和页面(page)，不处理其他自定义文章类型
    // 这样可以避免误拦截其他插件的自定义文章类型
    $allowed_post_types = array('post', 'page');
    if (!isset($data['post_type']) || !in_array($data['post_type'], $allowed_post_types, true)) {
        return $data;
    }

    // 排除自动保存和修订版本，只处理真正的保存操作
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $data;
    }
    if (isset($data['post_status']) && $data['post_status'] === 'auto-draft') {
        return $data;
    }
    if (wp_is_post_revision($postarr['ID'] ?? 0)) {
        return $data;
    }

    // 处理标题
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

/**
 * 处理评论空格转换 - 在WordPress trim之前拦截
 *
 * WordPress在 wp_handle_comment_submission() 函数中会执行:
 * $comment_content = trim( $comment_data['comment'] );
 *
 * 所以我们必须在这之前就替换空格
 *
 * 方案:在 init 钩子中,只在确认是评论提交时才处理
 *
 * 关于安全性说明:
 * 1. 这个函数不需要nonce验证,因为它只是在WordPress处理评论之前预处理数据
 * 2. WordPress的wp_handle_comment_submission()函数会进行完整的安全验证
 * 3. 我们不进行sanitize是因为WordPress后续会统一处理
 * 4. 这个函数只是替换空格字符,不会引入安全风险
 */
add_action('init', 'keepspace_process_comment_before_trim', 1);
function keepspace_process_comment_before_trim() {
    // 检查设置是否启用
    if (get_option('keepspace_comment', '1') != '1') {
        return;
    }

    // 只在POST请求时处理 - 使用isset检查避免未定义索引警告
    if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    // 只处理评论提交
    // 通过检查是否存在 comment_post_ID 来判断是否是评论提交
    // phpcs:disable WordPress.Security.NonceVerification.Missing -- 这是预处理,WordPress后续会验证nonce
    if (!isset($_POST['comment_post_ID']) || !isset($_POST['comment'])) {
        return;
    }

    // 验证comment_post_ID是有效的数字
    if (!is_numeric($_POST['comment_post_ID'])) {
        return;
    }

    // 获取评论内容
    // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- WordPress的wp_handle_comment_submission()会进行完整的sanitize
    $comment_content = wp_unslash($_POST['comment']);
    // phpcs:enable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

    // 替换空格
    $comment_content = keepspace_replace_spaces_simple($comment_content);

    // 更新$_POST
    $_POST['comment'] = $comment_content;
    // phpcs:enable WordPress.Security.NonceVerification.Missing
}

// 处理古腾堡编辑器的REST API请求（标题、摘要、正文）
add_filter('rest_pre_insert_post', 'keepspace_process_rest_post', 10, 2);
function keepspace_process_rest_post($prepared_post, $request) {
    // 只处理文章(post)和页面(page)，不处理其他自定义文章类型
    $allowed_post_types = array('post', 'page');
    if (!isset($prepared_post->post_type) || !in_array($prepared_post->post_type, $allowed_post_types, true)) {
        return $prepared_post;
    }

    // 检查用户权限 - 必须能编辑文章
    if (!current_user_can('edit_post', $prepared_post->ID ?? 0) && !current_user_can('edit_posts')) {
        return $prepared_post;
    }

    // 处理标题
    if (get_option('keepspace_title', '1') == '1' && isset($prepared_post->post_title)) {
        $prepared_post->post_title = keepspace_replace_spaces_simple($prepared_post->post_title);
    }

    // 处理摘要
    if (get_option('keepspace_excerpt', '1') == '1' && isset($prepared_post->post_excerpt)) {
        $prepared_post->post_excerpt = keepspace_replace_spaces($prepared_post->post_excerpt);
    }

    // 处理正文
    if (get_option('keepspace_content', '1') == '1' && isset($prepared_post->post_content)) {
        $prepared_post->post_content = keepspace_replace_spaces($prepared_post->post_content);
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