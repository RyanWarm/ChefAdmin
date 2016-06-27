<?php
@require_once('Mail.php');

class MailPear {
    public function __construct() {
    }
    public function send($to, $subject, $body) {
        $from = '<no-reply@u2top.cn>';
        $host = 'ssl://smtp.exmail.qq.com';
        $username = 'no-reply@u2top.cn';
        $password = 'crane1121';

        $headers = array('From' => $from,
                         'To' => $to,
                         "MIME-Version"=> '1.0',
                         "Content-type" => "text/html; charset=utf8",
                         'Subject' => $subject);

        $smtp = @Mail::factory('smtp',
                              array('host' => $host,
                                    'port' => 465, // for ssl
                                    'auth' => true,
                                    'username' => $username,
                                    'password' => $password ) );
        
        $mail = @$smtp->send($to, $headers, $body);
        if (@PEAR::isError($mail)) {
            error_log("send email to " . $to . " failed: " . $mail->getMessage() );
            return false;
        } else {
            error_log("send email to " . $to . " success");
            return true;
        }
        
    }

}

?>