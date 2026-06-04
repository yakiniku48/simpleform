# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 概要

PHPで実装したシンプルなお問い合わせフォーム。フレームワーク・Composerなし。`libs/` 配下に自作のミニフレームワークを持つ。

## ローカル環境での確認

ビルドプロセス・テストスイートなし。PHPの組み込みサーバーで動作確認できる。

```sh
php -S localhost:8080
```

ただし `.htaccess` によるリライトはApache前提のため、ルーティングの確認はApache環境が必要。

## アーキテクチャ

### リクエストフロー

すべてのリクエストは `.htaccess` が `index.php` に転送する。

```
GET  /         → showForm()
GET  /form     → showForm()
POST /confirm  → showConfirm()  (CSRF確認→バリデーション→確認画面 or リダイレクト)
POST /submit   → handleSubmit() (CSRF確認→バリデーション→メール送信→サンクスへリダイレクト)
GET  /thanks   → showThanks()
```

PRGパターン（Post/Redirect/Get）を実装している。バリデーションエラーや入力値はセッションを使わず、有効期限30秒の短命クッキー（`postValues`, `validationErrors`）で受け渡す。

### ファイル構成の命名規則

- `_page.*.php` — HTMLページテンプレート（`render()` で呼び出す）
- `_mail.*.php` — メールテンプレート（`render()` で呼び出す）
- `libs/` — 自作クラス群

### 主要クラス・関数

**`libs/Routing.php`** — `Routing` クラス。`add(method, path, callback)` でルートを登録し、`dispatch()` で解決する。`new Routing('/simpleform')` のようにベースパスを渡す。

**`libs/Validation.php`** — `Validation` クラス。ルールはパイプ区切りで記述（`required|valid_email|matches[emailConf]`）。利用可能なルール: `required`, `permit_empty`, `valid_email`, `matches[field]`, `regex[pattern]`, `recaptcha_v3[action,score]`。

**`libs/Mailer.php`** — `Mailer` クラス。PHPの `mail()` 関数ラッパー。日本語件名は `mb_encode_mimeheader` でエンコード。添付ファイルは `addAttachment()` で追加し、送信ごとに `clearRecipients()` / `clearAttachments()` を呼ぶ。

**`libs/Helpers.php`** — グローバル関数群:
- `h($string)` — HTMLエスケープ（必ずユーザー入力の出力時に使う）
- `render($template, $vars)` — テンプレートをバッファリングしてレンダリング
- `loadEnv($path)` — `.env` ファイルの読み込み
- `csrf_token()` / `check_csrf()` — CSRF対策（Cookie二重送信パターン）
- `getPostValue($key, $escape)` — `filter_input` 経由でPOSTを取得
- `buildFormData($postKeys, $isHTML)` — メール用データ構築
- `formHiddenParams()` — 確認画面の hidden フィールド生成
- `showConfirmValue($key)` — 確認画面での値表示
- `clearSessionCookies()` — PRG用の短命クッキー（`postValues`, `validationErrors`）を削除。`showForm()` でクッキー読み取り後に明示的に呼ぶ

### 環境変数

`.env`（実体）と `.env.example`（テンプレート）を管理。`loadEnv()` で読み込み、`getenv()` でアクセス。

```
RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...
```

### セキュリティ実装

- **CSRF**: Cookie二重送信パターン（`_csrf_token` クッキー ↔ `_csrf` POSTパラメータ）
- **XSS**: `h()` でエスケープ。テンプレート内では必ず `h()` を通すこと
- **reCAPTCHA v3**: バリデーションルール `recaptcha_v3[action,score]` として実装。`RECAPTCHA_SECRET_KEY` 環境変数が必要

### フロントエンド

- Bootstrap 5.3（CDN）
- AjaxZip3（郵便番号→住所自動補完）
- Google reCAPTCHA v3（フォーム送信時にトークン取得 → hidden フィールドにセット）
