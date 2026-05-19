/**
 * WP AIBot — Admin JavaScript
 * Handles tab switching, API URL auto-fill, JSON Schema builder,
 * notification rules builder, and schema preview for the chatbot meta box.
 */
(function($) {
    'use strict';

    var config = window.aiChatbotAdmin || {};
    var schemaIdx = config.schemaIdx || 0;
    var notifyIdx = config.notifyIdx || 0;
    $(document).ready(function() {
        // ===== Tab switching =====
        $('.ai-chatbot-tab-btn').on('click', function() {
            var tab = $(this).data('tab');
            $('.ai-chatbot-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.ai-chatbot-tab-panel').removeClass('active');
            $('.ai-chatbot-tab-panel[data-tab="' + tab + '"]').addClass('active');
        });

        // ===== Auto-fill API base URL on platform change =====
        var $platform = $('#chatbot_platform');
        var $apiUrl = $('#chatbot_api_base_url');
        var platformUrls = {
            'openai':    'https://api.openai.com/v1',
            'anthropic': 'https://api.anthropic.com/v1',
        };
        $platform.on('change', function() {
            var url = platformUrls[$(this).val()];
            if (url && $apiUrl.val() !== '') {
                var current = $apiUrl.val().replace(/\/+$/, '');
                var isDefault = Object.values(platformUrls).some(function(v) {
                    return v && current === v.replace(/\/+$/, '');
                });
                if (isDefault || current === '') {
                    $apiUrl.val(url);
                }
            } else if (url && $apiUrl.val() === '') {
                $apiUrl.val(url);
            }
        });

        // ===== JSON Schema Builder =====
        var $schemaContainer = $('#js-schema-fields');
        var $template = $('#js-schema-row-tpl');

        // Add new field row
        $('.js-schema-add-row').on('click', function() {
            var html = $template.html().replace(/__IDX__/g, schemaIdx);
            var $row = $(html);
            $schemaContainer.append($row);
            schemaIdx++;
        });

        // Remove field row (event delegation)
        $schemaContainer.on('click', '.js-schema-remove-row', function() {
            $(this).closest('.js-schema-row').remove();
        });

        // Toggle enum values field when type changes (event delegation)
        $schemaContainer.on('change', '.js-schema-field-type select', function() {
            var $row = $(this).closest('.js-schema-row');
            var type = $(this).val();
            $row.find('.js-schema-dependent').each(function() {
                if ($(this).data('dep-type') === type) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // ===== Notification Rules Builder =====
        var $notifyContainer = $('#js-notify-rules-fields');
        var $notifyTpl = $('#js-notify-rule-tpl');

        $('.js-notify-add-rule').on('click', function() {
            var html = $notifyTpl.html().replace(/__RIDX__/g, notifyIdx);
            $notifyContainer.append(html);
            notifyIdx++;
        });

        $notifyContainer.on('click', '.js-notify-remove-rule', function() {
            $(this).closest('.js-notify-rule-row').remove();
        });

        // ===== Font Awesome Icon Selector =====
        var $faInput = $('#chatbot_fab_icon');
        var $faPreview = $('#ai-chatbot-fa-preview');

        // Update preview when user types
        $faInput.on('input', function() {
            updateFaPreview($(this).val());
        });

        // Click an icon in the grid
        $(document).on('click', '.ai-chatbot-fa-option', function() {
            var icon = $(this).data('icon');
            $faInput.val(icon);
            $('.ai-chatbot-fa-option').css({'border-color':'#ccc','background':'#fff'});
            $(this).css({'border-color':'#2271b1','background':'#f0f6fc'});
            updateFaPreview(icon);
        });

        function updateFaPreview(icon) {
            if (icon && icon.indexOf('fa-') === 0) {
                $faPreview.html('<i class="fa ' + icon + '"></i>');
            } else if (icon) {
                $faPreview.html('<span style="font-size:20px;">' + icon + '</span>');
            } else {
                $faPreview.html('<span style="font-size:20px;">💬</span>');
            }
        }
    });
})(jQuery);
