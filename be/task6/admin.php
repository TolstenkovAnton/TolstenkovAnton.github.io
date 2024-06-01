<?php
  include("../hid_vars.php");
  $haveAdmin = checkAdmin($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);

  if (!$haveAdmin) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    
    exit();
  }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="libs/bootstrap-4.0.0-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="./styleAdmin.css">
    <title>Задание 6 (ADMIN)</title>
</head>
<body class="admin">

  <header>
    <div><a href="#data">Информация</a></div>
    <div><a href="#analize">Статистика</a></div>
</header>

  <table id="data">
    <thead>
      <tr>
        <th>id</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Почта</th>
        <th>День рождения</th>
        <th>Пол</th>
        <th>Биография</th>
        <th>ЯП</th>
        <th></th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php
        $dbFD = $db->query("SELECT * FROM application ORDER BY id_app DESC");
        while($row = $dbFD->fetch(PDO::FETCH_ASSOC)){
          echo '<tr data-id='.$row['id_app'].'>
                  <td>'.$row['id_app'].'</td>
                  <td>'.$row['fio'].'</td>
                  <td>'.$row['telephone'].'</td>
                  <td>'.$row['email'].'</td>
                  <td>'.date("d.m.Y", strtotime($row['bday'])).'</td>
                  <td>'.(($row['sex'] == "male") ? "Мужской" : "Женский").'</td>
                  <td class="wb">'.$row['biography'].'</td>
                  <td>';
          $dbl = $db->prepare("SELECT * FROM app_link_lang fd
                                LEFT JOIN prog_lang l ON l.id_prog_lang = fd.id_prog_lang
                                WHERE id_app = ?");
          $dbl->execute([$row['id_app']]);
          while($row1 = $dbl->fetch(PDO::FETCH_ASSOC)){
            echo $row1['name_prog_lang'].'<br>';
          }
          echo '</td>
                <td><a href="./index.php?uid='.$row['user_id'].'" target="_blank">Редактировать</a></td>
                <td><button class="remove">Удалить</button></td>
                <td colspan="10" class="form_del hid">Форма удалена</td>
              </tr>';
        }
      ?>


    </tbody>
  </table>

  <table class="analize" id="analize">
    <thead>
      <tr>
        <th>ЯП</th>
        <th>Кол-во пользователей</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $qu = $db->query("SELECT l.id_prog_lang, l.name_prog_lang, COUNT(id_app) as count FROM prog_lang l 
                            LEFT JOIN app_link_lang fd ON fd.id_prog_lang = l.id_prog_lang
                            GROUP by l.id_prog_lang");
        while($row = $qu->fetch(PDO::FETCH_ASSOC)){
          echo '<tr>
                  <td>'.$row['name_prog_lang'].'</td>
                  <td>'.$row['count'].'</td>
                </tr>';
        }
      ?>
    </tbody>
  </table>

  <script src="./core.js"></script>
</body>
</html>
