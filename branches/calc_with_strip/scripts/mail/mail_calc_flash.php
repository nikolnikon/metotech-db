<?php

$to = 'info@metotech.ru';

$text = iconv('utf-8', 'windows-1251', $_POST['text']);
$name = iconv('utf-8', 'windows-1251', $_POST['name']);
//$email = iconv('utf-8', 'windows-1251', $_POST['email']);
$subject = iconv('utf-8', 'windows-1251', $_POST['subject']);
$subject .= " (калькулятор flash)";

$message = "Имя: ".$name."\n\n".$text;

mail ($to, $subject, $message, "From: ".iconv("utf-8", "windows-1251", $_POST['name'])." (".$type.") "."<".$_POST['email'].">");

?>