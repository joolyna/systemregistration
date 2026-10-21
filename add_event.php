<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/event_validate.php';
require_once __DIR__ . '/config/connection.php';
require_once __DIR__ . '/classes/Event.php';

$db = (new Database())->connect();
$eventModel = new Event($db);

$error = '';
$values = ['event_name' => '', 'event_date' => '', 'venue' => '', 'capacity' => '50'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$values, $error] = read_event_post();

    if ($error === '') {
        try {
            $eventModel->add($values['event_name'], $values['event_date'], $values['venue'], (int) $values['capacity']);
            flash('Event added successfully.');
            redirect('events.php');
        } catch (PDOException $e) {
            $error = 'Unable to save the event. Please try again.';
        }
    }
}

$pageTitle = 'Add Event';
$active = 'events';
require __DIR__ . '/includes/header.php';

page_head('Add event', 'Set the date and how many students can join.');
$submitLabel = 'Save event';
require __DIR__ . '/includes/event_form.php';
require __DIR__ . '/includes/footer.php';
