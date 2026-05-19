<?php
defined('ABSPATH') || exit;
/**
 * Admin meta box for conversation details.
 * Variables set by AI_Chatbot_CPT_Conversation::render_meta_box().
 *
 * @var WP_Post $post
 * @var string  $session_id
 * @var int     $chatbot_id
 * @var string  $history
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
    <!-- Summary -->
    <div class="ai-conv-section">
        <h3><?php esc_html_e('Overview', 'wp-aibot'); ?></h3>
        <table class="widefat striped">
            <tr><th><?php esc_html_e('Chatbot', 'wp-aibot'); ?></th><td><?php echo esc_html($bot_name); ?> (#<?php echo $chatbot_id; ?>)</td></tr>
            <tr><th><?php esc_html_e('Session ID', 'wp-aibot'); ?></th><td><code><?php echo esc_html($session_id); ?></code></td></tr>
            <tr><th><?php esc_html_e('Messages', 'wp-aibot'); ?></th><td><?php echo $msg_count; ?></td></tr>
            <tr><th><?php esc_html_e('Started', 'wp-aibot'); ?></th><td><?php echo esc_html($started_at); ?></td></tr>
        </table>
    </div>

    <!-- Visitor Info -->
    <?php if ($ip || $ua || $page_url): ?>
    <div class="ai-conv-section">
        <h3><?php esc_html_e('Visitor', 'wp-aibot'); ?></h3>
        <table class="widefat striped">
            <?php if ($ip): ?><tr><th><?php esc_html_e('IP', 'wp-aibot'); ?></th><td><?php echo esc_html($ip); ?></td></tr><?php endif; ?>
            <?php if ($ua): ?><tr><th><?php esc_html_e('User Agent', 'wp-aibot'); ?></th><td style="font-size:12px;word-break:break-all;"><?php echo esc_html($ua); ?></td></tr><?php endif; ?>
            <?php if ($page_url): ?><tr><th><?php esc_html_e('Page URL', 'wp-aibot'); ?></th><td><a href="<?php echo esc_url($page_url); ?>" target="_blank"><?php echo esc_html($page_url); ?></a></td></tr><?php endif; ?>
        </table>
    </div>
    <?php endif; ?>

    <!-- Messages -->
    <div class="ai-conv-section">
        <h3><?php esc_html_e('Messages', 'wp-aibot'); ?></h3>
        <?php
        if (empty($history)) {
            echo '<p style="color:#999;">' . esc_html__('No messages recorded.', 'wp-aibot') . '</p>';
        } else {
            // Parse multi-line messages: accumulate content until next marker
            $lines = explode("\n", $history);
            $messages = [];
            $current_role = null;
            $current_content = [];

            foreach ($lines as $line) {
                $line = rtrim($line);
                if (preg_match('/^\*\*(User|Assistant):\*\*\s*(.*)$/', $line, $m)) {
                    // Save previous message
                    if ($current_role !== null) {
                        $messages[] = [
                            'role'    => $current_role,
                            'content' => implode("\n", $current_content),
                        ];
                    }
                    $current_role = strtolower($m[1]);
                    $current_content = [trim($m[2])];
                } elseif ($current_role !== null) {
                    $current_content[] = $line;
                }
            }
            // Save last message
            if ($current_role !== null) {
                $messages[] = [
                    'role'    => $current_role,
                    'content' => implode("\n", $current_content),
                ];
            }

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

        <details style="margin-top:12px;">
            <summary style="cursor:pointer;font-size:12px;color:#666;"><?php esc_html_e('View raw JSON', 'wp-aibot'); ?></summary>
            <div class="ai-conv-json"><?php echo esc_html(wp_json_encode($lead_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></div>
        </details>
    </div>
    <?php endif; ?>

    <!-- Raw History JSON -->
    <div class="ai-conv-section">
        <h3><?php esc_html_e('Raw Conversation Data', 'wp-aibot'); ?></h3>
        <details>
            <summary style="cursor:pointer;font-size:12px;color:#666;margin-bottom:8px;"><?php esc_html_e('View raw conversation JSON', 'wp-aibot'); ?></summary>
            <?php
            $all_meta = get_post_meta($post->ID);
            $clean = [];
            foreach ($all_meta as $k => $v) {
                $clean[$k] = maybe_unserialize($v[0]);
            }
            ?>
            <div class="ai-conv-json"><?php echo esc_html(wp_json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></div>
        </details>
    </div>
</div>
<script>
var el = document.querySelector('.ai-conv-messages-scroll');
if (el) el.scrollTop = el.scrollHeight;
</script>
