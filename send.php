<?php
// Файлы phpmailer
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

// Переменные, которые отправляет пользователь

if (isset($_POST['name']))
    $name = $_POST['name'];
else
    $name = '';
if (isset($_POST['email']))
    $email = $_POST['email'];
else
    $email = '';
if (isset($_POST['message']))
    $message = $_POST['message'];
else
    $message = 'Lorem ipsum dolor sit amet.';
if (isset($_POST['file'])) {
    $file = $_POST['file'];
    $rfile = "Файл прикреплён.";
} else
    $rfile = "Файл не прикреплён.";

if ((!isset($_POST['name'])) && (!isset($_POST['email'])) && (!isset($_POST['message']))) 
	die("Входные данные пусты. Скрипт завершается.");

// Формирование самого письма
$title = "Заголовок письма";
$body = "
<h2>Новое письмо</h2>
<b>Имя:</b> $name<br>
<b>Почта:</b> $email<br><br>
<b>Сообщение:</b><br>$message
";

// Настройки PHPMailer
$mail = new PHPMailer\PHPMailer\PHPMailer();
try {
    $mail->isSMTP();
    $mail->CharSet = "UTF-8";
    $mail->SMTPAuth   = true;
    //$mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

    // Настройки вашей почты
    $mail->Host       = 'smtp.yandex.ru'; // SMTP сервера вашей почты
    $mail->Username   = 'your_login'; // Логин на почте
    $mail->Password   = 'password'; // Пароль на почте
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;
    $mail->setFrom('mail@yandex.ru', 'Имя отправителя'); // Адрес самой почты и имя отправителя

    // Получатель письма
    $mail->addAddress('youremail@yandex.ru');
    $mail->addAddress('youremail@gmail.com'); // Ещё один, если нужен

    // Прикрипление файлов к письму
    if (!empty($file['name'][0])) {
        for ($ct = 0; $ct < count($file['tmp_name']); $ct++) {
            $uploadfile = tempnam(sys_get_temp_dir(), sha1($file['name'][$ct]));
            $filename = $file['name'][$ct];
            if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
                $mail->addAttachment($uploadfile, $filename);
                $rfile = "Файл $filename прикреплён.";
            } else {
                $rfile = "Не удалось прикрепить файл $filename";
            }
        }
    }
// Отправка сообщения
    $mail->isHTML(true);
    $mail->Subject = $title;
    $mail->Body = $body;

// Проверяем отравленность сообщения
    if ($mail->send()) {
        $result = "success";
        $status = "Сообщение успешно отправлено.";
    } else {
        $result = "error";
        $status = "Сообщение не было отправлено.";
    }

} catch (Exception $e) {
    $result = "error";
    $status = "Сообщение не было отправлено. Причина ошибки: {$mail->ErrorInfo}";
}

// Отображение результата
echo "result: " . $result . "<br>resultfile: " . $rfile . "<br>status: " . $status;