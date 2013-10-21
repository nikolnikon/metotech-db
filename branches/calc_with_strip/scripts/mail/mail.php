#!/usr/local/bin/php
<?php
function rpHash($value) { 
    $hash = 5381; 
    $value = strtoupper($value); 
    for($i = 0; $i < strlen($value); $i++) { 
        $hash = (($hash << 5) + $hash) + ord(substr($value, $i)); 
    } 
    return $hash; 
} 
if (rpHash($_POST['captcha']) == $_POST['captchaHash']) {
	$to = "info@metotech.ru";
	switch ($_POST['form_type']) {
		case 'calc_mail':
			$type = "�����������";
			$message = "���: ".iconv("UTF-8", "CP1251", $_POST['name'])."\n\n".iconv("UTF-8", "CP1251", $_POST['text']);
			break;
		case 'art_mail':
			$type = "������";
			$message = "���: ".iconv("UTF-8", "CP1251", $_POST['name'])."\n\n".iconv("UTF-8", "CP1251", $_POST['text']);
			break;
		case 'mail':
			$type = "����";
			$message="�����: ".iconv("UTF-8", "CP1251", $_POST['company'])."\n"."�������: ".iconv("UTF-8", "CP1251", $_POST['phone'])."\n"."���: ".iconv("UTF-8", "CP1251", $_POST['name'])."\n\n".iconv("UTF-8", "CP1251", $_POST['text']);
			break;
	}

	if (mail($to, iconv("UTF-8", "CP1251", $_POST['subject'])." ($type)", $message, "From: ".iconv("UTF-8", "CP1251", $_POST['name'])." (".$type.") "."<".$_POST['email'].">" )) {
	   $resp['success'] = true;
	   $resp['message'] = iconv("CP1251", "UTF-8", "���� ��������� ������� ���������� � � ��������� ����� ����� �����������");
	}
	else {
	   $resp['success'] = false;
	   $resp['message'] = iconv("CP1251", "UTF-8", "�������� ��������� �� �������. ���������� ��������� ������� ����� ��������� �����");
	}
}
else {
	$resp['success'] = false;
	$resp['message'] = iconv("CP1251", "UTF-8", "������ �������� ���. �������� ��������� �� �������. ������� ���������� ��� � ��������� �������");
}
print json_encode($resp);
?>
