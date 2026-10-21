<?php
class Registration {
    private PDO $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    public function getAll(string $keyword = ""): array {
        $sql = "SELECT r.registration_id, r.student_id, r.event_id, r.status, r.attendance, r.created_at,
                       s.student_name, s.course, s.email,
                       e.event_name, e.event_date
                FROM registrations r
                JOIN students s ON s.student_id = r.student_id
                JOIN events e   ON e.event_id   = r.event_id
                WHERE e.event_name LIKE :q1 OR s.student_name LIKE :q2 OR r.status LIKE :q3
                ORDER BY r.registration_id DESC";
        $stmt = $this->conn->prepare($sql);
        $like = "%{$keyword}%";
        $stmt->execute([":q1" => $like, ":q2" => $like, ":q3" => $like]);
        return $stmt->fetchAll();
    }
    public function add(int $studentId, int $eventId, string $status): bool {
        $stmt = $this->conn->prepare("INSERT INTO registrations (student_id, event_id, status) VALUES (?, ?, ?)");
        return $stmt->execute([$studentId, $eventId, $status]);
    }
    public function get(int $id): array|false {
        $stmt = $this->conn->prepare("SELECT * FROM registrations WHERE registration_id=?");
        $stmt->execute([$id]); return $stmt->fetch();
    }
    public function update(int $id, int $studentId, int $eventId, string $status): bool {
        $stmt = $this->conn->prepare("UPDATE registrations SET student_id=?, event_id=?, status=? WHERE registration_id=?");
        return $stmt->execute([$studentId, $eventId, $status, $id]);
    }
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM registrations WHERE registration_id=?");
        return $stmt->execute([$id]);
    }

    /** Everyone registered for one event who has not cancelled (the attendance list). */
    public function getByEvent(int $eventId): array {
        $stmt = $this->conn->prepare(
            "SELECT r.registration_id, r.attendance, s.student_name, s.course, s.year_level, s.email
             FROM registrations r
             JOIN students s ON s.student_id = r.student_id
             WHERE r.event_id = ? AND r.status <> 'Cancelled'
             ORDER BY s.student_name ASC");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }
    public function setAttendance(int $id, int $eventId, string $attendance): bool {
        $stmt = $this->conn->prepare(
            "UPDATE registrations SET attendance = ? WHERE registration_id = ? AND event_id = ? AND status <> 'Cancelled'");
        return $stmt->execute([$attendance, $id, $eventId]);
    }
    public function markAllPresent(int $eventId): int {
        $stmt = $this->conn->prepare(
            "UPDATE registrations SET attendance = 'Present'
             WHERE event_id = ? AND status <> 'Cancelled' AND attendance = 'Pending'");
        $stmt->execute([$eventId]);
        return $stmt->rowCount();
    }
}
