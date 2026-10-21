<?php
/**
 * Reads + validates the registration form POST.
 * Rules: student and event must exist, no duplicate (student + event),
 * and the event must have a free seat (cancelled registrations don't use a seat).
 * $ignoreId = the registration being edited (0 when adding).
 * Returns [values, error].
 */
function read_registration_post(PDO $db, int $ignoreId = 0): array
{
    $values = [
        'student_id' => trim($_POST['student_id'] ?? ''),
        'event_id'   => trim($_POST['event_id'] ?? ''),
        'status'     => trim($_POST['status'] ?? 'Registered'),
    ];

    $studentId = filter_var($values['student_id'], FILTER_VALIDATE_INT);
    $eventId   = filter_var($values['event_id'], FILTER_VALIDATE_INT);

    if (!$studentId || !$eventId) {
        return [$values, 'Please choose a student and an event.'];
    }
    if (!in_array($values['status'], STATUSES, true)) {
        return [$values, 'Please choose a valid status.'];
    }

    $check = $db->prepare("SELECT 1 FROM students WHERE student_id = ?");
    $check->execute([$studentId]);
    if (!$check->fetchColumn()) {
        return [$values, 'Please select a valid student.'];
    }

    $ev = $db->prepare("SELECT capacity FROM events WHERE event_id = ?");
    $ev->execute([$eventId]);
    $capacity = $ev->fetchColumn();
    if ($capacity === false) {
        return [$values, 'Please select a valid event.'];
    }

    $dup = $db->prepare(
        "SELECT 1 FROM registrations WHERE student_id = ? AND event_id = ? AND registration_id <> ?"
    );
    $dup->execute([$studentId, $eventId, $ignoreId]);
    if ($dup->fetchColumn()) {
        return [$values, 'This student is already registered for that event.'];
    }

    if ($values['status'] !== 'Cancelled') {
        $taken = $db->prepare(
            "SELECT COUNT(*) FROM registrations
             WHERE event_id = ? AND status <> 'Cancelled' AND registration_id <> ?"
        );
        $taken->execute([$eventId, $ignoreId]);
        if ((int) $taken->fetchColumn() >= (int) $capacity) {
            return [$values, 'Sorry, this event is already full.'];
        }
    }

    return [$values, ''];
}
