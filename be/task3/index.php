<?php
function print_error($error)
{
    print($error);
    exit();
}
function validate_data($data)
{
    $all_names = ["fio", "telephone", "email", "bday", "sex", "langs", "biography", "contract"];
    $size_limits = ["fio" => 255, "email" => 255, "biography" => 512];
    foreach ($all_names as $key) {
        if (empty($data[$key])) {
            print_error("Field " . $key . " is empty.");
        } elseif (in_array($key, array_keys($size_limits))
            && strlen($data[$key]) > $size_limits[$key]) {
            print_error("Length of the contents of the field " . $key . " more than " . $size_limits[$key]
                . " symbols.");
        } elseif ($key == "telephone" && !preg_match('/^(\s*)?(\+)?([- _():=+]?\d[- _():=+]?){10,14}(\s*)?$/', $data[$key])) {
            print_error("Invalid telephone.");
        }
    }
}

function save_to_database($data)
{
    include("../hid_vars.php");
    $db_req = 'mysql:dbname=' . $database . ';host=' . $host;
    try {
        $db = new PDO($db_req, $user, $password,
            [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $names_data_for_app = ['fio', 'telephone', 'email', 'bday', 'sex', 'biography'];
        $app_req = "INSERT INTO application (" . implode(', ', $names_data_for_app) .
            ") VALUES (";
        $data_for_app = [];
        foreach ($names_data_for_app as $name) {
            $data_for_app[] = "'" . $data[$name] . "'";
        }
        $app_req = $app_req . implode(', ', $data_for_app) . ");";
        $app_stmt = $db->prepare($app_req);
        $app_stmt->execute();

        $last_app_id = $db->lastInsertId();
        $link_req = "INSERT INTO app_link_lang (id_app, id_prog_lang) VALUES ";
        $data_for_link = [];
        foreach ($data["langs"] as $lang) {
            $data_for_link[] = "(" . $last_app_id . ", " . $lang . ")";
        }
        $link_req = $link_req . implode(", ", $data_for_link) . ";";
        $link_stmt = $db->prepare($link_req);
        $link_stmt->execute();
        print("Data successfully saved to the database.");
    } catch (PDOException $e) {
        print_error($e->getMessage());
    }
}


header('Content-Type: text/html; charset=UTF-8');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {

        print('The data is stored in the database.');
    }
    include('body.php');
    exit();
}
$form_data = $_POST;
//foreach ($form_data as $key => $val)
//{
//    print($key . " = '" . $val . "' empty = " . isset($val) . " ");
//}
validate_data($form_data);
save_to_database($form_data);


