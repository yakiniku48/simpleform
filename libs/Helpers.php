<?php

/**
 * Common Functions
 */
function h($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function render($template, $vars = [])
{
    extract($vars, EXTR_SKIP);
    if (! file_exists($template)) {
        throw new Exception('テンプレートが見つかりません: '.$template);
    }

    ob_start();
    include $template;
    return ob_get_clean();
}

/**
 * Load environment variables from .env file
 */
function loadEnv($path)
{
    if (! file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'");
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

/**
 * Emulate Sessions Helpers
 */
define('COOKIE_POST_VALUES', 'postValues');
define('COOKIE_VALIDATION_ERRORS', 'validationErrors');

function clearSessionCookies()
{
    setcookie(COOKIE_POST_VALUES, '', time() - 3600, '/', '', true, true);
    setcookie(COOKIE_VALIDATION_ERRORS, '', time() - 3600, '/', '', true, true);
}

function redirect($path, $errors = [])
{
    setcookie(COOKIE_POST_VALUES, json_encode($_POST, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), time() + 30, '/', '', true, true);
    setcookie(COOKIE_VALIDATION_ERRORS, json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), time() + 30, '/', '', true, true);
    header('Location: ' . $path);
    exit();
}

function getOldFromCookie()
{
    $cookie = [];
    if ($inputCookie = filter_input(INPUT_COOKIE, COOKIE_POST_VALUES)) {
        $cookie = json_decode($inputCookie, true);
    }

    $old = [];
    foreach (array_keys(validationRules()) as $key) {
        $old[$key] = (! empty($cookie[$key])) ? $cookie[$key] : false;
    }

    return $old;
}

function getErrorsFromCookie()
{
    $errors = [];
    if ($validationErrors = filter_input(INPUT_COOKIE, COOKIE_VALIDATION_ERRORS)) {
        $errors = json_decode($validationErrors, true);
    }

    return $errors;
}

/**
 * Form Helpers
 */
function getPostValue($key, $escape = false) {
    $value = filter_input(INPUT_POST, $key, FILTER_DEFAULT, ['flags' => FILTER_REQUIRE_ARRAY]);
    if (empty($value)) {
        $value = filter_input(INPUT_POST, $key);
    }
    if (empty($value)) {
        return;
    }
    if ($escape) {
        if (is_array($value)) {
            return array_map('h', $value);
        }
        return h($value);
    }
    return $value;
}

function buildFormData($postKeys, $isHTML = false) {
    $formData = [];
    foreach ($postKeys as $key) {
        $values = getPostValue($key, $isHTML);
        if (is_array($values)) {
            $formData[$key] = implode(', ', $values);
        } elseif (! empty($values)) {
            $formData[$key] = $isHTML ? nl2br($values) : $values;
        } else {
            $formData[$key] = '';
        }
    }
    return $formData;
}

function showError($error)
{
    if (is_array($error)) {
        foreach ($error as $msg) {
            echo '<div class="invalid-feedback">' . $msg . '</div>';
        }
    } else {
        echo '<div class="invalid-feedback">' . $error . '</div>';
    }
}

function csrf_token() {
    if (empty($_COOKIE['_csrf_token'])) {
        $token = bin2hex(random_bytes(32));
        setcookie('_csrf_token', $token, [
            'expires'  => 0,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        return $token;
    }
    return $_COOKIE['_csrf_token'];
}

function check_csrf()
{
    $cookie = filter_input(INPUT_COOKIE, '_csrf_token');
    $post = filter_input(INPUT_POST, '_csrf');
    if (! $cookie || ! $post || $cookie !== $post) {
        setcookie('_csrf_token', '', time() - 3600, '/', '', true, true);
        http_response_code(400);
        echo render('_page.common.php', [
            'title' => 'Something went wrong',
            'message' => '不正なリクエストです',
        ]);
        exit();
    }
}

function formHiddenParams()
{
    if (empty($_POST)) return;

    $hiddens = '';
    $postKeys = array_keys(validationRules());
    foreach ($postKeys as $key) {
        $values = getPostValue($key, true);
        if (is_array($values)) {
            foreach ($values as $value) {
                $hiddens .= '<input type="hidden" name="'.$key.'[]" value="'.$value.'">';
            }
        } elseif ($values !== null) {
            $hiddens .= '<input type="hidden" name="'.$key.'" value="'.$values.'">';
        }
    }
    return $hiddens;

}

function showConfirmValue($key) {
    $values = getPostValue($key, true);
    if (is_array($values)) {
        echo implode(', ', $values);
    } elseif ($values !== null && $values !== false) {
        echo $values;
    } else {
        echo '-';
    }
}
