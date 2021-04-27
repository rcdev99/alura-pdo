<?php

use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use Alura\Pdo\Infrastructure\Repository\PdoStudentRepository;


require_once 'vendor/autoload.php';

$path = __DIR__ . '/banco.sqlite';
$pdo = new PDO('sqlite:' . $path);

//Instanciando aluno
$student = new Student(2,"Rodrigo Junior", new \DateTimeImmutable('1990-06-18'));

$studentRepository = new PdoStudentRepository(ConnectionCreator::createConnection());

$students = $studentRepository->studentsBirthAt(new \DateTimeImmutable('1995-03-08'));
var_dump($students);
