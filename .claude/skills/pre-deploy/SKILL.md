---
name: pre-deploy
description: FTPデプロイ前の安全確認チェックを実行する
disable-model-invocation: true
---

デプロイ前に以下を順番に確認してください。

## 1. PHP構文チェック

```bash
find . -name "*.php" -not -path "./.git/*" | xargs php -l
```

エラーがあれば修正してからデプロイしてください。

## 2. コミット漏れ確認

```bash
git status
```

未コミットの変更がある場合はコミットまたは意図的な除外かを確認してください。

## 3. .env の設定確認

`.env` を開いて以下を確認してください。

- `RECAPTCHA_SECRET_KEY` が本番用のキーになっているか
- `RECAPTCHA_SITE_KEY` が本番ドメイン用のキーになっているか

## 4. 添付ファイルの確認

`index.php` の `handleSubmit()` 内で添付している以下のファイルが本番用に差し替えられているか確認してください。

- `sample.pdf`
- `sample.zip`

差し替え不要な場合は添付コードを削除してください。

## 5. 送信先メールアドレスの確認

`index.php` の `handleSubmit()` 内の以下を確認してください。

- `$mailer->setTo(...)` が本番の送信先になっているか
- `$mailer->setFrom(...)` の差出人ドメインが本番ドメインになっているか
