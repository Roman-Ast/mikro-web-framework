<?php

namespace App;

require 'vendor/autoload.php';

use function App\Renderer\render;
use function App\response;

$app = new Application();
$DBusers = new DBimmitation\Users();


//GET
$app->get('/', function () {
    return response(render('index'));
});

$app->get('/about', function () {
    return response(
        render('about', 
            ['site' => 'Roman Popadinets mikro framework']
        )
    );
});

$app->get('/users/new', function ($meta, $params, $attributes) {
    return response(
        render('users/create', ['errors' => [], 'user' => []])
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
    $user = array_filter($users, function($user) use($attributes) {
        if ($user['id'] == $attributes['id']) {
            return $user;
        }
    });
    //var_dump($user);
    return response(render('users/show', ['user' => $user]));
});
$app->get('/users/:userId/articles/:id', function ($params, $arguments) {
    return render('articles/show', [
        'userId' => $arguments['userId'],
        'articleId' => $arguments['id']
        ]
    );
});
$app->get('/articles', function ($params) {
    return render('articles/index');
});

$app->get('/articles/:id', function ($params, $arguments) {
    return render('articles/show', ['articleId' => $arguments['id']]);
});


//POST 
$app->post('/users', function ($meta, $params, $attributes) use($DBusers) {
    $user = $params['user'];
    $errors = [];
    
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

    if (empty($errors)) {
        $DBusers->add($user);
        return response()->redirect('/');
    } else {
        return response(render('users/create', ['user' => $user, 'errors' => $errors]))
            ->withStatus(422);
    }
});




$app->run();