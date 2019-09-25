<?
//echo $_POST["login"] . " " . $_POST["password"]
require_once($_SERVER['DOCUMENT_ROOT'].'/php/lib.php');
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $isAuthOk = 0;
    // Функция для генерации случайной строки
    function generateCode($length = 6)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHI JKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0, $clen)];
        }
        return $code;
    }

    // Соединямся с БД
    $conn = getConnection();

    //$conn = new mysqli('localhost', 'root', '', 'tor');
    //addLog("", 1);
    // Вытаскиваем из БД запись, у которой логин равняеться введенному
    $query = $conn->query("SELECT user_id, user_password FROM users WHERE user_login='" . $_POST['login'] . "' LIMIT 1");
    $data = mysqli_fetch_assoc($query);
    //echo "Hello1";

    // Сравниваем пароли
    if ($data['user_password'] === md5(md5($_POST['password']))) {
        //echo "Hello 2";
        // Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));

        // Записываем в БД новый хеш авторизации и IP
        $conn->query("UPDATE users SET user_hash='" . $hash . "' WHERE user_id='" . $data['user_id'] . "'");

        // Ставим куки
        setcookie("id", $data['user_id'], time() + 30 * 60);
        setcookie("hash", $hash, time() + 30 * 60, null, null, null, true); // httponly !!!
        $isAuthOk = 1;

        addLog("", 2);
        // Переадресовываем браузер на страницу проверки нашего скрипта
        header("Location: orders/");
        exit();
        //echo "200";

    } else {
        //echo "Hello 3";
        setcookie("id", "", time() - 3600 * 24 * 30 * 12, "/");
        setcookie("hash", "", time() - 3600 * 24 * 30 * 12, "/");
        $isAuthOk = -1;
        addLog("", 4);
        //echo "-1";
    }
}
?>