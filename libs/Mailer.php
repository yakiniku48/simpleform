<?php

class Mailer
{
    public $isHTML = false;
    private $from = '';
    private $fromName = '';
    private $to = [];
    private $cc = [];
    private $bcc = [];
    private $replyTo = '';
    private $subject = '';
    private $message = '';
    private $attachments = [];

    public function isHTML($isHTML = true)
    {
        $this->isHTML = (bool) $isHTML;
    }

    public function setFrom($address, $name = '')
    {
        $this->from = $address;
        $this->fromName = $name;
    }

    public function setTo($address)
    {
        if (is_array($address)) {
            $this->to = $address;
        } else {
            $this->to = [$address];
        }
    }

    public function setCC($address)
    {
        if (is_array($address)) {
            $this->cc = $address;
        } else {
            $this->cc = [$address];
        }
    }

    public function setBCC($address)
    {
        if (is_array($address)) {
            $this->bcc = $address;
        } else {
            $this->bcc = [$address];
        }
    }

    public function setReplyTo($address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $this->replyTo = $address;
        return true;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function addAttachment($filePath, $filename = null)
    {
        if (file_exists($filePath)) {
            if ($filename === null) {
                $filename = basename($filePath);
            }
            $this->attachments[] = [
                'path' => $filePath,
                'name' => $filename,
            ];
        }
    }

    public function clearRecipients()
    {
        $this->to = [];
        $this->cc = [];
        $this->bcc = [];
        $this->replyTo = '';
    }

    public function clearAttachments()
    {
        $this->attachments = [];
    }

    public function send()
    {
        if (empty($this->to)) {
            return false;
        }
        $to = implode(', ', $this->to);
        $headers = '';
        // From header (日本語対応)
        if ($this->from) {
            if ($this->fromName) {
                $encodedName = mb_encode_mimeheader($this->fromName, 'UTF-8');
                $headers .= "From: {$encodedName} <{$this->from}>\r\n";
            } else {
                $headers .= "From: {$this->from}\r\n";
            }
        }
        // Reply-To
        if ($this->replyTo) {
            $headers .= "Reply-To: {$this->replyTo}\r\n";
        }
        // CC
        if (!empty($this->cc)) {
            $headers .= "Cc: " . implode(', ', $this->cc) . "\r\n";
        }
        // BCC
        if (!empty($this->bcc)) {
            $headers .= "Bcc: " . implode(', ', $this->bcc) . "\r\n";
        }

        // 件名エンコード（日本語対応）
        $subject = mb_encode_mimeheader($this->subject, 'UTF-8');

        // 添付ファイルがある場合はmultipart/mixed
        if (!empty($this->attachments)) {
            $boundary = '----=_Part_' . md5(uniqid(mt_rand(), true));
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";

            $body = "--{$boundary}\r\n";
            if ($this->isHTML) {
                $body .= "Content-Type: text/html; charset=UTF-8\r\n";
            } else {
                $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }
            $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
            $body .= $this->message . "\r\n";

            foreach ($this->attachments as $attachment) {
                if (!is_readable($attachment['path'])) {
                    continue;
                }
                $fileContent = file_get_contents($attachment['path']);
                $fileContent = chunk_split(base64_encode($fileContent));
                $fileName = mb_encode_mimeheader($attachment['name'], 'UTF-8');
                $mimeType = self::getMimeType($attachment['path']);
                $body .= "--{$boundary}\r\n";
                $body .= "Content-Type: {$mimeType}; name=\"{$fileName}\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n";
                $body .= "Content-Disposition: attachment; filename=\"{$fileName}\"\r\n\r\n";
                $body .= $fileContent . "\r\n";
            }
            $body .= "--{$boundary}--\r\n";
        } else {
            // 添付なし
            if ($this->isHTML) {
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            } else {
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            }
            $body = $this->message;
        }

        // mail()の第5引数は使わない（安全性のため）
        return mail($to, $subject, $body, $headers);
    }

    private static function getMimeType($file)
    {
        if (function_exists('mime_content_type')) {
            return mime_content_type($file);
        }
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $map = [
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        return isset($map[$ext]) ? $map[$ext] : 'application/octet-stream';
    }
}