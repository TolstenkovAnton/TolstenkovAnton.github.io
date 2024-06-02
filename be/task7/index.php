<?php
  header('Content-Type: text/html; charset=UTF-8');
  if(strpos($_SERVER['REQUEST_URI'], 'index.php') === false){
    header('Location: index.php');
    exit();
  }

  include("../hid_vars.php");
  
  $db_req = 'mysql:dbname=' . $database . ';host=' . $host;
  $db = new PDO($db_req, $user, $password,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $log = isset($_SESSION['login']);
  $adminLog = checkAdmin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
  $uid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
  $getUid = isset($_GET['uid']) ? checkInput($_GET['uid']) : '';

  if($adminLog){
    if(preg_match('/^[0-9]+$/', $getUid)){
      $uid = $getUid;
      $log = true;
    }
  }
  
  function del_cook($cook, $vals = 0){
    setcookie($cook.'_error', '', 100000);
    if($vals == 1) setcookie($cook.'_value', '', 100000);
    if($vals == 2) setcookie($cook, '', 100000);
  }
  function del_cook_all($p = 0){
    del_cook('fio', $p);
    del_cook('telephone', $p);
    del_cook('email', $p);
    del_cook('bday', $p);
    del_cook('sex', $p);
    del_cook('langs', $p);
    del_cook('biography', $p);
    del_cook('contract', $p);
  }

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(($adminLog && isset($getUid)) || !$adminLog){
      $cookAdmin = (isset($_COOKIE['admin_value']) ? $_COOKIE['admin_value'] : '');
      if($cookAdmin == '1'){
        del_cook_all(1);
        del_cook('admin', 1);
      }
    }
    
    $csrf_error = (isset($_COOKIE['csrf_error']) ? checkInput($_COOKIE['csrf_error']) : '');
    $fio = (isset($_COOKIE['fio_error']) ? checkInput($_COOKIE['fio_error']) : '');
    $telephone = (isset($_COOKIE['telephone_error']) ? checkInput($_COOKIE['telephone_error']) : '');
    $email = (isset($_COOKIE['email_error']) ? checkInput($_COOKIE['email_error']) : '');
    $bday = (isset($_COOKIE['bday_error']) ? checkInput($_COOKIE['bday_error']) : '');
    $sex = (isset($_COOKIE['sex_error']) ? checkInput($_COOKIE['sex_error']) : '');
    $langs = (isset($_COOKIE['langs_error']) ? checkInput($_COOKIE['langs_error']) : '');
    $biography = (isset($_COOKIE['biography_error']) ? checkInput($_COOKIE['biography_error']) : '');
    $contract = (isset($_COOKIE['contract_error']) ? checkInput($_COOKIE['contract_error']) : '');

    $errors = array();
    $messages = array();
    $values = array();
    $error = true;
    
    function setVal($enName, $param){
      global $values;
      $values[$enName] = empty($param) ? '' : strip_tags($param);
    }
    
    function val_empty($enName, $val){
      global $errors, $messages, $error, $values;
      if($error) 
        $error = empty($_COOKIE[$enName.'_error']);

      $errors[$enName] = isset($_COOKIE[$enName.'_error']);
      $messages[$enName] = "<div class='messageError'>$val</div>";
      $values[$enName] = empty($_COOKIE[$enName.'_value']) ? '' : checkInput($_COOKIE[$enName.'_value']);
      del_cook($enName);
      return;
    }
    
    if (isset($_COOKIE['csrf_error'])) {
      $messages['error'] = 'Не соответствие CSRF токена';
      del_cook('csrf');
    }
    if (isset($_COOKIE['save'])) {
      del_cook('save', 2);
      del_cook('login', 2);
      del_cook('password', 2);
      $messages['success'] = (!$log) ? 'Спасибо, данные сохранены' : 'Данные изменены';
      if (isset($_COOKIE['password'])) {
        $messages['info'] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
          и паролем <strong>%s</strong> для изменения данных.',
          checkInput($_COOKIE['login']),
          checkInput($_COOKIE['password']));
      }
    }
    
    val_empty('fio', $fio);
    val_empty('telephone', $telephone);
    val_empty('email', $email);
    val_empty('bday', $bday);
    val_empty('sex', $sex);
    val_empty('langs', $langs);
    val_empty('biography', $biography);
    val_empty('contract', $contract);

    $langssa = explode(',', $values['langs']);
    
    if ($error && $log) {
      try {
        $dbFD = $db->prepare("SELECT * FROM application WHERE user_id = ?");
        $dbFD->execute([$uid]);
        if($dbFD->rowCount() > 0){
          $fet = $dbFD->fetchAll(PDO::FETCH_ASSOC)[0];
          $form_id = $fet['id_app'];
          $_SESSION['form_id'] = $form_id;
          $dbL = $db->prepare("SELECT l.name_prog_lang FROM app_link_lang f
            LEFT JOIN prog_lang l ON l.id_prog_lang = f.id_prog_lang
            WHERE f.id_app = ?");
          $dbL->execute([$form_id]);
          $langssa = [];
          foreach($dbL->fetchAll(PDO::FETCH_ASSOC) as $item){
            $langssa[] = $item['name_prog_lang'];
          }
          setVal('fio', $fet['fio']);
          setVal('telephone', $fet['telephone']);
          setVal('email', $fet['email']);
          setVal('bday', $fet['bday']);
          setVal('sex', $fet['sex']);
          setVal('langs', $langs);
          setVal('biography', $fet['biography']);
          setVal('contract', $fet['contract']);
        }
        else{
          unset($_SESSION['user_id']);
          $log = false;
          unset($uid);
          $messages['error'] = 'Пользователь был удален';
          user_exit();
        }
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
    }
    
    include('body.php');
  }
  else{ //POST
    $csrf_tokens = (isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '');
    $fio = (isset($_POST['fio']) ? $_POST['fio'] : '');
    $telephone = (isset($_POST['telephone']) ? $_POST['telephone'] : '');
    $email = (isset($_POST['email']) ? $_POST['email'] : '');
    $bday = (isset($_POST['bday']) ? $_POST['bday'] : '');
    $sex = (isset($_POST['sex']) ? $_POST['sex'] : '');
    $langs = (isset($_POST['langs']) ? $_POST['langs'] : '');
    $biography = (isset($_POST['biography']) ? $_POST['biography'] : '');
    $contract = (isset($_POST['contract']) ? $_POST['contract'] : '');

    if($_SESSION['csrf_token'] != $csrf_tokens){
      set_cook('csrf_error', '1');
      header('Location: index.php'.(($getUid != NULL) ? '?uid='.$uid : ''));
      exit();
    }

    if(isset($_POST['logout_form'])){
      if($adminLog && empty($_SESSION['login'])){
        header('Location: admin.php');
      }
      else{
        user_exit();
      }
      exit();
    }
    
    $telephone1 = preg_replace('/\D/', '', $telephone);

    function val_empty($cook, $comment, $usl){
      global $error;
      $res = false;
      $setVal = $_POST[$cook];
      if ($usl) {
        set_cook($cook.'_error', $comment);
        $error = true;
        $res = true;
      }
      
      if($cook == 'langs'){
        global $langs;
        $setVal = ($langs != '') ? implode(",", $langs) : '';
      }
      
      set_cook($cook.'_value', $setVal, 60);
      return $res;
    }
    
    if(!val_empty('fio', 'Заполните поле', empty($fio))){
      if(!val_empty('fio', 'Длина поля > 255 символов', strlen($fio) > 255)){
        val_empty('fio', 'Поле не соответствует требованиям: <i>Фамилия Имя (Отчество)</i>, кириллицей', !preg_match('/^([а-яёА-ЯЁ]+-?[а-яёА-ЯЁ]+)( [а-яёА-ЯЁ]+-?[а-яёА-ЯЁ]+){1,2}$/Diu', $fio));
      }
    }
    if(!val_empty('telephone', 'Заполните поле', empty($telephone))){
      if(!val_empty('telephone', 'Длина поля некорректна', strlen($telephone) != 11)){
        val_empty('telephone', 'Поле должен содержать только цифры', ($telephone != $telephone1));
      }
    }
    if(!val_empty('email', 'Заполните поле', empty($email))){
      if(!val_empty('email', 'Длина поля > 255 символов', strlen($email) > 255)){
        val_empty('email', 'Поле не соответствует требованию example@mail.ru', !preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/', $email));
      }
    }
    if(!val_empty('bday', "Выберите дату рождения", empty($bday))){
      val_empty('bday', "Неверно введена дата рождения, дата больше настоящей", (strtotime("now") < strtotime($bday)));
    }
    val_empty('sex', "Выберите пол", (empty($sex) || !preg_match('/^(male|female)$/', $sex)));
    if(!val_empty('langs', "Выберите хотя бы один язык", empty($langs))){
      try {
        $inQuery = implode(',', array_fill(0, count($langs), '?'));
        $dbLangs = $db->prepare("SELECT id_prog_lang, name_prog_lang FROM prog_lang WHERE name_prog_lang IN ($inQuery)");
        foreach ($langs as $key => $value) {
          $dbLangs->bindValue(($key+1), $value);
        }
        $dbLangs->execute();
        $languages = $dbLangs->fetchAll(PDO::FETCH_ASSOC);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
      
      val_empty('langs', 'Неверно выбраны языки', $dbLangs->rowCount() != count($langs));
    }
    if(!val_empty('biography', 'Заполните поле', empty($biography))){
      val_empty('biography', 'Длина текста > 65 535 символов', strlen($biography) > 65535);
    }
    val_empty('contract', "Ознакомьтесь с контрактом", empty($contract));
    
    if ($error) {
      header('Location: index.php'.(($getUid != NULL) ? '?uid='.$uid : ''));
      exit();
    }
    else {
      del_cook_all();
    }
    
    if ($log) {
      
      $stmt = $db->prepare("UPDATE application SET fio = ?, telephone = ?, email = ?, bday = ?, sex = ?, biography = ? WHERE user_id = ?");
      $stmt->execute([$fio, $telephone, $email, $bday, $sex, $biography, $uid]);
      
      $stmt = $db->prepare("DELETE FROM app_link_lang WHERE id_app = ?");
      $stmt->execute([$_SESSION['form_id']]);
      
      $stmt1 = $db->prepare("INSERT INTO app_link_lang (id_app, id_prog_lang) VALUES (?, ?)");
      foreach($languages as $row){
        $stmt1->execute([$_SESSION['form_id'], $row['id_prog_lang']]);
      }
      if($adminLog) 
        set_cook('admin_value', '1', 60);
    }
    else 
    {
      $login = substr(uniqid(), 0, 4).rand(10, 100);
      $password = rand(100, 1000).substr(uniqid(), 4, 10);
      setcookie('login', $login);
      setcookie('password', $password);
      $mpassword = md5($password);
      try {
        $stmt = $db->prepare("INSERT INTO users (login, password) VALUES (?, ?)");
        $stmt->execute([$login, $mpassword]);
        $user_id = $db->lastInsertId();
        
        $stmt = $db->prepare("INSERT INTO application (user_id, fio, telephone, email, bday, sex, biography) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $fio, $telephone, $email, $bday, $sex, $biography]);
        $fid = $db->lastInsertId();
        $stmt1 = $db->prepare("INSERT INTO app_link_lang (id_app, id_prog_lang) VALUES (?, ?)");
        foreach($languages as $row){
            $stmt1->execute([$fid, $row['id_prog_lang']]);
        }
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
      
      set_cook('fio_value', $fio, 365);
      set_cook('telephone_value', $telephone, 365);
      set_cook('email_value', $email, 365);
      set_cook('bday_value', $bday, 365);
      set_cook('sex_value', $sex, 365);
      set_cook('like_value', $like, 365);
      set_cook('biography_value', $biography, 365);
      set_cook('contract_value', $contract, 365);
    }
    // Сохраняем куку с признаком успешного сохранения.
    setcookie('save', '1');
  
    // Делаем перенаправление.
    header('Location: index.php');
  }
?>
