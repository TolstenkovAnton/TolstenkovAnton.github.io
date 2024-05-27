<?php
  header('Content-Type: text/html; charset=UTF-8');
  
  function del_cook($cook){
    setcookie($cook.'_error', '', time() - 30 * 24 * 60 * 60);
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
    
    function val_empty($enName, $val){
      global $errors, $values, $messages;

      $errors[$enName] = !empty($_COOKIE[$enName.'_error']);
      $messages[$enName] = "<div class='messageError'>$val</div>";
      $values[$enName] = empty($_COOKIE[$enName.'_value']) ? '' : $_COOKIE[$enName.'_value'];
      del_cook($enName);
      return;
    }
    
    if (!empty($_COOKIE['save'])) {
      setcookie('save', '', 100000);
      // Если есть параметр save, то выводим сообщение пользователю.
      $messages['success'] = '<div class="message">Спасибо, данные сохранены.</div>';
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
    $error = false;

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
    
    try {
      $stmt = $db->prepare("INSERT INTO application (fio, telephone, email, bday, sex, biography) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->execute([$fio, $telephone, $email, $bday, $sex, $biography]);
      $fid = $db->lastInsertId();
      $stmt1 = $db->prepare("INSERT INTO app_link_lang (id_app, id_prog_lang) VALUES (?, ?)");
      foreach($languages as $row){
          $stmt1->execute([$fid, $row['id_link']]);
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