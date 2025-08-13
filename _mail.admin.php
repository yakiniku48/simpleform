メールフォームからの通知

お問い合わせ種別: <?= $formData['category'].PHP_EOL ?>
氏名: <?= $formData['lastName'].$formData['firstName'].PHP_EOL ?>
メールアドレス: <?= $formData['email'].PHP_EOL ?>
郵便番号: <?= $formData['postalCode'].PHP_EOL ?>
都道府県: <?= $formData['addressLevel1'].PHP_EOL ?>
住所: <?= $formData['address'].PHP_EOL ?>
性別: <?= $formData['gender'].PHP_EOL ?>
お問い合わせ内容:
<?= $formData['message'].PHP_EOL ?>