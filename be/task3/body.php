<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" href="../icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100;200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Задание 3</title>
</head>
<body>
    <header class = "rounded-bottom"> 
        <img src="icon.png" alt = "logo" class = "logo"> 
        <div id = "name">RECORDING LABELS</div> 
    </header>

    <div class = "block_form rounded mt-2 pl-4 pb-4">
          <h1 class = "mt-2">Форма</h1>
          <form action="#" method="post" target="_blank">
            <fieldset>
              <legend>Персональные данные</legend>
              <ul>
                <li>
                  <label for="fio">ФИО:</label>
                  <input type="text" name="fio" placeholder="Иван Иванов Иванович" id="fio" required>
                </li>
                <li>
                    <label for="bday">Дата рождения: </label>
                    <input type="date" id="bday" name="bday">
                </li>
              </ul>
            </fieldset>
            <fieldset>
                <legend>Пол</legend>
                <ul>
                  <li>
                    <input type="radio" name="sex" id="front" value="male" checked>
                    <label for="front">Мужской</label>
                  </li>
                  <li>
                    <input type="radio" name="sex" id="back" value="female">
                    <label for="back">Женский</label>
                  </li>
                </ul>
              </fieldset>
            <fieldset>
              <legend>Контакты</legend>
              <ul>
                <li>
                  <label for="email">E-mail:</label>
                  <input type="email" name="email" placeholder="ivanov@gmail.com" id="email" required>
                </li>
                <li>
                  <label for="telephone">Телефон:</label>
                  <input type="tel" name="telephone" placeholder="+7 000 000-00-00" id="telephone" maxlength="21" required>
                </li>
              </ul>
            </fieldset>
            <div>
              <label for="message">Биография</label>
              <br>
              <textarea name="biography" placeholder="Расскажите о себе" id="message"></textarea>
            </div>
            <fieldset>
                <label id="for-prog-lang" class="black label-center">Любимый язык программирования</label>
                <select name="langs[]" multiple="multiple" id="prog-lang" class="size-input" >
                    <option value="0">Pascal</option>
                    <option value="1">C</option>
                    <option value="2">C++</option>
                    <option value="3">JavaScript</option>
                    <option value="4">PHP</option>
                    <option value="5">Python</option>
                    <option value="6">Java</option>
                    <option value="7">Haskel</option>
                    <option value="8">Clojure</option>
                    <option value="9">Prolog</option>
                    <option value="10">Scala</option>
                </select>
            </fieldset>
            С контрактом ознакомлен(-а)
                <input type="checkbox" name="contract" id="Check" checked>
                <label for="Check"></label>
            <div>
              <button type="submit">Сохранить</button>
            </div>
          </form>
        </div>
      <footer class = "rounded-top mt-2">
        <div id = "footer_txt">© Tolstenkov Anton</div>
      </footer>
    </body>
</html>
</body>

</html>