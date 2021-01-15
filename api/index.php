<?php

include_once __DIR__ . '/models/City.php';
include_once __DIR__ . '/models/CarColor.php';
include_once __DIR__ . '/models/CarBrand.php';
include_once __DIR__ . '/models/CarModel.php';
include_once __DIR__ . '/models/Worker.php';
include_once __DIR__ . '/models/Order.php';
include_once __DIR__ . '/models/Tariff.php';
include_once __DIR__ . '/models/SendPassword.php';
include_once __DIR__ . '/models/AcceptPassword.php';
include_once __DIR__ . '/models/UploadFile.php';


header('Content-Type: application/json');
//header('Content-Type: image/jpeg');

$code     = 'OK';
$response = [];

$lang = 'en';

$post = $_POST;

if (array_key_exists('lang', $post)) {
    $lang = $post['lang'];
    unset($post['lang']);
}

// Получение статических данных
if (isset($_GET['getData'])) {
    $city = new City();
    $city->setLang($lang);

    $city->load(['currentTime' => 12]);
    $city->get();

    $color = new CarColor();
    $color->setLang($lang);
    $color->get();

    $position           = new Worker();
    $position->language = $lang;

    $model = new CarBrand();
    $model->setLang($lang);
    $model->get();

    $response = [
        'cities'    => $city->getCityList(),
        'carColors' => $color->getCarColorList(),
        'brands'    => $model->getCarBrandList(),
        'position'  => $position->getPositionList(),
    ];

    // Получение моделей бренда автомобиля
} elseif (isset($_GET['getCarModel'])) {
    $model = new CarModel();
    $model->setLang($lang);
    $model->load($post);
    $model->get();
    $response = $model->getCarModelList();


    // Создание исполнителя
} elseif (isset($_GET['createWorker'])) {
    $model = new Worker();
    $model->load($post);

    $model->post();
    $isCreated = $model->isCreated();

    if ($isCreated) {
        $model->sendMail();
    }
    $response = (int)$isCreated;


    // Получение списка готовых адресов
} elseif (isset($_GET['getPoints'])) {
    $model    = new Order();
    $response = $model->getPoints();


    // Получение списка тарифов
} elseif (isset($_GET['getTariff'])) {
    $model = new Tariff();
    $model->get();
    $response = $model->getTariffList();


    // Создание заказа-трансфера
} elseif (isset($_GET['createTransfer'])) {
    $pointId = $post['pointId'] ?: '0';

    $model = new Order();
    $model->load($post);

    $model->setAddressByPoint($pointId);

    $model->post();
    $response = (int)$model->isCreated();


    // Получение пароля по смс
} elseif (isset($_GET['sendPassword'])) {
    $model = new SendPassword();
    $model->load($post);
    $model->post();
    $response = (int)$model->isSend();


    // Подтверждение пароля по смс
} elseif (isset($_GET['acceptPassword'])) {
    $model = new AcceptPassword();
    $model->load($post);
    $model->post();
    $response = (int)$model->isAccept();


    // Создание заказа
} elseif (isset($_GET['createOrder'])) {
    $model = new Order();
    $model->load($post);
    $model->post();
    $response = (int)$model->isCreated();


    // Загрузка файла
} elseif (isset($_GET['upload'])) {

    $model = new UploadFile();
    $model->setMimeType();
    $model->load();
    $response = $model->getName();


} else {
    $code               = 'ERROR';
    $response['action'] = 'Unknown action';
}


if ($code != 'ERROR') {
    $errors = $model->getErrors();
    if (!empty($errors)) {
        $code     = 'ERROR';
        $response = $errors;
    }
}

echo json_encode(compact('code', 'response'));