<?php

class Validation
{
    protected $rules = [];
    protected $errors = [];

    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    public function run($input = null)
    {
        $this->errors = [];
        if ($input === null) {
            $input = $_POST;
        }

        foreach ($this->rules as $name => $ruleSet) {
            $label = $ruleSet['label'] ?? $name;
            $rules = explode('|', $ruleSet['rules']);
            $value = $input[$name] ?? '';

            foreach ($rules as $rule) {
                if (preg_match('/^(\w+)(?:\[(.+)\])?$/', $rule, $m)) {
                    $ruleName = $m[1];
                    $param = $m[2] ?? null;
                    if (method_exists($this, $ruleName)) {
                        $this->{$ruleName}($name, $label, $value, $input, $param);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    protected function required($name, $label, $value, $input, $param = null)
    {
        if ($value === '' || $value === null) {
            $this->addError($name, "<em>“{$label}”</em> は必須です。");
        }
    }

    protected function permit_empty($name, $label, $value, $input, $param = null)
    {
        // 何もしない
    }

    protected function valid_email($name, $label, $value, $input, $param = null)
    {
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($name, "<em>“{$label}”</em> の形式が正しくありません。");
        }
    }

    protected function matches($name, $label, $value, $input, $otherField)
    {
        if ($otherField && $value !== ($input[$otherField] ?? '')) {
            $this->addError($name, "<em>“{$label}”</em> が一致しません。");
        }
    }

    protected function regex($name, $label, $value, $input, $pattern)
    {
        if ($pattern === null || $pattern === '') {
            return;
        }
        if ($value !== '' && !preg_match($pattern, $value)) {
            $this->addError($name, "<em>“{$label}”</em> の形式が正しくありません。");
        }
    }

    protected function recaptcha_v3($name, $label, $value, $input, $actionScore)
    {
        $secret = getenv('RECAPTCHA_SECRET_KEY');
        $url = 'https://www.google.com/recaptcha/api/siteverify';

        list($action, $threshold) = explode(',', $actionScore);

        $data = [
            'secret' => $secret,
            'response' => $value,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 5,
            ],
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            $this->addError($name, "<em>“{$label}”</em> の検証に失敗しました。時間をおいて再度お試しください。");
            return;
        }

        $resultData = json_decode($result, true);
        $verifyRecaptchaV3 = (
            !empty($resultData['success']) &&
            $resultData['action'] === $action &&
            $resultData['score'] >= $threshold
        );

        if (! $verifyRecaptchaV3) {
            $this->addError($name, "<em>“{$label}”</em> に失敗しました。");
        }
    }

    protected function addError($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    public function errors()
    {
        return $this->errors;
    }
}

