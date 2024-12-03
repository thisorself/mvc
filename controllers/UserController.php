<?php
include_once __DIR__ . '/../core/Controller.php';

class UserController extends Controller {
    public function __construct($pdo) {
        parent::__construct($pdo);
    }

    public function index() {
        $users = User::all($this->pdo);
        if (count($users) == 0) {
            "Немае користувачiв!";
            exit;
        }

        print '<h1>Список користувачiв</h1>';
        print "<table border='2'><tr>";
        foreach ($users[0]->fields as $key => $value) {
            print "<th>$key</th>";
        }
        print "</tr>";

        foreach ($users as $user) {
            print "<tr>";
            foreach ($user->fields as $key => $value) {
                print "<td>";
                print $value;
                print "</td>";
            }
            print "</tr>";
        }
        print '</table>';
    }

    public function show($id) {
        $user = User::find($this->pdo, $id);
        if (!$user) {
            print "Немае такого користувача!";
            exit;
        }

        print '<h1>Користувач</h1>';
        print "<table border='2'><tr>";
        foreach ($user->fields as $key => $value) {
            print "<th>$key</th>";
        }
        print "</tr>";

        print "<tr>";
        foreach ($user->fields as $key => $value) {
            print "<td>";
            print $value;
            print "</td>";
        }
        print "</tr>";
        print '</table>';
    }

    public function read($id) {
        $user = User::find($this->pdo, $id);
        $count = Model::count($this->pdo, 'users');
    
        $list = "";
        if ($id > 1)
            $list .= '<a href="user.php?mode=read&user=' . $id - 1 . '">Назад</a>   ';
        if ($id != $count)
            $list .= '<a href="user.php?mode=read&user=' . $id + 1 . '">Вперед</a><br>';
        else
            $list .= '<br>';

        print
            '<p>Номер користувача: ' . $user->fields->id . '</p>
            <p>Нiкнейм:' . $user->fields->username . '</p>
            <p>Повне iм`я:' . $user->fields->fullname . '</p>
            <p>Телефон:' . $user->fields->phone . '</p>
            <p>Пошта:' . $user->fields->email . '</p>
            <p>Тип користувача:' . Controller::EnumAndNull($user->fields->role) . '</p>' . $list . '
            <a href="user.php?mode=create&user=' . $count + 1 . '">Додати</a><br>
            <a href="user.php?mode=edit&user=' . $id . '">Редагувати</a><br>
            <a href="edit.php?do=delete&user=' . $user->fields->id . '">Видалити</a><br>
            <a href="../index.php">Повернутися</a>';
    }

    public function edit_show($id) {
        $user = User::find($this->pdo, $id);

        print
            '<form action="edit.php?do=edit&user=' . $user->fields->id . '" method="POST">
            <p>Номер користувача: ' . $user->fields->id  . '</p>
            <p>Нiкнейм: <input type="text" name="username" value="' . $user->fields->username . '"></p>
            <p>ПIБ: <input type="text" name="fullname" value="' . $user->fields->fullname . '"></p>
            <p>Телефон: <input type="text" name="phone" value="' . $user->fields->phone . '"></p>
            <p>Пошта: <input type="text" name="email" value="' . $user->fields->email . '"></p>
            <p>Тип користувача: <input type="text" name="role" value="' . Controller::EnumAndNull($user->fields->role) . '"></p>
            <a href="estate.php?mode=read&user=' . $id . '">Повернутися до перегляду</a><br>
            <input type="submit" value="Зберегти">     
            </form>';
    }

    public function correct($do) {
        $errors = [];

        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $prohibitedPattern = '/[!@#$%^&*]/';
        if ($username == "" || preg_match($prohibitedPattern, $username)) {
            $errors[] = "Нiкнейм пустий або некоректний!";
        }

        $password = isset($_POST['password']) ? $_POST['password'] : "";
        if ($do != 'edit') {
            $prohibitedPattern = '/[!@#$%^&*]/';
            if ($password == "" || preg_match($prohibitedPattern, $username) || str_contains($password, ' ')) {
                $errors[] = "Пароль пустий або некоректний!";
            }
            else $password = password_hash($password, PASSWORD_BCRYPT);
        }

        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : "";
        if ($fullname == "") {
            $errors[] = "Iм'я пусте або некоректне!";
        }

        $phone = isset($_POST['phone']) ? $_POST['phone'] : "";
        if ($phone == "" || !str_contains($phone, '+')) {
            $errors[] = "Телефон пустий або некоректний!";
        }

        $email = isset($_POST['email']) ? $_POST['email'] : "";
        if ($email == "" || !str_contains($email, '@')) {
            $errors[] = "Пошта пуста або некоректна!";
        }

        $role = isset($_POST['role']) ? $_POST['role'] : "";
        if ($role != "") {
            try {
                $role = Role::from($role);
            } catch (ValueError $e) {
                $errors[] = "Тип користувача некоректний!";
            }
        } else
            $errors[] = "Тип користувача пустий!";
        
        if (!$errors) {
            $fields = [
                'id' => $_GET['user'],
                'username' => $username,
                'password' => $password,
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'role' => $role
            ];
            switch ($do) {
                case 'edit':
                    $this->edit($fields);
                    break;
                case 'create':
                    $this->create($fields);
                    break;
            }
        } else {
            foreach ($errors as $error) {
                print $error . "<br>";
            }
        }
    }

    private function edit($fields) {
        $user = User::find($this->pdo, $_GET['user']);

        foreach ($fields as $key => $value) {
            if ($key == 'id') continue;
            if ($key == 'password') continue;
            $user->fields->$key = $value;
        }

        $user->update();
        print "Користувач успiшно оновлений!<br>";
    }

    private function create($fields) {
        $user = User::create($this->pdo, $fields);
        $user->insert();
        print "Користувач успiшно створений!";
    }

    public function delete($id) {
        User::find($this->pdo, $id)->delete();
        print "Користувач успiшно видалений!";
    }

    public function create_form($id) {
        print
            '<h1>Створення нерухомостi</h1><br>
            <form action="edit.php?do=create&user=' . $id . '" method="POST">
            <p>Номер користувача:' . $id . '</p>
            <p>Нiкнейм: <input type="text" name="username"></p>
            <p>Пароль: <input type="text" name="password"></p>
            <p>ПIБ: <input type="text" name="fullname"></p>
            <p>Телефон: <input type="text" name="phone"></p>
            <p>Пошта: <input type="text" name="email"></p>
            <p>Тип користувача: <input type="text" name="role"></p>
            <input type="submit" value="Створити">     
            </form>';
    }

    public function store($user) {
        $user->insert();
        print '<h1>Користувача успiшно збережено!</h1>';
        print '<a href="user.php?mode=read&user=1">Повернутися</a>';
    }

    public function checkLogIn() {
        if (!isset($_COOKIE['token'])) {
            print '<h4>Незареестрований гiсть!</h4>
                   <form action="register.php" method="POST">
                   <p>Введiть нiкнейм:</p><input type="text" name="username">
                   <p>Введiть пароль:</p><input type="text" name="password"><br>
                   <input type="submit" value="Зарееструватися"></button>';
        }
        else {
            $user = User::find($this->pdo, $_COOKIE['user_id']);
            print $user->fields->fullname . " вітаємо!<br>";
            setcookie('token', $user->fields->password, time() + 2000);
            setcookie('user_id', $user->fields->id, time() + 2000);
            print "<a href='views/estate.php?mode=read&real_estate=1'>Перейти до перегляду нерухомостi.</a><br>";
            print "<a href='views/user.php?mode=read&user=1'>Перейти до перегляду користувачiв.</a>";
        }
    }
}