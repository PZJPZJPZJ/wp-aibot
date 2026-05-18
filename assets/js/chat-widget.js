(function() {
    'use strict';

    class AIChatWidget {
        constructor(containerId, config) {
            this.container = document.getElementById(containerId);
            if (!this.container) return;
            this.config = config;
            this.widgetId = config.widget_id;
            this.sessionId = config.session_id;
            this.sessionToken = config.session_token;
            this.apiUrl = AIChatBotGlobals.rest_url;
            this.isOpen = config.layout_mode === 'inline';

            // localStorage UUID-based visitor session (persists across page loads)
            this.visitorId = localStorage.getItem('ai_chat_visitor');
            if (!this.visitorId) {
                this.visitorId = this.generateUUID();
                localStorage.setItem('ai_chat_visitor', this.visitorId);
            }
            // Load persisted session token for this visitor
            var storedToken = localStorage.getItem('ai_chat_token_' + this.visitorId);
            if (storedToken) {
                this.sessionToken = storedToken;
            } else {
                // Fresh visitor — clear IP-based token so backend generates one from visitor_id
                this.sessionToken = '';
            }

            // Also restore session_id from storage (matches backend's visitor-based derivation)
            var storedSessionId = localStorage.getItem('ai_chat_sid_' + this.visitorId + '_' + this.config.chatbot_id);
            if (storedSessionId) {
                this.sessionId = storedSessionId;
            }

            this.hasHistory = false;

            this.init();
        }

        generateUUID() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                var r = Math.random() * 16 | 0;
                return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
            });
        }

        async loadHistory() {
            try {
                var url = (AIChatBotGlobals.history_url || this.apiUrl.replace('/chat', '/history')) + '?' + new URLSearchParams({
                    chatbot_id: this.config.chatbot_id,
                    visitor_id: this.visitorId,
                    session_id: this.sessionId || '',
                    session_token: this.sessionToken || '',
                });

                var res = await fetch(url, {
                    method: 'GET',
                    headers: { 'X-WP-Nonce': AIChatBotGlobals.nonce },
                });

                var data = await res.json();

                if (data.ok && data.data) {
                    // Persist session token (same pattern as sendMessage)
                    if (data.data.session_token) {
                        this.sessionToken = data.data.session_token;
                        try {
                            localStorage.setItem('ai_chat_token_' + this.visitorId, data.data.session_token);
                            if (data.data.session_id) {
                                localStorage.setItem('ai_chat_sid_' + this.visitorId + '_' + this.config.chatbot_id, data.data.session_id);
                            }
                        } catch (e) {}
                    }

                    // Render past messages
                    if (Array.isArray(data.data.messages) && data.data.messages.length > 0) {
                        this.hasHistory = true;
                        data.data.messages.forEach(function(msg) {
                            this.addMessage(msg.role, msg.content);
                        }.bind(this));
                    }
                }
            } catch (err) {
                console.error('History fetch error:', err);
            }
        }

        async init() {
            this.render();
            this.bindEvents();

            if (this.config.layout_mode !== 'inline') {
                this.toggleChat(false);
            }

            // Load past conversation history first, then show greeting if no history
            await this.loadHistory();
            if (!this.hasHistory && this.config.greeting) {
                this.addMessage('bot', this.config.greeting);
            }
        }

        render() {
            const i18n = this.config.i18n || {};
            const isFloating = this.config.layout_mode !== 'inline';

            let html = '';
            if (isFloating) {
                html = `
                    <div class="ai-chatbot-fab" data-widget="${this.widgetId}">
                        <span class="ai-chatbot-fab-icon">${this.config.fab_icon || '💬'}</span>
                    </div>
                    <div class="ai-chatbot-popup" style="display:none;">
                        <div class="ai-chatbot-header">
                            <span class="ai-chatbot-title">${i18n.title || 'AI Assistant'}</span>
                            <span class="ai-chatbot-subtitle">${i18n.subtitle || 'Ask me anything'}</span>
                            <button class="ai-chatbot-close">&times;</button>
                        </div>
                        <div class="ai-chatbot-messages"></div>
                        <div class="ai-chatbot-input-area">
                            <textarea class="ai-chatbot-input" placeholder="${i18n.input_placeholder || 'Type your message...'}" rows="1"></textarea>
                            <button class="ai-chatbot-send">➤</button>
                        </div>
                    </div>
                `;
            } else {
                html = `
                    <div class="ai-chatbot-inline">
                        <div class="ai-chatbot-header">
                            <span class="ai-chatbot-title">${i18n.title || 'AI Assistant'}</span>
                            <span class="ai-chatbot-subtitle">${i18n.subtitle || 'Ask me anything'}</span>
                        </div>
                        <div class="ai-chatbot-messages"></div>
                        <div class="ai-chatbot-input-area">
                            <textarea class="ai-chatbot-input" placeholder="${i18n.input_placeholder || 'Type your message...'}" rows="1"></textarea>
                            <button class="ai-chatbot-send">➤</button>
                        </div>
                    </div>
                `;
            }

            this.container.innerHTML = html;

            this.messagesEl = this.container.querySelector('.ai-chatbot-messages');
            this.inputEl = this.container.querySelector('.ai-chatbot-input');
            this.sendBtn = this.container.querySelector('.ai-chatbot-send');

            if (isFloating) {
                this.fabEl = this.container.querySelector('.ai-chatbot-fab');
                this.popupEl = this.container.querySelector('.ai-chatbot-popup');
                this.closeBtn = this.container.querySelector('.ai-chatbot-close');
            }
        }

        bindEvents() {
            if (this.sendBtn) {
                this.sendBtn.addEventListener('click', () => this.handleSend());
            }
            if (this.inputEl) {
                this.inputEl.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.handleSend();
                    }
                });
                this.inputEl.addEventListener('input', () => this.autoResize());
            }
            if (this.fabEl) {
                this.fabEl.addEventListener('click', () => this.toggleChat());
            }
            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    this.toggleChat(false);
                }.bind(this));
            }
        }

        toggleChat(forceState) {
            if (!this.popupEl) return;
            this.isOpen = forceState !== undefined ? forceState : !this.isOpen;
            this.popupEl.style.display = this.isOpen ? 'flex' : 'none';
            if (this.isOpen && this.inputEl) {
                this.inputEl.focus();
            }
        }

        handleSend() {
            const text = this.inputEl.value.trim();
            if (!text) return;
            this.inputEl.value = '';
            this.autoResize();
            this.sendMessage(text);
        }

        async sendMessage(text) {
            this.addMessage('user', text);
            this.showTyping();

            try {
                const res = await fetch(this.apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': AIChatBotGlobals.nonce,
                    },
                    body: JSON.stringify({
                        chatbot_id: this.config.chatbot_id,
                        message: text,
                        session_id: this.sessionId,
                        session_token: this.sessionToken,
                        visitor_id: this.visitorId,
                        metadata: {
                            page: location.href,
                            referrer: document.referrer,
                            language: navigator.language,
                            user_agent: navigator.userAgent,
                            screen: screen.width + 'x' + screen.height,
                            timestamp: new Date().toISOString(),
                        },
                    }),
                });

                const data = await res.json();
                this.hideTyping();

                console.log('AI Chatbot response:', data);

                if (data.ok) {
                    console.log('Reply text:', data.data && data.data.reply);
                    // Persist session token + session_id for visitor-based sessions
                    if (data.data && data.data.session_token) {
                        this.sessionToken = data.data.session_token;
                        try {
                            localStorage.setItem('ai_chat_token_' + this.visitorId, data.data.session_token);
                            localStorage.setItem('ai_chat_sid_' + this.visitorId + '_' + this.config.chatbot_id, data.data.session_id);
                        } catch(e) {}
                    }
                    this.addMessage('bot', data.data.reply);
                    if (data.data.should_collect_contact && !this.contactShown) {
                        this.contactShown = true;
                        this.showContactForm();
                    }
                } else {
                    console.warn('API error:', data.message || data.code);
                    this.addMessage('bot', 'Sorry: ' + (data.message || 'Request failed.'));
                }
            } catch (err) {
                console.error('Chat fetch error:', err);
                this.hideTyping();
                this.addMessage('bot', 'Sorry, a network error occurred. Please try again.');
            }
        }

        addMessage(role, content) {
            const div = document.createElement('div');
            div.className = 'ai-chatbot-message ai-chatbot-' + role;

            if (role === 'bot' && this.config.avatar) {
                const img = document.createElement('img');
                img.className = 'ai-chatbot-avatar';
                img.src = this.config.avatar;
                img.alt = '';
                div.appendChild(img);
            }

            const bubble = document.createElement('div');
            bubble.className = 'ai-chatbot-bubble';

            if (role === 'bot') {
                bubble.innerHTML = this.renderMarkdown(content);
            } else {
                bubble.textContent = content;
            }

            div.appendChild(bubble);
            this.messagesEl.appendChild(div);
            this.scrollToBottom();
        }

        showTyping() {
            const div = document.createElement('div');
            div.className = 'ai-chatbot-message ai-chatbot-bot';
            div.id = this.widgetId + '-typing';

            const bubble = document.createElement('div');
            bubble.className = 'ai-chatbot-bubble ai-chatbot-typing';
            bubble.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
            div.appendChild(bubble);
            this.messagesEl.appendChild(div);
            this.scrollToBottom();
        }

        hideTyping() {
            const el = document.getElementById(this.widgetId + '-typing');
            if (el) el.remove();
        }

        showContactForm() {
            // Simple contact collection prompt
            const div = document.createElement('div');
            div.className = 'ai-chatbot-message ai-chatbot-bot';
            const bubble = document.createElement('div');
            bubble.className = 'ai-chatbot-bubble ai-chatbot-contact-form';
            bubble.innerHTML = '<p>Would you like to leave your contact information?</p>' +
                '<input type="text" placeholder="Name" class="ai-chat-contact-input" />' +
                '<input type="email" placeholder="Email" class="ai-chat-contact-input" />' +
                '<button class="ai-chat-contact-submit">Submit</button>';
            div.appendChild(bubble);
            this.messagesEl.appendChild(div);
        }

        renderMarkdown(text) {
            if (typeof text !== 'string') return '';
            try {
                // Simple Markdown rendering (bold, italic, links, line breaks)
                let html = text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                    .replace(/\*(.+?)\*/g, '<em>$1</em>')
                    .replace(/\[(.+?)\]\((.+?)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>')
                    .replace(/\n/g, '<br>');
                return html;
            } catch (e) {
                console.warn('AI Chatbot markdown render error:', e);
                return this.escapeHtml(text);
            }
        }

        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        autoResize() {
            if (this.inputEl) {
                this.inputEl.style.height = 'auto';
                this.inputEl.style.height = Math.min(this.inputEl.scrollHeight, 120) + 'px';
            }
        }

        scrollToBottom() {
            if (this.messagesEl) {
                this.messagesEl.scrollTop = this.messagesEl.scrollHeight;
            }
        }
    }

    // Initialize on Elementor frontend ready
    if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
        elementorFrontend.hooks.addAction('frontend/element_ready/ai_chatbot', function($scope) {
            $scope.find('.ai-chatbot-container').each(function() {
                var el = this;
                var widgetId = el.dataset.widgetId || el.id.replace('ai-chatbot-container-', '');
                var config = window['AIChatConfig_' + widgetId];
                if (config && !el.__chatWidget) {
                    el.__chatWidget = new AIChatWidget(el.id, config);
                }
            });
        });
    }

    // Also init on page load for non-Elementor usage
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.ai-chatbot-container').forEach(function(el) {
            var widgetId = el.dataset.widgetId || el.id.replace('ai-chatbot-container-', '');
            var config = window['AIChatConfig_' + widgetId];
            if (config && !el.__chatWidget) {
                el.__chatWidget = new AIChatWidget(el.id, config);
            }
        });
    });
})();
