# Simple Form

PHP製のシンプルなメールフォーム
NO フレームワーク & NO Composer

## 機能

- 入力 → 確認 → 送信
- バリデーション（必須、メール形式、一致確認など）
- 管理者・ユーザーへのメール送信
- CSRF対策（Cookie二重送信パターン）
- Google reCAPTCHA v3
- 郵便番号 → 住所の自動補完（AjaxZip3）

## 動作要件

- PHP 7.4 以上

## 環境変数

`.env.example` をコピーして `.env` を作成し、各値を設定する。

```sh
cp .env.example .env
```

| 変数名 | 説明 |
|---|---|
| `RECAPTCHA_SITE_KEY` | reCAPTCHA v3 サイトキー |
| `RECAPTCHA_SECRET_KEY` | reCAPTCHA v3 シークレットキー |

## ファイル構成

```
index.php              # エントリーポイント・ルーティング定義
_page.form.php         # 入力フォーム画面
_page.confirm.php      # 確認画面
_page.thanks.php       # 完了画面
_page.common.php       # 共通レイアウト
_mail.admin.php        # 管理者向けメールテンプレート
_mail.user.php         # ユーザー向けメールテンプレート
libs/
  Routing.php          # ルーティング
  Validation.php       # バリデーション
  Mailer.php           # メール送信
  Helpers.php          # グローバル関数群
  Autoload.php         # クラスオートロード
```
