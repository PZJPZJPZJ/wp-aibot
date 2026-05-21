/**
 * WP AIBot — Admin JavaScript
 * Handles tab switching, API URL auto-fill, JSON Schema builder,
 * notification rules builder, and schema preview for the chatbot meta box.
 */
(function($) {
    'use strict';

    var config = window.aiChatbotAdmin || {};
    var schemaIdx = config.schemaIdx || 0;
    var notifyGroupIdx = config.notifyGroupIdx || 0;
    var captureGroupIdx = config.captureGroupIdx || 0;
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

        // ===== Notification Rules Builder (grouped: OR between groups, AND within) =====
        var $notifyContainer = $('#js-notify-rules-fields');
        var $notifyGroupTpl = $('#js-notify-group-tpl');
        var $notifyCondTpl = $('#js-notify-condition-tpl');

        // Add a new rule group
        $('.js-notify-add-group').on('click', function() {
            var html = $notifyGroupTpl.html().replace(/__NGIDX__/g, notifyGroupIdx);
            var $group = $(html);
            $notifyContainer.append($group);
            notifyGroupIdx++;
        });

        // Remove a rule group
        $notifyContainer.on('click', '.js-notify-remove-group', function() {
            $(this).closest('.ai-chatbot-rule-group').remove();
        });

        // Add a condition to a specific group
        $notifyContainer.on('click', '.js-notify-add-condition', function() {
            var $group = $(this).closest('.ai-chatbot-rule-group');
            var gidx = $group.data('group-index');
            var $conditions = $group.find('.ai-chatbot-rule-group-conditions');
            var cidx = $conditions.children().length;
            var html = $notifyCondTpl.html()
                .replace(/__NGIDX__/g, gidx)
                .replace(/__NCIDX__/g, cidx);
            $conditions.append(html);
        });

        // Remove a condition from a group
        $notifyContainer.on('click', '.js-notify-remove-condition', function() {
            $(this).closest('.ai-chatbot-condition-row').remove();
        });

        // ===== Lead Capture Rules Builder (grouped: OR between groups, AND within) =====
        var $captureContainer = $('#js-capture-rules-fields');
        var $captureGroupTpl = $('#js-capture-group-tpl');
        var $captureCondTpl = $('#js-capture-condition-tpl');

        // Add a new rule group
        $(document).on('click', '.js-capture-add-group', function() {
            var html = $captureGroupTpl.html().replace(/__GIDX__/g, captureGroupIdx);
            var $group = $(html);
            $captureContainer.append($group);
            captureGroupIdx++;
        });

        // Remove a rule group
        $captureContainer.on('click', '.js-capture-remove-group', function() {
            $(this).closest('.ai-chatbot-rule-group').remove();
        });

        // Add a condition to a specific group
        $captureContainer.on('click', '.js-capture-add-condition', function() {
            var $group = $(this).closest('.ai-chatbot-rule-group');
            var gidx = $group.data('group-index');
            var $conditions = $group.find('.ai-chatbot-rule-group-conditions');
            var cidx = $conditions.children().length;
            var html = $captureCondTpl.html()
                .replace(/__GIDX__/g, gidx)
                .replace(/__CIDX__/g, cidx);
            $conditions.append(html);
        });

        // Remove a condition from a group
        $captureContainer.on('click', '.js-capture-remove-condition', function() {
            $(this).closest('.ai-chatbot-condition-row').remove();
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
            } else if (icon && icon.indexOf('dashicons-') === 0) {
                $faPreview.html('<span class="dashicons ' + icon + '" style="font-size:24px;width:auto;height:auto;"></span>');
            } else if (icon) {
                $faPreview.html('<span style="font-size:20px;">' + icon + '</span>');
            } else {
                $faPreview.html('<span style="font-size:20px;">💬</span>');
            }
        }

        // ===== Lead Capture Form Fields Builder =====
        var $leadFieldsContainer = $('#js-lead-fields');
        var $leadFieldsTpl = $('#js-lead-field-tpl');

        $(document).on('click', '.js-lead-field-add', function() {
            var html = $leadFieldsTpl.html().replace(/__LIDX__/g, config.leadFieldsIdx);
            $leadFieldsContainer.append(html);
            config.leadFieldsIdx++;
        });

        $leadFieldsContainer.on('click', '.js-lead-field-remove', function() {
            $(this).closest('.js-lead-field-row').remove();
        });
    });
})(jQuery);
