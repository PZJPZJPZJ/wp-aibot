# AI Chatbot — WordPress AI 聊天机器人插件

**定位：API 调度中枢** — 用户发起聊天 → 后台响应 → 嵌入提示词 + 知识全文 → 调用 AI → 返回数据 → 呈现并记录 → AI 判断线索 → 触发通知。

---

## 核心特性

- **多机器人完全独立** — 每个机器人拥有独立的提示词、知识库、对话记忆、线索字段、样式和通知配置
- **Elementor Widget** — 精简版 Widget，仅做机器人选择，不承载样式控制
- **后台在线编辑前端 UI** — CodeMirror 编辑器，HTML/CSS/JS 均可自定义，模板变量替换
- **知识库管理** — Markdown 知识文档，支持在线编写和 .md 文件上传，当前全文注入 AI 上下文
- **对话记忆** — 对话历史序列化为 Markdown 格式，按机器人隔离存储
- **AI 线索采集** — 通过 System Prompt 约束 AI 返回结构化 JSON，实现自动线索提取与评分
- **通知** — 企业微信 Webhook + Email，基于线索评分触发

---

## 扩展预留

| 预留点 | 未来接入方式 |
|--------|------------|
| **RAG 检索** | 替换 `load_context()` 为检索逻辑，外部系统通过 filter 接入 |
| **Embeddings API** | 已预留 `embed()` 方法，后续接入向量数据库 |
| **多 AI Provider** | 定义 `AI_Provider_Interface`，新增 Provider 实现该接口，如 Anthropic / Gemini / Ollama |
| **Streaming** | 客户端 EventSource，服务端 Chunked 输出 |
| **第三方通知** | 通过 `apply_filters` 支持 Slack / Discord / Telegram |
| **Gutenberg Block** | 注册 `wp.block` 与 Elementor Widget 类似 |
| **Analytics** | 后台图表统计（对话量、线索转化率等） |

---
