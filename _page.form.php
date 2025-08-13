<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Simple Form</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
</head>
<body class="bg-body-tertiary">
    <div class="container-fluid">
        <main class="row py-5">
            <div class="col-md-8 offset-md-2 pb-5 text-center">
                <span class="h1 mb-4">💌</span>
                <h1 class="h1">Simple Form</h1>
                <p class="text-start">このダミーテキストは自由に改変することが出来ます。ダミーテキストはダミー文書やダミー文章とも呼ばれることがあります。カタカナ語が苦手な方は「組見本」と呼ぶとよいでしょう。ダミーテキストはダミー文書やダミー文章とも呼ばれることがあります。文章に特に深い意味はありません。主に書籍やウェブページなどのデザインを作成する時によく使われます。</p>
            </div>
            <div class="col-md-8 offset-md-2">
                <form method="POST" action="./confirm" id="simpleForm" class="row g-3" novalidate>
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <div class="col-12">
                        <label for="category" class="form-label">お問い合わせ種別 <em class="text-danger">*</em></label>
                        <?php
                        $choices = ['このサイトについて', 'このサイトの運営者について', 'その他'];
                        ?>
                        <?php foreach ($choices as $choice) : ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input <?php if (! empty($errors['category'])) echo 'is-invalid'; ?>" id="category:<?= $choice ?>" name="category[]" value="<?= $choice ?>" <?php if (is_array($old['category']) && in_array($choice, $old['category'])) echo 'checked'; ?>>
                                <label class="form-check-label" for="category:<?= $choice ?>"><?= $choice ?></label>
                                <?php if (! empty($errors['category']) && $choice == end($choices)) showError($errors['category']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-sm-6">
                        <label for="lastName" class="form-label">姓 <em class="text-danger">*</em></label>
                        <input type="text" class="form-control <?php if (! empty($errors['lastName'])) echo 'is-invalid'; ?>" id="lastName" name="lastName" placeholder="" value="<?= h($old['lastName']) ?>">
                        <?php if (! empty($errors['lastName'])) showError($errors['lastName']); ?>
                    </div>
                    <div class="col-sm-6">
                        <label for="firstName" class="form-label">名 <em class="text-danger">*</em></label>
                        <input type="text" class="form-control <?php if (! empty($errors['firstName'])) echo 'is-invalid'; ?>" id="firstName" name="firstName" placeholder="" value="<?= h($old['firstName']) ?>">
                        <?php if (! empty($errors['firstName'])) showError($errors['firstName']); ?>
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">メールアドレス <em class="text-danger">*</em></label>
                        <input type="email" class="form-control <?php if (! empty($errors['email'])) echo 'is-invalid'; ?>" id="email" name="email" placeholder="you@example.com" value="<?= h($old['email']) ?>">
                        <?php if (! empty($errors['email'])) showError($errors['email']); ?>
                    </div>
                    <div class="col-12">
                        <div class="form-text">確認のため、もう一度入力してください</div>
                        <input type="email" class="form-control <?php if (! empty($errors['emailConf'])) echo 'is-invalid'; ?>" id="emailConf" name="emailConf" placeholder="you@example.com" value="<?= h($old['emailConf']) ?>">
                        <?php if (! empty($errors['emailConf'])) showError($errors['emailConf']); ?>
                    </div>
                    <div class="col-12">
                        <label for="postalCode" class="form-label">郵便番号</label>
                        <input type="text" class="form-control <?php if (! empty($errors['postalCode'])) echo 'is-invalid'; ?>" id="postalCode" name="postalCode" placeholder="" value="<?= h($old['postalCode']) ?>">
                        <?php if (! empty($errors['postalCode'])) showError($errors['postalCode']); ?>
                    </div>
                    <div class="col-12">
                        <label for="addressLevel1" class="form-label">都道府県 <em class="text-danger">*</em></label>
                        <select id="addressLevel1" name="addressLevel1" class="form-select <?php if (! empty($errors['addressLevel1'])) echo 'is-invalid'; ?>">
                            <option value="">選択してください</option>
                            <?php
                            $choices = ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県', '山梨県', '新潟県', '富山県', '石川県', '福井県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'];
                            ?>
                            <?php foreach ($choices as $choice) : ?>
                                <option value="<?= $choice ?>" <?php if ($choice === $old['addressLevel1']) echo 'selected'; ?>><?= $choice ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (! empty($errors['addressLevel1'])) showError($errors['addressLevel1']); ?>
                    </div>
                    <div class="col-12">
                        <label for="address" class="form-label">住所 <em class="text-danger">*</em></label>
                        <input type="text" class="form-control <?php if (! empty($errors['address'])) echo 'is-invalid'; ?>" id="address" name="address" placeholder="" value="<?php echo h($old['address']); ?>">
                        <?php if (! empty($errors['address'])) showError($errors['address']); ?>
                    </div>
                    <div class="col-12">
                        <label for="gender" class="form-label">性別</label>
                        <?php
                        $choices = ['女性', '男性', 'その他'];
                        ?>
                        <?php foreach ($choices as $choice) : ?>
                            <div class="form-check">
                                <input type="radio" class="form-check-input <?php if (! empty($errors['gender'])) echo 'is-invalid'; ?>" id="gender:<?= $choice ?>" name="gender" value="<?= $choice ?>" <?php if ($choice === $old['gender']) echo 'checked'; ?>>
                                <label class="form-check-label" for="gender:<?= $choice ?>"><?= $choice ?></label>
                                <?php if (! empty($errors['gender']) && $choice == end($choices)) showError($errors['gender']); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-12">
                        <label for="message" class="form-label">お問い合わせ内容 <em class="text-danger">*</em></label>
                        <textarea class="form-control <?php if (! empty($errors['message'])) echo 'is-invalid'; ?>" id="message" name="message" rows="5"><?= h($old['message']) ?></textarea>
                        <?php if (! empty($errors['message'])) showError($errors['message']); ?>
                    </div>

                    <hr class="my-4">
                    <?php
                    if (!empty($errors['g-recaptcha-response'])) {
                        echo '<div class="is-invalid"></div>';
                        showError($errors['g-recaptcha-response']);
                    }
                    ?>
                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">次ページで入力内容を確認</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const postalInput = document.getElementById('postalCode');
        const prefSelect = document.getElementById('addressLevel1');
        const addressInput = document.getElementById('address');
        if (postalInput && prefSelect && addressInput) {
            postalInput.addEventListener('keyup', function () {
                AjaxZip3.zip2addr(this, '', 'addressLevel1', 'address');
            });
        }
    });
    </script>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= getenv('RECAPTCHA_SITE_KEY') ?>"></script>
    <script>
    grecaptcha.ready(function() {
        document.getElementById('simpleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            grecaptcha.execute('<?= getenv('RECAPTCHA_SITE_KEY') ?>', {action: 'submit'}).then(function(token) {
                document.getElementById('g-recaptcha-response').value = token;
                e.target.submit();
            });
        });
    });
    </script>
</body>
</html>
