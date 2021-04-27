<?php
require_once __DIR__.'/_Comments.class.php';

$Comments = $Comments ?? new _Comments;

// *Controller
if(
	array_key_exists('act',$_REQUEST)
	&& $_REQUEST['act'] === 'comments'
) foreach($_REQUEST as $cmd=>&$val){
		$method= "c_$cmd";

	if(method_exists($Comments, $method)){
		if(is_string($val)) $val= filter_var($val, FILTER_SANITIZE_STRING);
		tolog(__METHOD__,null,['$cmd'=>$cmd, '$val'=>$val]);
		$Comments->{$method}($val);
	}
}

tolog(['$method'=>$method, 's_method'=>$_REQUEST['s_method'] ?? null]);

if(empty($method)) $Comments->Render();


//* Сохранение, редактирование и удаление комментариев по AJAX-запросу

/* $s_method = $_REQUEST['s_method'] ?? null;

switch($s_method) {
	# Добавляем новый коммент
	case 'write': $Comments->write(); break;
	# Выводим форму редактирования коммента
	case 'edit_comm': $Comments->Edit_Comm(); break;
	# Сохраняем отредактированный коммент
	case 'save_edit':
		$Comments->Save_Edit();
		break;
	# Удаляем коммент
	case 'del': $Comments->Del_Comm(); break;
	# включение/отключение комментариев
	case 'enable_comm': $Comments->Enabled_Comm($_POST['enable_comm']); break;
	default :
	# Выводим существующие комменты
	if (isset($Comments) && (!$Comments->check_no_comm($Comments->p_name) || is_adm())) {
		// var_dump(\ADMIN);
		$Comments->Render();
	}
} */

