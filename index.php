<?php

require_once __DIR__.'/libs/Autoload.php';
loadEnv(__DIR__.'/.env');

function validationRules() {
    static $rules = null;
    if ($rules !== null) {
        return $rules;
    }
    $rules = [
        'category' => [
            'label' => 'お問い合わせ種別',
            'rules' => 'required',
        ],
        'lastName' => [
            'label' => '姓',
            'rules' => 'required',
        ],
        'firstName' => [
            'label' => '名',
            'rules' => 'required',
        ],
        'postalCode' => [
            'label' => '郵便番号',
            'rules' => 'regex[/^[0-9]{3}-?[0-9]{4}$/]',
        ],
        'addressLevel1' => [
            'label' => '都道府県',
            'rules' => 'required',
        ],
        'address' => [
            'label' => '住所',
            'rules' => 'required',
        ],
        'gender' => [
            'label' => '性別',
            'rules' => 'permit_empty',
        ],
        'email' => [
            'label' => 'メールアドレス',
            'rules' => 'required|valid_email|matches[emailConf]',
        ],
        'emailConf' => [
            'label' => 'メールアドレス（確認）',
            'rules' => 'permit_empty',
        ],
        'message' => [
            'label' => 'お問い合わせ内容',
            'rules' => 'required',
        ],
        'g-recaptcha-response' => [
            'label' => 'reCAPTCHA認証',
            'rules' => 'required|recaptcha_v3[submit,0.5]',
        ],
    ];
    return $rules;
}

$routing = new Routing('/simpleform');

$routing->add('GET', '/', function () { showForm(); });
$routing->add('GET', '/form', function () { showForm(); });
$routing->add('POST', '/confirm', function () { showConfirm(); });
$routing->add('POST', '/submit', function () { handleSubmit(); });
$routing->add('GET', '/thanks', function () { showThanks(); });

$routing->dispatch();

function showForm() {
    $old = getOldFromCookie();
    $errors = getErrorsFromCookie();
    clearSessionCookies();
    echo render('_page.form.php', [
        'old' => $old,
        'errors' => $errors,
    ]);
    return;
}

function showConfirm() {
    check_csrf();

    $validation = new Validation();
    $validation->setRules(validationRules());

    if ($validation->run()) {
        echo render('_page.confirm.php');
        return;
    }

    redirect('./form', $validation->errors());
    return;
}

function handleSubmit() {
    check_csrf();

    $validation = new Validation();
    $validation->setRules(validationRules());

    if (filter_input(INPUT_POST, 'action') !== 'send' || ! $validation->run()) {
        redirect('./form', $validation->errors());
        return;
    }

    $mailer = new Mailer();
    $postKeys = array_keys(validationRules());

    // To Admin
    $mailer->isHTML(false);
    $mailer->setFrom('simpleform@mock-up.dev', 'Simple Form');
    $mailer->setTo('hello@mock-up.dev');
    // $mailer->setCC('hello+cc@mock-up.dev');
    // $mailer->setBCC('hello+bcc@mock-up.dev');
    $mailer->setReplyTo(getPostValue('email'));
    $mailer->setSubject('Simple Form - お問い合わせ');
    $formData = buildFormData($postKeys, $mailer->isHTML);
    $mailer->setMessage(render('_mail.admin.php', ['formData' => $formData]));
    $mailer->addAttachment(__DIR__.'/sample.pdf');
    $mailer->addAttachment(__DIR__.'/sample.zip');

    if (! $mailer->send()) {
        echo render('_page.common.php', [
            'title' => 'Something went wrong',
            'message' => 'メールの送信に失敗しました。時間をおいて再度お試しください',
        ]);
        return;
    }

    $mailer->clearRecipients();
    $mailer->clearAttachments();

    // To User
    $mailer->isHTML(true);
    $mailer->setFrom('simpleform@mock-up.dev', 'Simple Form');
    $mailer->setTo(getPostValue('email'));
    $mailer->setSubject('Simple Form - お問い合わせを受け付けました');
    $formData = buildFormData($postKeys, $mailer->isHTML);
    $mailer->setMessage(render('_mail.user.php', ['formData' => $formData]));
    if (! $mailer->send()) {
        // 管理者宛は送信済みのため、エラー画面で再送信させず受付完了として扱う
        error_log('simpleform: 自動返信メールの送信に失敗しました to='.getPostValue('email'));
    }

    redirect('./thanks');
}

function showThanks() {
    echo render('_page.thanks.php');
    return;
}
