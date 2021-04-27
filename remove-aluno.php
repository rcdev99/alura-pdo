<?php

require_once 'vendor/autoload.php';

$path = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $path);

$prepareStatement = $pdo->prepare('DELETE FROM students WHERE id = (:id);');
$prepareStatement->bindValue(':id',2,PDO::PARAM_INT);
if($prepareStatement->execute()){
    echo 'Exclusão realizada com sucesso';
}
