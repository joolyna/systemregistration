<?php
class Event {
    private PDO $conn;
    public function __construct(PDO $db) { $this->conn = $db; }

    /** All events with how many seats are taken (cancelled registrations don't count). */
    public function getAll(string $keyword = ""): array {
        $sql = "SELECT e.*, COUNT(r.registration_id) AS taken,
                       COALESCE(SUM(r.attendance = 'Present'), 0) AS present,
                       COALESCE(SUM(r.attendance = 'Absent'), 0)  AS absent,
                       COALESCE(SUM(r.attendance = 'Excused'), 0) AS excused
                FROM events e
                LEFT JOIN registrations r
                       ON r.event_id = e.event_id AND r.status <> 'Cancelled'
                WHERE e.event_name LIKE :q1 OR e.venue LIKE :q2
                GROUP BY e.event_id
                ORDER BY e.event_date DESC, e.event_id DESC";
        $stmt = $this->conn->prepare($sql);
        $like = "%{$keyword}%";
        $stmt->execute([":q1" => $like, ":q2" => $like]);
        return $stmt->fetchAll();
    }
    public function get(int $id): array|false {
        $stmt = $this->conn->prepare(
            "SELECT e.*, (SELECT COUNT(*) FROM registrations r
                          WHERE r.event_id = e.event_id AND r.status <> 'Cancelled') AS taken
             FROM events e WHERE e.event_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function add(string $name, string $date, string $venue, int $capacity): bool {
        $stmt = $this->conn->prepare("INSERT INTO events (event_name, event_date, venue, capacity) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $date, $venue === '' ? null : $venue, $capacity]);
    }
    public function update(int $id, string $name, string $date, string $venue, int $capacity): bool {
        $stmt = $this->conn->prepare("UPDATE events SET event_name=?, event_date=?, venue=?, capacity=? WHERE event_id=?");
        return $stmt->execute([$name, $date, $venue === '' ? null : $venue, $capacity, $id]);
    }
    public function delete(int $id): bool {
        $stmt = $this->conn->prepare("DELETE FROM events WHERE event_id=?");
        return $stmt->execute([$id]);
    }
}
