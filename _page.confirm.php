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
                <p class="text-start">下記の内容でフォームを送信します。問題がなければ送信ボタンで送信してください</p>
            </div>
            <div class="col-md-8 offset-md-2">
                <form method="POST" action="./submit" id="simpleForm" class="row g-3" novalidate>
                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    <input type="hidden" name="_csrf" value="<?= h(csrf_token()) ?>">
                    <?= formHiddenParams() ?>
                    <style>
                    .table>:not(caption)>*>* {
                        background-color: transparent;
                    }
                    .table th {
                        white-space: nowrap;;
                    }
                    </style>
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <?php foreach (validationRules() as $key => $rule): ?>
                                    <tr>
                                        <th scope="row"><?= h($rule['label']) ?></th>
                                        <td>
                                            <pre><?php showConfirmValue($key); ?></pre>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary" name="action" value="send" type="submit">送信する</button>
                        <button class="btn btn-secondary" name="action" value="back" type="submit">修正する</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
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
