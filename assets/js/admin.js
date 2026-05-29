/**
 * WP AIBot — Admin JavaScript
 * Handles tab switching, API URL auto-fill, JSON Schema builder,
 * notification rules builder, and schema preview for the chatbot meta box.
 */
(function($) {
    'use strict';

    var config = window.aiChatbotAdmin || {};
    var schemaIdx = $('#js-schema-fields .js-schema-row').length;
    var notifyGroupIdx = $('#js-notify-rules-fields .ai-chatbot-rule-group').length;
    var captureGroupIdx = $('#js-capture-rules-fields .ai-chatbot-rule-group').length;
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
            var lidx = $leadFieldsContainer.children().length;
            var html = $leadFieldsTpl.html().replace(/__LIDX__/g, lidx);
            $leadFieldsContainer.append(html);
        });

        $leadFieldsContainer.on('click', '.js-lead-field-remove', function() {
            $(this).closest('.js-lead-field-row').remove();
        });

        // ===== Ripple settings toggle =====
        var rippleToggle = document.querySelector('[name="chatbot_fab_ripple_enabled"]');
        var rippleSettings = document.getElementById('ai-chatbot-ripple-settings');
        function toggleRippleSettings() {
            rippleSettings.style.display = rippleToggle && rippleToggle.checked ? 'flex' : 'none';
        }
        if (rippleToggle && rippleSettings) {
            rippleToggle.addEventListener('change', toggleRippleSettings);
            toggleRippleSettings();
        }

        // ===== Range slider live values =====
        function bindRangeSlider(inputId, displayId, formatter) {
            var input = document.getElementById(inputId);
            var display = document.getElementById(displayId);
            if (input && display) {
                input.addEventListener('input', function() {
                    display.textContent = formatter ? formatter(this.value) : this.value;
                });
            }
        }
        bindRangeSlider('chatbot_fab_ripple_opacity', 'ai-chatbot-ripple-opacity-val');
        bindRangeSlider('chatbot_fab_ripple_speed', 'ai-chatbot-ripple-speed-val', function(v) { return v + 's'; });
        bindRangeSlider('chatbot_fab_ripple_radius', 'ai-chatbot-ripple-radius-val', function(v) { return v + 'x'; });
        bindRangeSlider('chatbot_temperature', 'ai-chatbot-temp-val');

        // ===== Cache TTL & Open Delay toggle =====
        var defaultOpenToggle = document.querySelector('[name="chatbot_fab_default_open"]');
        var cacheTtlField = document.getElementById('ai-chatbot-cache-ttl-field');
        var openDelayField = document.getElementById('ai-chatbot-open-delay-field');
        var transitionField = document.getElementById('ai-chatbot-transition-field');
        function toggleOpenFields() {
            var show = defaultOpenToggle && defaultOpenToggle.checked;
            if (cacheTtlField) cacheTtlField.style.display = show ? 'block' : 'none';
            if (openDelayField) openDelayField.style.display = show ? 'block' : 'none';
            if (transitionField) transitionField.style.display = show ? 'block' : 'none';
        }
        if (defaultOpenToggle) {
            defaultOpenToggle.addEventListener('change', toggleOpenFields);
            toggleOpenFields();
        }

        // ===== WeCom guide toggle =====
        var guideToggle = document.getElementById('js-wecom-guide-toggle');
        var guide = document.getElementById('js-wecom-guide');
        if (guideToggle && guide) {
            guideToggle.addEventListener('click', function(e) {
                e.preventDefault();
                var isHidden = guide.style.display === 'none';
                guide.style.display = isHidden ? '' : 'none';
                guideToggle.textContent = isHidden ? config.i18n.hideGuide : config.i18n.showGuide;
            });
        }

        // ===== Model Selects: Fetch, Populate, Custom Toggle =====
        var $modelSelect = $('#chatbot_model');
        var $modelHidden = $('#chatbot_model_hidden');
        var $modelCustom = $('#chatbot_model_custom');
        var $modelCustomWrap = $('#chatbot-model-custom-wrap');
        var $fallbackSelect = $('#chatbot_fallback_model');
        var $fallbackHidden = $('#chatbot_fallback_model_hidden');
        var $fallbackCustom = $('#chatbot_fallback_model_custom');
        var $fallbackCustomWrap = $('#chatbot-fallback-model-custom-wrap');
        var $platformSelect = $('#chatbot_platform');

        function populateModelSelects(models) {
            var currentModel = $modelHidden.val();
            var currentFallback = $fallbackHidden.val();

            [$modelSelect, $fallbackSelect].forEach(function($sel) {
                var val = $sel.is($modelSelect) ? currentModel : currentFallback;
                $sel.find('option:not([value=""]):not([value="__custom__"])').remove();
                $.each(models, function(i, m) {
                    if ($sel.find('option[value="' + m.replace(/"/g, '&quot;') + '"]').length === 0) {
                        $sel.append($('<option>').val(m).text(m));
                    }
                });
                if (val && models.indexOf(val) !== -1) {
                    $sel.val(val);
                } else if (val && models.indexOf(val) === -1) {
                    $sel.val('__custom__');
                    showCustomInput($sel);
                } else {
                    $sel.val('');
                }
            });
        }

        function showCustomInput($sel) {
            var isModel = $sel.is($modelSelect);
            var $wrap = isModel ? $modelCustomWrap : $fallbackCustomWrap;
            var $input = isModel ? $modelCustom : $fallbackCustom;
            var $hidden = isModel ? $modelHidden : $fallbackHidden;
            var val = $hidden.val();
            if ($sel.val() === '__custom__') {
                $wrap.show();
                $input.val(val);
            } else {
                $wrap.hide();
            }
        }

        function syncHiddenFromSelect($sel) {
            var isModel = $sel.is($modelSelect);
            var $hidden = isModel ? $modelHidden : $fallbackHidden;
            var $input = isModel ? $modelCustom : $fallbackCustom;
            var $wrap = isModel ? $modelCustomWrap : $fallbackCustomWrap;

            if ($sel.val() === '__custom__') {
                $wrap.show();
                $input.val('').focus();
                $hidden.val('');
            } else {
                $wrap.hide();
                $hidden.val($sel.val());
            }
        }

        function syncHiddenFromCustom($input) {
            var isModel = $input.is($modelCustom);
            var $hidden = isModel ? $modelHidden : $fallbackHidden;
            $hidden.val($input.val());
        }

        // Restore saved custom values on page load
        if ($modelHidden.val() && !$modelSelect.find('option[value="' + $modelHidden.val().replace(/"/g, '&quot;') + '"]').length) {
            $modelSelect.val('__custom__');
            showCustomInput($modelSelect);
        }
        if ($fallbackHidden.val() && !$fallbackSelect.find('option[value="' + $fallbackHidden.val().replace(/"/g, '&quot;') + '"]').length) {
            $fallbackSelect.val('__custom__');
            showCustomInput($fallbackSelect);
        }

        // Select change → sync hidden, show/hide custom
        $modelSelect.on('change', function() { syncHiddenFromSelect($(this)); });
        $fallbackSelect.on('change', function() { syncHiddenFromSelect($(this)); });

        // Custom input change → sync to hidden
        $modelCustom.on('input', function() { syncHiddenFromCustom($(this)); });
        $fallbackCustom.on('input', function() { syncHiddenFromCustom($(this)); });

        // Auto-fetch models on page load for saved chatbots
        function fetchModels() {
            var chatbotId = $('#post_ID').val();
            if (!chatbotId || chatbotId <= 0) return;
            if ($platformSelect.val() === 'anthropic') return;

            $.post(ajaxurl, {
                action: 'ai_chatbot_fetch_models',
                chatbot_id: chatbotId,
                platform: $platformSelect.val(),
                api_base_url: $('#chatbot_api_base_url').val(),
                api_key: $('#chatbot_api_key').val(),
                _ajax_nonce: config.fetchModelsNonce
            }, function(response) {
                if (response.success && response.data.models) {
                    populateModelSelects(response.data.models);
                }
            });
        }
        fetchModels();

        // Re-fetch when platform changes (for OpenAI)
        $platformSelect.on('change', function() {
            if ($(this).val() !== 'anthropic') {
                fetchModels();
            }
        });
    });
})(jQuery);
