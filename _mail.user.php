<h1>自動返信メール</h1>

<dl>
<dt>お問い合わせ種別</dt><dd><?= $formData['category'] ?></dd>
<dt>氏名</dt><dd><?= $formData['lastName'].$formData['firstName'] ?></dd>
<dt>メールアドレス</dt><dd><?= $formData['email'] ?></dd>
<dt>郵便番号</dt><dd><?= $formData['postalCode'] ?></dd>
<dt>都道府県</dt><dd><?= $formData['addressLevel1'] ?></dd>
<dt>住所</dt><dd><?= $formData['address'] ?></dd>
<dt>性別</dt><dd><?= $formData['gender'] ?></dd>
<dt>お問い合わせ内容</dt><dd><?= $formData['message'] ?></dd>
</dl>