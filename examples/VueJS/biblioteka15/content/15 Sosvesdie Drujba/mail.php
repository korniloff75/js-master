<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<title>�������� �����</title>
</head>
<body>
<?php

function _mail ($from, $to, $subj, $what)
{
mail($to, $subj, $what, 
"From: $from
Reply-To: $from
Content-Type: text/plain; charset=windows-1251
Content-Transfer-Encoding: 8bit"
);
}



$name=$_POST["name"];
$email=$_POST["email"];
$mess=$_POST["mess"];
if (isset ($name))
{
$name = substr($name,0,20); //�� ����� ���� ����� 30 ��������
if (empty($name))
{
echo "<center><b>�� ������� ���!<p>";
echo "<a href=index.htm>��������� � ��������� ��������� �����.</a>";
exit;
}
}
else
{
$name = "�� �������";
}
if (isset ($email))
{
$email = substr($email,0,20); //�� ����� ���� ����� 20 ��������
if (empty($email))
{
echo "<center><b>�� ������ e-mail!<p>";
echo "<a href=index.htm>��������� � ��������� ��������� �����.</a>";
exit;
}
}
else
{
$email = "�� �������";
}
if (isset ($mess))
{
$mess = substr($mess,0,1000); //�� ����� ���� ����� 1000 ��������
if (empty($mess))
{
echo "<center><b>��������� �� ��������!<p>";
echo "<a href=index.htm>��������� � ��������� ��������� �����.</a>";
exit;
}
}
else
{
$mess = "�� �������";
}
$i = "�� �������";
if ($name == $i AND $email == $i AND $mess == $i)
{
echo "������! ������� �� ���� �������� ���������";
exit;
}
$to = "Rina.65@mail.ru";
$subject = "��������� � ������ �����";
$message = "��� ����������: $name, ����������� �����: $email, ���������: $mess, IP-�����:$REMOTE_ADDR";
_mail ("site@xopoco.ru", $to,$subject,$message);// or print "�� ���� ��������� ������!";
echo "<center><b>������� �� �������� ������ ���������<br><a href=index.htm>�������</a>, ����� ��������� �� ������� ��������";
exit;
?>
</body>
</html>
