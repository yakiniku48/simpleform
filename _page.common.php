<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Simple Form</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://www.google.com/recaptcha/api.js?render=<?= getenv('RECAPTCHA_SITE_KEY') ?>"></script>
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
                <h2><?= $title ?></h2>
                <p><?= $message ?></p>
            </div>
        </main>
    </div>
</body>
</html>
