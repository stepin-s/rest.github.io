<?php

require_once __DIR__ . '/_params.php';

// Путь до директории с временными файлами
$dir = $GLOBALS['params']['tmp'];

// Время через которое файл считается устаревшим
$offsetTime = 1 * 60 * 60;

// Исключения
$except_files = [
    '.', // Текущая директория. Удаляет все файлы внутри
    '..', // Родительская директория. Удаляет все файлы на один уровень выше
    'test' // Файл для тестов
];

$time  = time();
$files = scandir($dir);

foreach ($files as $file) {
    if (in_array($file, $except_files, false)) {
        continue;
    }
    $file       = $dir . '/' . $file;
    $fileCreate = filectime($file);

    if ($fileCreate + $offsetTime < $time) {
        unlink($file);
    }
}

