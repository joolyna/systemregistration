<?php
class Student {
    private PDO $conn;
    private string $table = "students";
    public function __construct(PDO $db) { $this->conn = $db; }

    public function getAll(string $keyword = ""): array {
        $sql = "SELECT * FROM {$this->table}
                WHERE student_name LIKE :q OR course LIKE :q OR year_level LIKE :q OR email LIKE :q
                ORDER BY student_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":q" => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }
    public function add(string $name, string $course, string $year, string $email): bool {
        $stmt = $this->conn->prepare("INSERT INTO students (student_name, course, year_level, email) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $course, $year, $email]);
    }
    public function get(int $id): array|false {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->execute([$id]); return $stmt->fetch();
    }
    public function update(int $id, string $name, string $course, string $year, string $email): bool {
        $stmt = $this->conn->prepare("UPDATE students SET student_name=?, course=?, year_level=?, email=? WHERE student_id=?");
        return $stmt->execute([$name, $course, $year, $email, $id]);
    }
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM students WHERE student_id=?");
        return $stmt->execute([$id]);
    }
}
