<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/event_validate.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Event.php';

$db = (new Database())->connect();
$eventModel = new Event($db);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    flash('Invalid event. Please use the Edit button in the events table.', 'error');
    redirect('events.php');
}

$event = $eventModel->get($id);
if (!$event) {
    flash('Event not found.', 'error');
    redirect('events.php');
}

$taken = (int) $event['taken'];
$error = '';
$values = [
    'event_name' => $event['event_name'],
    'event_date' => $event['event_date'],
    'venue'      => (string) $event['venue'],
    'capacity'   => (string) $event['capacity'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $error] = read_event_post();

    if ($error === '' && (int) $values['capacity'] < $taken) {
        $error = "Capacity can't be lower than the $taken student" . ($taken === 1 ? '' : 's') . ' already registered.';
    }

    if ($error === '') {
        try {
            $eventModel->update($id, $values['event_name'], $values['event_date'], $values['venue'], (int) $values['capacity']);
            flash('Event updated successfully.');
            redirect('events.php');
        } catch (PDOException $e) {
            $error = 'Unable to update the event. Please try again.';
        }
    }
}

$pageTitle = 'Edit Event';
$active = 'events';
require __DIR__ . '/includes/header.php';

page_head('Edit event', $taken . ' seat' . ($taken === 1 ? '' : 's') . ' currently taken.');
$submitLabel = 'Update event';
require __DIR__ . '/includes/event_form.php';
require __DIR__ . '/includes/footer.php';
