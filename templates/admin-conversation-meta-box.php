<?php
defined('ABSPATH') || exit;
/**
 * Admin meta box for conversation details.
 * Variables set by AI_Chatbot_CPT_Conversation::render_meta_box().
 *
 * @var WP_Post $post
 * @var string  $session_id
 * @var int     $chatbot_id
 * @var array   $messages
 * @var int     $msg_count
 * @var string  $started_at
 * @var mixed   $lead_data
 * @var string  $ip
 * @var string  $ua
 * @var string  $page_url
 * @var string  $bot_name
 */
?>
<div class="ai-conv-wrap">
    <!-- Overview (includes visitor info and summary) -->
    <div class="ai-conv-section">
        <h3><?php esc_html_e('Overview', 'wp-aibot'); ?></h3>
        <table class="widefat striped">
            <tr><th><?php esc_html_e('Chatbot', 'wp-aibot'); ?></th><td><?php echo esc_html($bot_name); ?> (#<?php echo $chatbot_id; ?>)</td></tr>
            <tr><th><?php esc_html_e('Session ID', 'wp-aibot'); ?></th><td><code><?php echo esc_html($session_id); ?></code></td></tr>
            <tr><th><?php esc_html_e('Messages', 'wp-aibot'); ?></th><td><?php echo $msg_count; ?></td></tr>
            <tr><th><?php esc_html_e('Started', 'wp-aibot'); ?></th><td><?php echo esc_html($started_at); ?></td></tr>
            <?php
            $notify_count = (int) get_post_meta($post->ID, 'conversation_notification_count', true);
            if ($notify_count > 0):
                echo '<tr><th>' . __('Notification Sent', 'wp-aibot') . '</th><td><span style="color:#46b450;">✓ ' . sprintf(__('%d time(s)', 'wp-aibot'), $notify_count) . '</span></td></tr>';
            endif;
            $conv_summary = get_post_meta($post->ID, 'conversation_summary', true);
            if (!empty($conv_summary)):
            ?><tr><th><?php esc_html_e('Summary', 'wp-aibot'); ?></th><td><?php echo esc_html($conv_summary); ?></td></tr><?php
            endif;
            ?>
            <?php if ($ip): ?><tr><th><?php esc_html_e('IP', 'wp-aibot'); ?></th><td><?php echo esc_html($ip); ?></td></tr><?php endif; ?>
            <?php if ($ua): ?><tr><th><?php esc_html_e('User Agent', 'wp-aibot'); ?></th><td style="font-size:12px;word-break:break-all;"><?php echo esc_html($ua); ?></td></tr><?php endif; ?>
            <?php if ($page_url): ?><tr><th><?php esc_html_e('Page URL', 'wp-aibot'); ?></th><td><a href="<?php echo esc_url($page_url); ?>" target="_blank"><?php echo esc_html($page_url); ?></a></td></tr><?php endif; ?>
        </table>
    </div>

    <!-- Messages -->
    <div class="ai-conv-section">
        <h3><?php esc_html_e('Messages', 'wp-aibot'); ?></h3>
        <?php
        if (empty($messages)) {
            echo '<p style="color:#999;">' . esc_html__('No messages recorded.', 'wp-aibot') . '</p>';
        } else {
            echo '<div class="ai-conv-messages-scroll" style="max-height:420px;overflow-y:auto;">';
            echo '<table class="ai-conv-msg-table widefat">';
            foreach ($messages as $msg) {
                $role = $msg['role'];
                $content = $msg['content'];
                $css_class = 'ai-conv-msg-' . $role;
                $label = ($role === 'user') ? __('User', 'wp-aibot') : __('Assistant', 'wp-aibot');
                echo '<tr class="' . $css_class . '">';
                echo '<td class="ai-conv-msg-label">' . esc_html($label) . '</td>';
                echo '<td class="ai-conv-msg-content">' . esc_html($content) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }
        ?>
    </div>

    <!-- Lead Data -->
    <?php if (!empty($lead_data)): ?>
    <div class="ai-conv-section">
        <h3><?php esc_html_e('Lead Data', 'wp-aibot'); ?></h3>
        <table class="widefat ai-conv-lead-table">
            <?php foreach ($lead_data as $key => $value): ?>
            <tr>
                <td><?php echo esc_html(ucwords(str_replace('_', ' ', $key))); ?></td>
                <td><?php echo is_array($value) ? esc_html(wp_json_encode($value)) : esc_html($value); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

</div>
<script>
var el = document.querySelector('.ai-conv-messages-scroll');
if (el) el.scrollTop = el.scrollHeight;
</script>
