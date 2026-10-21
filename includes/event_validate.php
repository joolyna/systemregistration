<?php
/** Reads + validates the event form POST. Returns [values, error]. */
function read_event_post(): array
{
    $values = [
        'event_name' => trim($_POST['event_name'] ?? ''),
        'event_date' => trim($_POST['event_date'] ?? ''),
        'venue'      => trim($_POST['venue'] ?? ''),
        'capacity'   => trim($_POST['capacity'] ?? ''),
    ];

    if ($values['event_name'] === '' || $values['event_date'] === '' || $values['capacity'] === '') {
        return [$values, 'Please fill in the event name, date and capacity.'];
    }
    $date = DateTime::createFromFormat('Y-m-d', $values['event_date']);
    if (!$date || $date->format('Y-m-d') !== $values['event_date']) {
        return [$values, 'Please enter a valid event date.'];
    }
    $cap = filter_var($values['capacity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10000]]);
    if ($cap === false) {
        return [$values, 'Capacity must be a whole number between 1 and 10,000.'];
    }

    return [$values, ''];
}
