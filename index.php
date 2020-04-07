<?php

namespace App;

require 'vendor/autoload.php';

use function App\Renderer\render;
use function App\response;
use App\FilesUpload;

$app = new Application();
$DBusers = new DBimmitation\Users();


//GET
$app->get('/', function () {
    ini_set('session.gc_maxlifetime', 86400);
    ini_set('session.cookie_lifetime', 86400);
    session_start();
    if (isset($_SESSION['user'])) {
        
        return response(render('index', [
            'user' => $_SESSION['user'], 
            'message' => $_SESSION['flash_message']
        ]));
    }
    return response(render('index'));
    
});

$app->get('/login', function () {
    return response(render('auth/login'));
});

$app->get('/register', function () {
    return response(render('auth/register'));
});


$app->get('/about', function () {
    return response(
        render('about', 
            ['site' => 'Roman Popadinets mikro framework']
        )
    );
});

$app->get('/users', function ($params) use($DBusers) {
    $users = $DBusers->getAllUsers();
    return response(
        render('users/index', ['users' => $users])
    );
});

$app->get('/users/:id', function ($meta, $params, $attributes) use($DBusers) {
    $users = $DBusers->getAllUsers();
    $user = array_reduce($users, function($acc, $user) use($attributes) {
        if ($user['id'] == $attributes['id']) {
            $acc = $user;
            return $acc;
        }
        return $acc;
    }, null);
    
    return response(render('users/show', ['user' => $user]));
});

$app->get('/articles', function ($params) {
    session_start();
    if ($_SESSION['user']) {
        return response(render('articles/index', ['user' => $_SESSION['user']]));
    }
    return response(render('articles/index'));
});

$app->get('/articles/create', function ($params) {
    session_start();
    return response(render('articles/create', ['user' => $_SESSION['user']]));
});

$app->get('/articles/:id', function ($params, $arguments) {
    return render('articles/show', ['articleId' => $arguments['id']]);
});

$app->get('/logout', function ($params, $arguments) {
    session_start();
    session_destroy();
    return response()->redirect('/');
});

//POST 
$app->post('/register', function ($meta, $params, $attributes) use($DBusers) {
    $user = $params['user'];
    $errors = [];

    //check repeating email
    $users = $DBusers->getAllUsers();
    foreach ($users as $item) {
        if ($user['email'] === $item['email']) {
            $errors['email'] = 'Такой пользователь уже зарегестрирован';
            return response(render('auth/register', ['user' => $user, 'errors' => $errors]))
            ->withStatus(422);
        }
    }

    if (!$user['email']) {
        $errors['email'] = "Email can't be blank";
    } else if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "It is not a valid email";
    } elseif (!$user['name']) {
        $errors['name'] = "Name can't be blank";
    } elseif (strlen($user['name']) < 3) {
        $errors['name'] = "Name should contents minimum 3 chars";
    } elseif (strlen($user['password']) < 7) {
        $errors['password'] = "Password should contents minimum 7 symbols";
    } elseif ($user['password'] !== $user['password_confirmation']) {
        $errors['password_confirmation'] = "Passwords doesn`t matches";
    } 

    //file uploading
    $path = FilesUpload::upload('user', 'avatar', 'images/user_avatars');
    
    if (!$path) {
        $user['avatar'] = 'images/user_avatars/default-user.png';
    } else {
        $user['avatar'] = $path;
    }

    if (empty($errors)) {
        //password hashing
        $hashedPass = password_hash($user['password'], PASSWORD_BCRYPT);
        $user['password'] = $hashedPass;

        $DBusers->add($user);
        session_start();
        $_SESSION['user'] = $user;
        $_SESSION['flash_message'] = "Добро пожаловать, Вы были успешно зарегестрирoваны";
        
        return response()->redirect('/');
    } else {
        return response(render('auth/register', ['user' => $user, 'errors' => $errors]))
            ->withStatus(422);
    }
});

$app->post('/login', function ($meta, $params, $attributes) use($DBusers) {
    $email = $params['email'];
    $pass = $params['password'];
    $errors = [];

    $users = $DBusers->getAllUsers();
    $authenticatedUser = array_reduce($users, function($acc, $user) use($email, $pass) {
        if ($user['email'] === $email && password_verify($pass, $user['password'])) {
            $acc = $user;
            return $acc;
        }
        return $acc;
    }, null);
    
    if ($authenticatedUser) {
        session_start();
        $_SESSION['user'] = $authenticatedUser;
        $_SESSION['flash_message'] = "Добро пожаловать, Вы вошли как {$authenticatedUser['name']}";
        return response()->redirect('/');
    } else {
        $errors['auth'] = 'Введены неверные логин и/или пароль';
        return response(render('auth/login', ['errors' => $errors]))->withStatus(422);
    }
    
});

$app->post('/articles', function($meta, $params, $attributes) {
    $title = $params['title'];
    $body = $params['body'];
    $author = $params['author'];
    $hashTags = $params['tags'];
    $errors = [];
    
    //validation
    if (strlen($title) > 20) {
        $errors['title'] = 'заголовок не может быть длиннее 20 символов';
    } else if (strlen($body) < 200) {
        $errors['body'] = 'статья не может содержать менее 200 символов';
    } else if (!$title) {
        $errors['title'] = 'Заголовок не может быть пустым';
    } else if (!$hashTags) {
        $errors['title'] = 'Заголовок не может быть пустым';
    }

    if (empty($errors)) {
        return response()->redirect('/articles/index');
    } else {
        $errors['auth'] = 'Введены неверные логин и/или пароль';
        return response(render('auth/login', ['errors' => $errors]))->withStatus(422);
    }
});

$app->run();