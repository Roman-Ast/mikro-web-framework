<?php

namespace App;

class FilesUpload
{
    public static function upload($entity, $key, $destination)
    {
        if (array_key_exists($entity, $_FILES)) {
            error_log(print_r($_FILES, true));
            $errorCode = $_FILES[$entity]['error'][$key];
            if ($errorCode !== UPLOAD_ERR_NO_FILE) {
                if ($errorCode !== UPLOAD_ERR_OK) {
                    return false;
                } else {
                    $tmpName = $_FILES['user']['tmp_name'][$key];
                    $name = $_FILES['user']['name'][$key];
                    $extension = new \SplFileInfo($name);
                    $baseName = basename($name, $extension);
                    $hashedName = md5(uniqid());
                    $newDestination = __DIR__.DIRECTORY_SEPARATOR.'storage'.DIRECTORY_SEPARATOR.$destination;
                    $finalDestination = $newDestination.DIRECTORY_SEPARATOR.$hashedName.$extension;
                    if (!move_uploaded_file($tmpName, $finalDestination)) {
                        return false;
                    } else {
                        return $destination .DIRECTORY_SEPARATOR. $hashedName.$extension;
                    }
                }
            }
        }
    }

    private function codeToMessage($code)
    {
        '[{"id":1,"name":"Roman","email":"roma@mail.ru","avatar":"https:\/\/sun9-14.userapi.com\/c628530\/v628530830\/199fc\/hUenz-7zS3U.jpg","password":"12345678"},{"id":2,"name":"Ruslan","email":"rus@mail.ru","avatar":"https:\/\/sun2.beeline-kz.userapi.com\/eAfwQ-VEE0VIyqzfczy0dqdjFQeIMTHKNlbt6Q\/ggRhEhyaHpQ.jpg","password":"12345678"},{"id":3,"name":"Igor","email":"igor@mail.ru","avatar":"https:\/\/sun9-33.userapi.com\/c626625\/v626625395\/18829\/26oJy_ksRzU.jpg","password":"12345678"},{"email":"oksi@mail.ru","name":"oksana","avatar":"https:\/\/sun9-62.userapi.com\/c637325\/v637325999\/55f61\/T8Tx-wVTobI.jpg","password":"1","id":4},{"email":"aha@mail.ru","name":"aha","avatar":"https:\/\/image.shutterstock.com\/image-vector\/incognito-icon-browse-private-vector-260nw-1462596698.jpg","password":"12345678","password_confirmation":"12345678","id":5},{"email":"ooo@mail.ru","name":"ooo","password":"12345678","password_confirmation":"12345678","avatar":"1474011210_15.jpg","id":6},{"email":"aaa@mail.ru","name":"aaa","password":"1234567","password_confirmation":"1234567","avatar":"images\/user_avatars$2y$10$BMinulkxNQeIY9DY7HprEeIVbHJ6L.06SOmiOtj6ruN6aYk47gkya","id":7}]';
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload _max_filesize";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missin a temporary folder";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stepped by extension";
                break;
    
            default:
                $message = "Unknown error";
                break;
        }
        return $message;
    }
}