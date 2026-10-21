<?php
class Course {
    private PDO $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    public function getAll(string $keyword = ""): array {
        $sql = "SELECT c.*, COUNT(s.student_id) AS students
                FROM courses c
                LEFT JOIN students s ON s.course = c.course_name
                WHERE c.course_name LIKE :q OR c.college LIKE :q
                GROUP BY c.course_id
                ORDER BY c.college, c.course_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":q" => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }
    public function get(int $id): array|false {
        $stmt = $this->conn->prepare("SELECT * FROM courses WHERE course_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function nameTaken(string $name, int $exceptId = 0): bool {
        $stmt = $this->conn->prepare("SELECT 1 FROM courses WHERE course_name = ? AND course_id <> ?");
        $stmt->execute([$name, $exceptId]);
        return (bool) $stmt->fetchColumn();
    }
    public function colleges(): array {
        return $this->conn->query("SELECT DISTINCT college FROM courses ORDER BY college")->fetchAll(PDO::FETCH_COLUMN);
    }
    public function studentCount(string $courseName): int {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM students WHERE course = ?");
        $stmt->execute([$courseName]);
        return (int) $stmt->fetchColumn();
    }
    public function add(string $college, string $name): bool {
        $stmt = $this->conn->prepare("INSERT INTO courses (college, course_name) VALUES (?, ?)");
        return $stmt->execute([$college, $name]);
    }
    // Renaming a course also renames it on every student who has it
    public function update(int $id, string $college, string $name): bool {
        $old = $this->get($id);
        if (!$old) return false;
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("UPDATE courses SET college = ?, course_name = ? WHERE course_id = ?");
            $stmt->execute([$college, $name, $id]);
            if ($old['course_name'] !== $name) {
                $upd = $this->conn->prepare("UPDATE students SET course = ? WHERE course = ?");
                $upd->execute([$name, $old['course_name']]);
            }
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM courses WHERE course_id = ?");
        return $stmt->execute([$id]);
    }
}
