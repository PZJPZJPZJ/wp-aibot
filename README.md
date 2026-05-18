# AI Chatbot — WordPress 插件开发文档

> WordPress AI 聊天机器人插件。Elementor Widget（仅选择机器人），多实例管理，后台在线编辑前端 UI，Markdown 对话持久化，AI 线索采集与通知。
> 
> **定位：API 调度中枢** — 用户发起聊天 → 后台响应 → 嵌入提示词 + 知识全文 → 调用 AI → 返回数据 → 呈现并记录 → AI 判断线索 → 触发通知。

---

## 一、核心需求矩阵

| 需求 | 实现方式 |
|------|---------|
| **通用插件** | 不绑定特定品牌，所有文案/提示词均可后台配置 |
| **Elementor 组件** | 注册为 Elementor Widget，**仅做机器人选择**，不承载样式控制 |
| **多机器人完全独立** | Custom Post Type `ai_chatbot`，每个实例的提示词、知识库、对话记忆、线索字段、样式、通知完全独立 |
| **后台在线编辑前端 UI** | HTML/CSS/JS 代码编辑器（CodeMirror），模板变量替换，所见即所得 |
| **知识库管理** | 独立 CPT 管理 Markdown 知识文档，支持在线编写、上传 .md |
| **知识注入** | **全文注入** AI 上下文，不实现本地 RAG；预留 Embeddings API 扩展接口 |
| **对话记忆** | 对话历史序列化为 Markdown 格式，按机器人隔离存储 |
| **线索采集** | System Prompt 约束 AI 返回结构化 JSON（已验证可行） |
| **通知** | 企业微信 Webhook + Email，基于线索评分触发 |

---

## 二、用户界面交互流程

### 管理员视角

```
WordPress 后台
│
├── AI Chatbots (CPT 列表 — 每个机器人完全独立)
│   ├── 新建机器人
│   ├── 编辑机器人
│   │   ├── ── 基本设置 ──
│   │   │    ├── 名称、别名、状态 (发布/草稿)
│   │   │    ├── 头像/图标（可上传自定义图标）
│   │   │    └── 初始问候语、离线自动回复文本
│   │   │
│   │   ├── ── AI 配置（独立）──
│   │   │    ├── Provider (OpenAI / OpenRouter / DeepSeek / 自定义)
│   │   │    ├── [扩展预留] 后续可接入 Anthropic、Gemini、Ollama 等
│   │   │    ├── API 地址、API Key、Model
│   │   │    ├── Temperature、Max Tokens
│   │   │    └── System Prompt（代码编辑器，支持变量占位符）
│   │   │
│   │   ├── ── 知识库（独立绑定）──
│   │   │    ├── 从已有知识文档中选择（多选checkbox）
│   │   │    ├── 或快速新建一篇绑定到此机器人
│   │   │    └── [扩展预留] 后续版本支持检索策略、Embeddings、RAG
│   │   │
│   │   ├── ── 对话记忆（独立）──
│   │   │    ├── 上下文轮数 (max_history，默认 10)
│   │   │    ├── 会话过期时间 (TTL)
│   │   │    └── 记忆注入格式配置
│   │   │
│   │   ├── ── 线索收集（独立）──
│   │   │    ├── 启用/禁用线索采集
│   │   │    ├── 自定义字段列表（增删改、排序、必填开关、标签文案）
│   │   │    ├── 线索评分规则自定义
│   │   │    └── 通过 System Prompt 约束 AI 输出结构化 JSON
│   │   │
│   │   ├── ── 前端 UI（在线编辑）──
│   │   │    ├── 布局模式: inline / popup / floating
│   │   │    ├── HTML 模板编辑器（CodeMirror 高亮）
│   │   │    │   └── 可编辑整个聊天组件的 HTML 结构
│   │   │    ├── CSS 编辑器（CodeMirror 高亮）
│   │   │    │   └── 自由 CSS 编写
│   │   │    └── JS 编辑器（CodeMirror 高亮）
│   │   │        └── 自定义 JS 逻辑/事件钩子
│   │   │
│   │   └── ── 通知配置（独立）──
│   │        ├── 启用/禁用
│   │        ├── 邮件通知（收件人、邮件模板）
│   │        ├── Webhook URL（企业微信等）
│   │        └── 通知触发条件（评分阈值）
│   │
│   └── 预览机器人（打开独立页面测试对话）
│
├── Knowledge Base (独立 CPT: ai_knowledge)
│   ├── 新建知识文档
│   │   ├── 在线 Markdown 编辑器（分屏：编辑←→预览）
│   │   ├── 从 .md 文件导入（拖拽上传）
│   │   └── 元数据: 标题、分类、标签
│   ├── 编辑知识文档
│   │   ├── Markdown 全文编辑
│   │   ├── 版本历史（修订对比）
│   │   └── 关联的机器人列表
│   └── 分类/标签管理
│
└── Conversations (对话记录列表)
    ├── 按机器人筛选
    ├── 按日期/评分/IP筛选
    ├── 查看完整对话 (Markdown 格式渲染)
    └── 导出对话 (MD / CSV / JSON)
```

### Elementor 编辑器视角

```
Elementor 编辑器
│
└── 拖入 "AI Chatbot" Widget（精简版）
    ├── 选择机器人实例 (下拉选择，仅此一项)
    └── 渲染输出：后台 HTML 模板 + CSS + JS
        └── 所有样式控制由后台 HTML/CSS 编辑完成
```

### 前端用户视角

```
发布后页面
│
└── 聊天组件渲染
    ├── Inline 模式：直接在页面中嵌入
    └── Popup/Window 模式：浮动按钮 → 弹出聊天窗口
```

---

## 三、技术架构

```
┌──────────────────────────────────────────────────────────────────┐
│                      WordPress                                    │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │                  Admin Panel                                  │ │
│  │  ┌────────────┐  ┌──────────────┐  ┌────────────────────┐   │ │
│  │  │ Chatbot CPT│  │ Knowledge    │  │ Conversations      │   │ │
│  │  │ - CRUD     │  │ Base (MD)    │  │ - 对话列表         │   │ │
│  │  │ - 元配置   │  │ - 上传/编辑  │  │ - Markdown 查看    │   │ │
│  │  │ - 样式设置 │  │ - 分类/标签  │  │ - 导出             │   │ │
│  │  │ - 预览     │  │              │  │ - 线索标记         │   │ │
│  │  └─────┬──────┘  └──────┬───────┘  └────────────────────┘   │ │
│  └────────┼─────────────────┼───────────────────────────────────┘ │
│           │                 │                                      │
│           ▼                 ▼                                      │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │               Core Engine                                     │ │
│  │                                                               │ │
│  │  ┌──────────────┐                                             │ │
│  │  │ Chat Router  │──▶ 1. 加载机器人配置                        │ │
│  │  │ (REST API)   │──▶ 2. 加载知识全文                          │ │
│  │  │              │──▶ 3. 加载对话历史                          │ │
│  │  │ POST /v1/    │──▶ 4. 组装 System Prompt                    │ │
│  │  │   chat       │──▶ 5. 调用 AI API                           │ │
│  │  └──────┬───────┘──▶ 6. 解析线索 + 保存对话                  │ │
│  │         │          ▶ 7. 触发通知 (Webhook/Email)              │ │
│  │         │                                                     │ │
│  │         ▼                                                     │ │
│  │  ┌───────────────────────────────────────────────────────┐   │ │
│  │  │              AI Provider Layer                         │   │ │
│  │  │  ┌──────────────────────────────────────────────────┐ │   │ │
│  │  │  │  OpenAI 兼容 API （OpenAI / OpenRouter / DeepSeek│ │   │ │
│  │  │  │  ... 等任何 Chat Completions 接口）               │ │   │ │
│  │  │  └──────────────────────────────────────────────────┘ │   │ │
│  │  │  ┌─────────────────┐  [扩展预留]                     │   │ │
│  │  │  │ Provider_Interface │ → Anthropic / Gemini / Ollama │   │ │
│  │  │  └─────────────────┘                               │   │ │
│  │  └───────────────────────┬───────────────────────────────┘   │ │
│  │                          │                                    │ │
│  │  ┌───────────────────────▼───────────────────────────────┐   │ │
│  │  │              Response Pipeline                         │   │ │
│  │  │  ┌──────────┐  ┌──────────┐  ┌──────────────────┐    │   │ │
│  │  │  │ Lead     │  │ Memory   │  │ Notifier         │    │   │ │
│  │  │  │ Extractor│  │ Saver    │  │ (WeCom Webhook   │    │   │ │
│  │  │  │ (JSON    │  │ (MD)     │  │  / Email)        │    │   │ │
│  │  │  │  parse)  │  │          │  │                  │    │   │ │
│  │  │  └──────────┘  └──────────┘  └──────────────────┘    │   │ │
│  │  └──────────────────────────────────────────────────────┘   │ │
│  └──────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────────┐ │
│  │              Elementor Widget（精简版）                        │ │
│  │  ┌──────────────────────────────────────────────────────────┐ │ │
│  │  │  AI_Chatbot_Widget                                       │ │ │
│  │  │  - chatbot_selector（仅此一个控件）                       │ │ │
│  │  │  - render() → 输出后端 HTML 模板                         │ │ │
│  │  └──────────────────────────────────────────────────────────┘ │ │
│  │                                                               │ │
│  │  ┌──────────────────────────────────────────────────────────┐ │ │
│  │  │  Frontend Assets                                         │ │ │
│  │  │  chat-widget.js  |  chat-widget.css  |  marked.min.js    │ │ │
│  │  └──────────────────────────────────────────────────────────┘ │ │
│  └──────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────┘
```

---

## 四、插件文件结构

```
wp-content/plugins/ai-chatbot-elementor/
├── ai-chatbot-elementor.php                # 插件主文件
│
├── includes/
│   ├── class-plugin.php                    # 核心引导类
│   ├── class-cpt-chatbot.php               # CPT: ai_chatbot 注册 + Meta
│   ├── class-cpt-knowledge.php             # CPT: ai_knowledge 注册 + Meta
│   ├── class-cpt-conversation.php          # CPT: ai_conversation 注册 + Meta
│   │
│   ├── class-chat-api.php                  # REST API 路由（公共访问）
│   ├── class-ai-client.php                 # AI Provider 封装 [+ 扩展接口]
│   ├── class-knowledge-loader.php          # 知识库加载器（全文注入）
│   │   └── [扩展预留] → 后续替换为 class-rag-engine.php
│   ├── class-memory-manager.php            # Markdown 对话记忆
│   ├── class-lead-processor.php            # 线索提取 + 评分
│   ├── class-notifier.php                  # 通知（Webhook / Email）
│   │
│   ├── class-elementor-widget.php          # Elementor Widget（精简版）
│   │
│   ├── class-admin-columns.php             # 后台列表自定义列
│   ├── class-admin-ajax.php                # 后台 AJAX (预览等)
│   ├── class-export.php                    # 对话导出
│   │
│   └── class-installer.php                 # 激活/卸载处理
│
├── assets/
│   ├── js/
│   │   ├── chat-widget.js                  # 前端聊天引擎
│   │   └── admin-knowledge.js              # 知识库管理 JS
│   ├── css/
│   │   ├── chat-widget.css                 # 前端聊天样式
│   │   └── admin.css                       # 后台管理样式
│   └── lib/
│       └── marked.min.js                   # Markdown 渲染 (前端)
│
├── templates/
│   ├── chat-widget-render.php              # Widget 渲染模板
│   ├── admin-chatbot-meta-box.php          # 机器人元数据配置面板
│   ├── admin-knowledge-meta-box.php        # 知识库元数据面板
│   └── admin-settings.php                  # 全局设置页
│
└── readme.txt                              # WP 插件仓库信息
```

---

## 五、数据库/存储设计

### 5.1 自定义文章类型 (Custom Post Types)

| CPT | Slug | 用途 |
|-----|------|------|
| AI Chatbot | `ai_chatbot` | 每个机器人实例 |
| Knowledge Doc | `ai_knowledge` | 每篇知识文档 |
| Conversation | `ai_conversation` | 每次对话会话 |

### 5.2 Chatbot CPT Meta Fields

```php
$meta = [
    // === AI 配置（独立） ===
    'chatbot_provider'          => 'openai',
    'chatbot_api_base_url'      => 'https://api.openai.com/v1',
    'chatbot_api_key'           => 'sk-...',       // 加密存储
    'chatbot_model'             => 'gpt-4o-mini',
    'chatbot_temperature'       => 0.2,
    'chatbot_max_tokens'        => 2000,
    'chatbot_system_prompt'     => '...',           // 见第六节完整示例

    // === 知识库（独立绑定，全文注入） ===
    'chatbot_knowledge_ids'     => [12, 34, 56],    // ai_knowledge ID 数组
    // [扩展预留] chatbot_retrieval_mode → 后续支持 keyword | semantic | hybrid
    // [扩展预留] chatbot_embedding_config → 后续支持 Embeddings API

    // === 对话记忆（独立） ===
    'chatbot_max_history'       => 10,
    'chatbot_session_ttl'       => 60,              // 分钟

    // === 基本设置 ===
    'chatbot_greeting'          => 'Hello! How can I help you?',
    'chatbot_offline_msg'       => 'We are offline. Please leave a message.',
    'chatbot_avatar'            => '',              // 附件 ID

    // === 前端 UI（在线编辑，每个机器人独立） ===
    'chatbot_layout_mode'       => 'inline',        // inline | popup | floating
    'chatbot_html_template'     => '<div ...>{{avatar}}...',  // CodeMirror 编辑
    'chatbot_custom_css'        => '',              // 自定义 CSS
    'chatbot_custom_js'         => '',              // 自定义 JS

    // === 线索收集（独立） ===
    'chatbot_lead_enabled'      => true,
    'chatbot_lead_fields'       => [ /* 字段配置 */ ],
    'chatbot_lead_score_rules'  => [ /* 评分规则 */ ],

    // === 通知（独立） ===
    'chatbot_notify_enabled'    => false,
    'chatbot_notify_email'      => '',
    'chatbot_notify_webhook'    => '',              // 企业微信 Webhook URL
    'chatbot_notify_on_scores'  => ['A', 'B'],

    // === i18n 文本（独立） ===
    'chatbot_i18n' => [
        'title'              => 'AI Assistant',
        'subtitle'           => 'Ask me anything',
        'input_placeholder'  => 'Type your message...',
        'thinking_text'      => 'Thinking...',
    ],
];
```

### 5.3 Knowledge Doc CPT Meta Fields

```php
$meta = [
    'knowledge_markdown'        => '# Title\n\nContent...',
    'knowledge_file_path'       => '',              // 上传文件路径（可选）
    'knowledge_categories'      => ['product', 'faq'],
    'knowledge_tags'            => ['pricing', 'setup'],
    'knowledge_language'        => 'en',

    // [扩展预留] knowledge_chunks → 后续 RAG 方案使用
    // [扩展预留] knowledge_embeddings → 后续向量化使用
];
```

知识库当前策略：**全文注入**。系统将选中知识文档的 Markdown 全文拼入 System Prompt。
扩展预留：后续如需 RAG，在 `class-knowledge-loader.php` 中添加分块 + 检索逻辑即可，上层调用接口不变。

### 5.4 Conversation CPT Meta Fields

```php
$meta = [
    'conversation_chatbot_id'    => 123,
    'conversation_session_id'    => 'sess_abc123',

    // Markdown 格式对话历史
    'conversation_history'       => "**User:** ...\n**Assistant:** ...",

    // 访客数据（服务端采集）
    'conversation_visitor_ip'       => '192.168.1.1',
    'conversation_visitor_ua'       => 'Mozilla/5.0...',
    'conversation_visitor_page_url' => 'https://example.com/pricing',
    'conversation_visitor_referrer' => 'https://google.com/...',
    'conversation_visitor_language' => 'en-US,en;q=0.9',
    'conversation_visitor_country'  => '',       // 可选 GeoIP

    // 对话统计
    'conversation_message_count' => 5,
    'conversation_started_at'    => '2025-01-15 10:00:00',

    // 提取的线索数据
    'conversation_lead_data'  => [
        'lead_score' => 'A',
        'name'       => 'John',
        'email'      => 'john@example.com',
        // ...
    ],
];
```

---

## 六、核心模块详细设计

### 6.1 聊天 API — `class-chat-api.php`

**路由：** `POST /wp-json/ai-chat/v1/chat`

**公共访问鉴权策略：**
- 使用 `permission_callback => '__return_true'`
- `X-WP-Nonce` 对未登录用户无效，故更换为 **Session 签名机制**
- 前端生成 `session_id`（UUID v4），请求时携带 `X-Session-Token: {hmac(session_id, site_key)}`
- 服务端验证签名，防止会话伪造
- IP + Session 双重限流（每分钟 30 次/每 IP）

```json
// Request
{
  "chatbot_id": 123,
  "message": "I want to know about your pricing",
  "session_id": "sess_abc123",
  "session_token": "hmac_signature",
  "metadata": {
    "page": "https://example.com/pricing",
    "referrer": "https://google.com/",
    "language": "en-US"
  }
}

// Response
{
  "ok": true,
  "data": {
    "reply": "Based on our documentation...",
    "session_id": "sess_abc123",
    "conversation_id": 456,
    "lead_score": "C",
    "should_notify_sales": false,
    "should_collect_contact": false
  }
}
```

**处理流程：**

```
POST /chat
  │
  ├─ 1. Session Token 验证
  ├─ 2. chatbot_id 存在且已发布
  ├─ 3. 消息长度校验 (1-2000 字符)
  ├─ 4. 限流检查 (IP + session_id)
  │
  ├─ 5. 采集访客数据（服务端 IP/UA + 客户端 metadata）
  │
  ├─ 6. Memory Manager → 加载 Markdown 对话历史
  │
  ├─ 7. Knowledge Loader → 加载知识全文
  │   └── 拼接 chatbot_knowledge_ids → 读取 Markdown → 注入 System Prompt
  │   └── [扩展预留] 后续替换为 RAG 引擎检索
  │
  ├─ 8. AI Client → chat completion（含完整 System Prompt）
  │
  ├─ 9. Lead Processor → 解析 JSON 线索
  │
  ├─ 10. Memory Manager → 保存对话（Markdown 格式）
  │
  ├─ 11. 判断 should_notify_sales → 触发异步通知
  │
  └─ 12. 返回 { reply, session_id, lead_score, ... }
```

### 6.2 知识库加载器 — `class-knowledge-loader.php`

**当前实现（全文注入）：**

```php
class Knowledge_Loader {
    /**
     * 当前策略：将绑定的知识文档全文拼接为 context 字符串。
     *
     * [扩展预留]
     * 后续如需 RAG 检索，修改此方法即可：
     * - 替换为调用 RAG_Engine->retrieve_relevant($query, $chunks)
     * - 上层调用接口不变
     * - 外部系统（如 Pinecone、Milvus）也可通过 filter 接入
     */
    public function load_context(int $chatbot_id): string {
        $knowledge_ids = get_post_meta($chatbot_id, 'chatbot_knowledge_ids', true);
        if (empty($knowledge_ids)) {
            return '';
        }

        $parts = [];
        foreach ((array) $knowledge_ids as $doc_id) {
            $doc = get_post($doc_id);
            if (!$doc || $doc->post_status !== 'publish') continue;
            $markdown = get_post_meta($doc_id, 'knowledge_markdown', true);
            if (!empty($markdown)) {
                $title = $doc->post_title;
                $parts[] = "---\nSource: {$title}\n{$markdown}\n---";
            }
        }

        return implode("\n\n", $parts);
    }
}
```

**扩展预留点：**
- `apply_filters('ai_chatbot_knowledge_context', $context, $chatbot_id, $query)` — 允许第三方插件替换上下文
- `apply_filters('ai_chatbot_retrieval_strategy', 'fulltext', $chatbot_id)` — 未来可切换策略

### 6.3 AI Client — `class-ai-client.php`

```php
/**
 * AI Provider 封装
 *
 * 当前支持：OpenAI 兼容 API（OpenAI / OpenRouter / DeepSeek 等）
 *
 * [扩展预留] AI_Provider_Interface
 * 后续新增 Provider 实现接口即可注册：
 * - Anthropic_Provider
 * - Gemini_Provider
 * - Ollama_Provider (本地)
 */
class AI_Client {

    /**
     * 调用 Chat Completions
     * 当前：直接传递 messages + 参数
     * [扩展预留] 后续可添加 tool_use、streaming 等
     */
    public function chat(array $messages, array $config): array {
        $body = [
            'model'       => $config['model'],
            'messages'    => $messages,
            'temperature' => $config['temperature'] ?? 0.2,
            'max_tokens'  => $config['max_tokens'] ?? 2000,
        ];

        return $this->request($body, $config);
    }

    /**
     * [扩展预留] Embeddings API
     * 后续接入语义检索时使用
     */
    public function embed(string $text, array $config): array {
        // POST {base_url}/embeddings
    }

    /**
     * [扩展预留] 获取可用模型列表
     */
    public function list_models(array $config): array {
        // GET {base_url}/models
    }
}
```

### 6.4 System Prompt 组装与线索提取

这是插件的核心价值——通过精心设计的 System Prompt 约束 AI，**无需 JSON Mode**，纯提示词即可实现结构化输出。

**已通过验证的 System Prompt 模板**：

```
You are {role}.

You serve B2B customers including:
- {customer_types}

Business context:
{company_description}

Your goals:
1. Answer questions about {company_name}'s solutions.
2. Recommend relevant products.
3. Guide users to clarify project requirements.
4. Collect lead information: {lead_fields}.
5. Never invent prices, delivery time, certifications, project references, contract terms, or legal commitments.
6. If quotation is requested, ask for drawings, BOQ, project location, product scope, quantity, and timeline.
7. Detect the user's language and answer in the same language.
8. Keep the answer concise, professional, international, and B2B sales-oriented.
9. If the user is not ready to leave contact information, continue helping and recommend relevant next steps.

Lead scoring:
A = clear project + country/city + products + scale/units + contact info + drawings/BOQ or timeline.
B = clear product/project need + contact info, but missing drawings/quantity/timeline.
C = general product research without contact info.
D = unrelated, spam, or low-intent chat.

Return ONLY valid JSON, no markdown, no code fences, no extra text, in this exact shape:
{
  "answer": "natural language response to visitor",
  "lead": {
    "lead_score": "A|B|C|D",
    "customer_type": "...",
    "name": "",
    "email": "",
    "whatsapp": "",
    "country": "",
    "city": "",
    "project_type": "",
    "number_of_units": "",
    "products_needed": [],
    "has_drawings": false,
    "timeline": "",
    "source_page": "",
    "summary": "",
    "recommended_next_step": ""
  },
  "should_notify_sales": false
}

Rules for should_notify_sales:
- true for lead_score A or B.
- true if the user provided email or WhatsApp and has a project/product request.
- false for C or D unless contact details and project intent are present.

---

{knowledge_context}

---

{conversation_history}

User: {user_message}
```

**重组逻辑**（在 `class-chat-api.php` 中）：

```php
$messages = [
    [
        'role'    => 'system',
        'content' => $this->build_system_prompt($chatbot, $knowledge_context),
    ],
    ...$memory_history,   // 历史消息（role user / assistant）
    [
        'role'    => 'user',
        'content' => $user_message,
    ],
];
```

**线索解析**（在 `class-lead-processor.php` 中）：

```php
class Lead_Processor {
    /**
     * 从 AI 回复中解析 JSON
     * AI 被要求返回纯 JSON，直接 json_decode
     * [防御] 如果 AI 不遵守格式，尝试从 ```json ... ``` 中提取
     */
    public function parse(string $ai_response): ?array {
        // 1. 直接尝试 json_decode
        $data = json_decode($ai_response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        // 2. 尝试从 markdown code fence 中提取
        preg_match('/```(?:json)?\s*({[\s\S]*?})\s*```/', $ai_response, $matches);
        if (!empty($matches[1])) {
            $data = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }
        }

        // 3. 无法解析，记录日志
        return null;
    }
}
```

### 6.5 Elementor Widget（精简版）— `class-elementor-widget.php`

仅保留机器人选择器，所有 UI 渲染由后台 HTML 模板 + CSS + JS 完成。

```php
class AI_Chatbot_Widget extends \Elementor\Widget_Base {

    public function get_name(): string {
        return 'ai_chatbot';
    }

    public function get_title(): string {
        return 'AI Chatbot';
    }

    public function get_icon(): string {
        return 'eicon-comments';
    }

    protected function register_controls(): void {
        $this->start_controls_section('section_content', [
            'label' => 'Chatbot Settings',
        ]);

        $this->add_control('chatbot_id', [
            'label'   => 'Select Chatbot',
            'type'    => \Elementor\Controls_Manager::SELECT,
            'options' => $this->get_chatbot_options(),
            'default' => '',
        ]);

        $this->end_controls_section();
    }

    public function render(): void {
        $settings = $this->get_settings_for_display();
        $chatbot_id = $settings['chatbot_id'] ?? 0;

        if (empty($chatbot_id) || get_post_type($chatbot_id) !== 'ai_chatbot') {
            echo '<div class="ai-chat-error">Please select a chatbot in the widget settings.</div>';
            return;
        }

        // 会话 ID（基于访客 IP + 机器人 ID）
        $session_id = 'sess_' . md5($_SERVER['REMOTE_ADDR'] . $chatbot_id);
        $session_token = $this->generate_session_token($session_id);

        // 暴露数据给前端 JS
        wp_localize_script('ai-chat-widget', 'AIChatConfig_' . $this->get_id(), [
            'chatbot_id'    => $chatbot_id,
            'session_id'    => $session_id,
            'session_token' => $session_token,
            'api_url'       => rest_url('ai-chat/v1/chat'),
            'layout_mode'   => get_post_meta($chatbot_id, 'chatbot_layout_mode', true) ?: 'inline',
            'greeting'      => get_post_meta($chatbot_id, 'chatbot_greeting', true) ?: 'Hello!',
            'i18n'          => get_post_meta($chatbot_id, 'chatbot_i18n', true) ?: [],
            'avatar'        => wp_get_attachment_url(get_post_meta($chatbot_id, 'chatbot_avatar', true)),
        ]);

        // 输出机器人自定义 HTML 模板（含变量替换）
        echo $this->render_html_template($chatbot_id);
    }

    /**
     * Session Token 生成：HMAC 签名
     * 防止会话伪造，不依赖 WP Nonce
     */
    private function generate_session_token(string $session_id): string {
        $secret = defined('AI_CHAT_SESSION_SECRET')
            ? AI_CHAT_SESSION_SECRET
            : wp_salt('auth');
        return hash_hmac('sha256', $session_id, $secret);
    }
}
```

### 6.6 前端聊天引擎 — `assets/js/chat-widget.js`

```javascript
class AIChatWidget {
    constructor(containerId, config) {
        this.containerId = containerId;
        this.config = config;
        this.sessionId = config.session_id;
        this.sessionToken = config.session_token;
        this.init();
    }

    async sendMessage(text) {
        this.addMessage('user', text);
        this.showTyping();

        try {
            const res = await fetch(this.config.api_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    chatbot_id: this.config.chatbot_id,
                    message: text,
                    session_id: this.sessionId,
                    session_token: this.sessionToken,
                    metadata: {
                        page: location.href,
                        referrer: document.referrer,
                        language: navigator.language,
                        user_agent: navigator.userAgent,
                        screen: `${screen.width}x${screen.height}`,
                        timestamp: new Date().toISOString(),
                    },
                }),
            });

            const data = await res.json();
            this.hideTyping();

            if (data.ok) {
                this.addMessage('bot', data.data.reply);
                if (data.data.should_collect_contact) {
                    this.showContactForm();
                }
            }
        } catch (err) {
            this.hideTyping();
            this.addMessage('bot', 'Sorry, an error occurred.');
        }
    }
}
```

### 6.7 通知 — `class-notifier.php`

```php
class Notifier {
    /**
     * 根据线索评分触发通知
     *
     * 当前支持：
     * - 企业微信 Webhook
     * - Email
     *
     * [扩展预留]
     * - Slack / Discord 等通过 filter 扩展
     * - 后续可接入第三方通知服务
     */
    public function notify(array $lead_data, array $visitor_data, array $config): void {
        if (empty($config['notify_on_scores'])
            || !in_array($lead_data['lead_score'] ?? '', $config['notify_on_scores'])) {
            return;
        }

        $payload = array_merge($lead_data, ['visitor' => $visitor_data]);

        // Webhook（企业微信等）
        if (!empty($config['webhook_url'])) {
            $this->send_webhook($config['webhook_url'], $payload);
        }

        // Email
        if (!empty($config['email'])) {
            $this->send_email($config['email'], $payload);
        }
    }
}
```

---

## 七、安全性设计

| 层次 | 措施 |
|------|------|
| **API Key** | 存储在 `wp_options`，支持 `define()` 常量覆盖，前端不可读 |
| **会话伪造** | Session Token HMAC 签名验证，不依赖 WP Nonce |
| **CSRF** | REST API 无需 cookie 认证，无 CSRF 风险 |
| **Rate Limit** | 基于 IP + session_id，每分钟最多 30 次请求，返回 429 |
| **输入过滤** | `message` 最大 2000 字符；`session_id` 仅允许字母数字和短横线 |
| **XSS** | 前端 `textContent` 渲染；Markdown 用 `marked.js`（sanitize） |
| **后台权限** | 管理页面 `current_user_can('manage_options')` |

---

## 八、分阶段实施计划

```
Phase 1 — 核心骨架 (Day 1-2)
├── 1.1 插件主文件 + 引导类
├── 1.2 CPT: ai_chatbot (注册 + meta boxes)
├── 1.3 CPT: ai_knowledge (注册 + meta boxes)
├── 1.4 后台设置页面 (全局 API Key 等)
└── 1.5 Elementor Widget 精简注册 (仅机器人选择)

Phase 2 — AI 对话核心 (Day 3-5)
├── 2.1 AI Client (OpenAI 兼容 API)
├── 2.2 REST API 路由 + Session Token 鉴权 + 限流
├── 2.3 Knowledge Loader (全文注入)
├── 2.4 Memory Manager (Markdown 格式存储)
├── 2.5 Lead Processor (JSON 解析)
├── 2.6 前端 Chat Widget (Vanilla JS)
└── 2.7 Elementor Widget 完善

Phase 3 — 通知与管理 (Day 6-8)
├── 3.1 Notifier (企业微信 Webhook + Email)
├── 3.2 CPT: ai_conversation + 对话查看
├── 3.3 访客数据采集管线
├── 3.4 后台对话列表 + 线索展示
├── 3.5 对话导出 (MD / CSV)
└── 3.6 后台 HTML/CSS/JS 编辑器 (CodeMirror)

Phase 4 — 生产加固 (Day 9-10)
├── 4.1 安全审计 (限流 / 输入过滤验证)
├── 4.2 错误处理 + 日志
├── 4.3 隐私合规 (GDPR IP 匿名化)
├── 4.4 WP CLI 命令
├── 4.5 国际化 (i18n)
└── 4.6 文档 + 部署检查
```

---

## 九、扩展预留清单

| 预留点 | 位置 | 未来接入方式 |
|--------|------|------------|
| **RAG 检索** | `class-knowledge-loader.php` | 替换 `load_context()` 为检索逻辑，外部系统通过 filter 接入 |
| **Embeddings API** | `class-ai-client.php` | 已预留 `embed()` 方法，后续接入向量数据库 |
| **多 Provider** | `class-ai-client.php` | 定义 `AI_Provider_Interface`，新增 Provider 实现该接口 |
| **Streaming** | `class-chat-api.php` | 客户端 EventSource，服务端 Chunked 输出 |
| **第三方通知** | `class-notifier.php` | 通过 `apply_filters` 支持 Slack / Discord / Telegram |
| **Gutenberg Block** | — | 注册 `wp.block` 与 Elementor Widget 类似 |
| **Analytics** | — | 后台图表统计（对话量、线索转化率等） |

---

## 十、依赖清单

| 依赖 | 用途 | 必须? |
|------|------|-------|
| Elementor (免费版) | Widget 渲染 | 是 |
| PHP 8.0+ | 运行环境 | 是 |
| WordPress 6.0+ | 运行环境 | 是 |
| cURL 扩展 | AI API 调用 | 是 |
| marked.js (CDN) | 前端 MD 渲染 | 是 |
| OpenAI / 兼容 API | AI 对话 | 是 |
| `wp_remote_post()` | HTTP 请求 | 内置 |
| `wp_mail()` | 邮件通知 | 内置 |
