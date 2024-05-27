<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" href="../icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@100;200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <title>Задание 5</title>
</head>
<body>
<div class="block_form">
    <form action="" method="post">
        <h3>Форма</h3>
        <div class="message"><?php if(isset($messages['success'])) echo $messages['success']; ?></div>
        <div>
            <input class="w100 <?php echo ($errors['fio'] != NULL) ? 'borders' : ''; ?>" value="<?php echo $values['fio']; ?>" type="text" name="fio" placeholder="ФИО">
            <div class="texterror"><?php echo $messages['fio']?></div>
        </div>
        <div>
            <input class="w100 <?php echo ($errors['telephone'] != NULL) ? 'borders' : ''; ?>" value="<?php echo $values['telephone']; ?>" type="tel" name="telephone" placeholder="Телефон">
            <div class="texterror"><?php echo $messages['telephone']?></div>
        </div>
        <div>
            <input class="w100 <?php echo ($errors['email'] != NULL) ? 'borders' : ''; ?>" value="<?php echo $values['email']; ?>" type="email" name="email" placeholder="email">
            <div class="texterror"><?php echo $messages['email']?></div>
        </div>
        <div>
            <input class="w100 <?php echo ($errors['bday'] != NULL) ? 'borders' : ''; ?>" value="<?php if($values['bday'] > 100000) echo $values['bday']; ?>" type="date" name="bday">
            <div class="texterror"><?php echo $messages['bday']?></div>
        </div>
        <div class="mgn">
            <div>Пол:</div>
            <label>
                <input type="radio" name="sex" value="male" <?php if($values['sex'] == 'male') echo 'checked';?>>
                <span class="<?php echo ($errors['sex'] != NULL) ? 'texterror2' : ''; ?>">Мужской</span>
            </label>
            <br>
            <label>
                <input type="radio" name="sex" value="female" <?php if($values['sex'] == 'female') echo 'checked';?>>
                <span class="<?php echo ($errors['sex'] != NULL) ? 'texterror2' : ''; ?>">Женский</span>
            </label>
            <div class="texterror"><?php echo $messages['sex']?></div>
        </div>
        <div class="mgn">
            <select class="w100 <?php echo ($errors['langs'] != NULL) ? 'borders' : ''; ?>" name="langs[]" id="langs" multiple="multiple">
                <option disabled selected>Любимый язык программирования</option>
                <option value="Pascal" <?php echo (in_array('Pascal', $langssa)) ? 'selected' : ''; ?>>Pascal</option>
                <option value="C" <?php echo (in_array('C', $langssa)) ? 'selected' : ''; ?>>C</option>
                <option value="C++" <?php echo (in_array('C++', $langssa)) ? 'selected' : ''; ?>>C++</option>
                <option value="JavaScript" <?php echo (in_array('JavaScript', $langssa)) ? 'selected' : ''; ?>>JavaScript</option>
                <option value="PHP" <?php echo (in_array('PHP', $langssa)) ? 'selected' : ''; ?>>PHP</option>
                <option value="Python" <?php echo (in_array('Python', $langssa)) ? 'selected' : ''; ?>>Python</option>
                <option value="Java" <?php echo (in_array('Java', $langssa)) ? 'selected' : ''; ?>>Java</option>
                <option value="Haskel" <?php echo (in_array('Haskel', $langssa)) ? 'selected' : ''; ?>>Haskel</option>
                <option value="Clojure" <?php echo (in_array('Clojure', $langssa)) ? 'selected' : ''; ?>>Clojure</option>
                <option value="Prolog" <?php echo (in_array('Prolog', $langssa)) ? 'selected' : ''; ?>>Prolog</option>
                <option value="Scala" <?php echo (in_array('Scala', $langssa)) ? 'selected' : ''; ?>>Scala</option>
            </select>
            <div class="texterror"><?php echo $messages['langs']?></div>
        </div>
        <div>
            <textarea name="biography" placeholder="Биография" class="<?php echo ($errors['biography'] != NULL) ? 'borders' : ''; ?>"><?php echo $values['biography']; ?></textarea>
            <div class="texterror"><?php echo $messages['biography']?></div>
        </div>
        <div>
            <input type="checkbox" name="contract" id="contract" <?php echo ($values['contract'] != NULL) ? 'checked' : ''; ?>>
            <label for="contract" class="<?php echo ($errors['contract'] != NULL) ? 'texterror2' : ''; ?>">С контрактом ознакомлен (а)</label>
            <div class="texterror"><?php echo $messages['contract']?></div>
        </div>
        <button type="submit">Отправить</button>
    </form>
  </div>
</body>
</html>