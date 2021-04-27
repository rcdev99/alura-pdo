<?php

namespace Alura\Pdo\Infrastructure\Repository;

use Alura\Pdo\Domain\Model\Phone;
use Alura\Pdo\Domain\Repository\StudentRepository;
use Alura\Pdo\Domain\Model\Student;
use Alura\Pdo\Infrastructure\Persistence\ConnectionCreator;
use PDO;

class PdoStudentRepository implements StudentRepository
{
    
    private \PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection; 
    }

    private function hydrateStudentList(\PDOStatement $stmt): array
    {
        $studentDataList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $studentList = [];

        foreach ($studentDataList as $studentData) {
            $studentList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birth_date'])                
            );
        }

        return $studentList;
    }

    //Responsável por obter a lista de estudantes cadastrados
    public function allStudents():array{      
        
        $result = $this->connection->query('SELECT * FROM students;');
        $studentDataList = $result->fetchAll(PDO::FETCH_ASSOC);
        $studentList = [];

        foreach ($studentDataList as $studentData) {
            //Instanciando aluno e inserindo em lista com base nos dados obtidos
            $studentList[] = new Student(
                $studentData['id'],
                $studentData['name'],
                new \DateTimeImmutable($studentData['birth_date'])
            );
        };

        return $studentList;
    }

    //Responsável por trazer todos os estudantes nascidos em uma determinada data
    public function studentsBirthAt(\DateTimeImmutable $birthDate): array{
        
        $sql = 'SELECT * FROM students WHERE birth_date = ?;';
        $statment = $this->connection->prepare($sql);
        $statment->bindValue(1,$birthDate->format('Y-m-d'));
        $statment->execute();

        return $this->hydrateStudentList($statment);
    }

    public function save(Student $student): bool{

        $sqlInsert = "INSERT INTO students (name, birth_date) VALUES (:name, :birth_date);";
        $statement = $this->connection->prepare($sqlInsert);
        $statement->bindValue(':name', $student->name());
        $statement->bindValue(':birth_date', $student->birthDate()->format('Y-m-d'));
        return $statement->execute();
    }

    public function remove(Student $student): bool
    {
        $statement = $this->connection->prepare('DELETE FROM students WHERE id = ?;');
        $statement->bindValue(1,$student->id(),PDO::PARAM_INT);
        return $statement->execute();
    }

    public function studentsWithPhones(): array
    {
        $sql = 'SELECT students.id,
                       students.name,
                       students.birth_date,
                       phones.id AS phone_id,
                       phones.area_code,
                       phones.number
                FROM students
                JOIN phones ON students.id = phones.student_id;';         
                
        $statement = $this->connection->query($sql);
        $result = $statement->fetchAll();       
        $studentList = [];

        foreach ($result as $row) {
            if(!array_key_exists($row['id'], $studentList)){
                $studentList[$row['id']] = new Student(
                    $row['id'],
                    $row['name'],
                    new \DateTimeImmutable($row['birth_date'])
                );    
            }
            
            $phone = new Phone($row['phone_id'], $row['area_code'], $row['number']);
             $studentList[$row['id']]->addPhone($phone);   
        }

        return $studentList;
    }

    private function fillPhonesOf(Student $student): void
    {
        $sql = 'select id, area_code, number FROM phones WHERE student_id = ?';
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(1, $student->id(), PDO::PARAM_INT);
        //Executando script
        $statement->execute();

        //Recebendo retorno do script executado
        $phoneDataList = $statement->fetchAll();
        foreach ($phoneDataList as $phoneData) {
            $phone = new Phone(
                $phoneData['id'],
                $phoneData['area_code'],
                $phoneData['number'] 
            );

            $student->Addphone($phone);
        }

    }
}