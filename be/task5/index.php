<?php
  header('Content-Type: text/html; charset=UTF-8');
  session_start();
  $log = !empty($_SESSION['login']);
  
  function del_cook($cook, $vals = 0){
    setcookie($cook.'_error', '', 100000);
    if($vals) setcookie($cook.'_value', '', 100000);
  }
  
  include("../hid_vars.php");
  $db_req = 'mysql:dbname=' . $database . ';host=' . $host;
  $db = new PDO($db_req, $user, $password,
  [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $fio = (!empty($_COOKIE['fio_error']) ? $_COOKIE['fio_error'] : '');
    $telephone = (!empty($_COOKIE['telephone_error']) ? $_COOKIE['telephone_error'] : '');
    $email = (!empty($_COOKIE['email_error']) ? $_COOKIE['email_error'] : '');
    $bday = (!empty($_COOKIE['bday_error']) ? $_COOKIE['bday_error'] : '');
    $sex = (!empty($_COOKIE['sex_error']) ? $_COOKIE['sex_error'] : '');
    $langs = (!empty($_COOKIE['langs_error']) ? $_COOKIE['langs_error'] : '');
    $biography = (!empty($_COOKIE['biography_error']) ? $_COOKIE['biography_error'] : '');
    $contract = (!empty($_COOKIE['contract_error']) ? $_COOKIE['contract_error'] : '');

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

      $errors[$enName] = !empty($_COOKIE[$enName.'_error']);
      $messages[$enName] = "<div class='messageError'>$val</div>";
      $values[$enName] = empty($_COOKIE[$enName.'_value']) ? '' : $_COOKIE[$enName.'_value'];
      del_cook($enName);
      return;
    }
    
    if (!empty($_COOKIE['save'])) {
      setcookie('save', '', 100000);
      setcookie('login', '', 100000);
      setcookie('password', '', 100000);
      $messages['success'] = 'Спасибо, результаты сохранены.';
      if (!empty($_COOKIE['password'])) {
        $messages['info'] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
          и паролем <strong>%s</strong> для изменения данных.',
          strip_tags($_COOKIE['login']),
          strip_tags($_COOKIE['password']));
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
    
    if ($error && !empty($_SESSION['login'])) {
      try {
        $dbFD = $db->prepare("SELECT * FROM application WHERE user_id = ?");
        $dbFD->execute([$_SESSION['user_id']]);
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
        setVal('bday', date("Y-m-d", intval($fet['bday'])));
        setVal('sex', $fet['sex']);
        setVal('langs', $like_lang);
        setVal('biography', $fet['biography']);
        setVal('contract', $fet['contract']);
      }
      catch(PDOException $e){
        print('Error : ' . $e->getMessage());
        exit();
      }
    }
    
    include('body.php');
  }
  else{ //POST
    $fio = (!empty($_POST['fio']) ? $_POST['fio'] : '');
    $telephone = (!empty($_POST['telephone']) ? $_POST['telephone'] : '');
    $email = (!empty($_POST['email']) ? $_POST['email'] : '');
    $bday = (!empty($_POST['bday']) ? $_POST['bday'] : '');
    $sex = (!empty($_POST['sex']) ? $_POST['sex'] : '');
    $langs = (!empty($_POST['langs']) ? $_POST['langs'] : '');
    $biography = (!empty($_POST['biography']) ? $_POST['biography'] : '');
    $contract = (!empty($_POST['contract']) ? $_POST['contract'] : '');
    
    if(isset($_POST['logout_form'])){
      del_cook('fio', 1);
      del_cook('telephone', 1);
      del_cook('email', 1);
      del_cook('bday', 1);
      del_cook('sex', 1);
      del_cook('langs', 1);
      del_cook('biography', 1);
      del_cook('contract', 1);
      session_destroy();
      header('Location: ./');
      exit();
    }

    $telephone1 = preg_replace('/\D/', '', $telephone);

    function val_empty($cook, $comment, $usl){
      global $error;
      $res = false;
      $setVal = $_POST[$cook];
      if ($usl) {
        setcookie($cook.'_error', $comment, time() + 24 * 60 * 60); //сохраняем на сутки
        $error = true;
        $res = true;
      }
      
      if($cook == 'langs'){
        global $langs;
        $setVal = ($langs != '') ? implode(",", $langs) : '';
      }
      
      setcookie($cook.'_value', $setVal, time() + 30 * 24 * 60 * 60); //сохраняем на месяц
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
      // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
      header('Location: index.php');
      exit();
    }
    else {
      // Удаляем Cookies с признаками ошибок.
      del_cook('fio');
      del_cook('telephone');
      del_cook('email');
      del_cook('bday');
      del_cook('sex');
      del_cook('langs');
      del_cook('biography');
      del_cook('contract');
    }
    
    if ($log) {
      
      $stmt = $db->prepare("UPDATE application SET fio = ?, telephone = ?, email = ?, bday = ?, sex = ?, biography = ? WHERE user_id = ?");
      $stmt->execute([$fio, $telephone, $email, $bday, $sex, $biography, $_SESSION['user_id']]);
      
      $stmt = $db->prepare("DELETE FROM app_link_lang WHERE id_app = ?");
      $stmt->execute([$_SESSION['form_id']]);
      
      $stmt1 = $db->prepare("INSERT INTO app_link_lang (id_app, id_prog_lang) VALUES (?, ?)");
      foreach($languages as $row){
        $stmt1->execute([$_SESSION['form_id'], $row['id_prog_lang']]);
      }
    }
    else 
    {
      $login = substr(uniqid(), 0, 4).rand(10, 100);
      $password = rand(100, 1000).substr(uniqid(), 4, 10);
      setcookie('login', $login);
      setcookie('password', $password);
      $mpassword = md5($password);
    }
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
    setcookie('fio_value', $fio, time() + 24 * 60 * 60 * 365);
    setcookie('telephone_value', $telephone, time() + 24 * 60 * 60 * 365);
    setcookie('email_value', $email, time() + 24 * 60 * 60 * 365);
    setcookie('bday_value', $bday, time() + 24 * 60 * 60 * 365);
    setcookie('sex_value', $sex, time() + 24 * 60 * 60 * 365);
    setcookie('like_value', $like, time() + 24 * 60 * 60 * 365);
    setcookie('biography_value', $biography, time() + 24 * 60 * 60 * 365);
    setcookie('contract_value', $contract, time() + 24 * 60 * 60 * 365);

    // Сохраняем куку с признаком успешного сохранения.
    setcookie('save', '1');
  
    // Делаем перенаправление.
    header('Location: index.php');
  }
?>