<?php
    include("../hid_vars.php");
    $db_req = 'mysql:dbname=' . $database . ';host=' . $host;
    $db = new PDO($db_req, $user, $password,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    function res($status, $val){
        exit(json_encode(array('status' => $status, 'value' => $val), JSON_UNESCAPED_UNICODE));
    }
    
    if(!checkAdmin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])) res('error', "Вы не авторизованы");

    $id = $_POST['id'];
    if(!preg_match('/^[0-9]+$/', $id)) res('error', "Введите id");

    
    $dbf = $db->prepare("SELECT * FROM application WHERE id_app = ?");
    $dbf->execute([$id]);
    if($dbf->rowCount() != 0){
        $dbdel = $db->prepare("DELETE FROM application WHERE id_app = ?");
        $dbdel->execute([$id]);
        $dbdel = $db->prepare("DELETE FROM app_link_lang WHERE id_app = ?");
        ($dbdel->execute([$id])) ? res('success', "Форма удалена") : res('error', "Ошибка удаления");
    }
    else{
        res('error', "Форма не найдена");
    }
?>