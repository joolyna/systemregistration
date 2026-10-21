<?php
/** Reads + validates the course form POST. Returns [values, error]. */
function read_course_post(): array
{
    $values = [
        'college'     => trim($_POST['college'] ?? ''),
        'course_name' => trim($_POST['course_name'] ?? ''),
    ];

    if ($values['college'] === '' || $values['course_name'] === '') {
        return [$values, 'Please fill in the college and the course name.'];
    }
    if (mb_strlen($values['college']) > 100) {
        return [$values, 'College name is too long (100 characters max).'];
    }
    if (mb_strlen($values['course_name']) > 80) {
        return [$values, 'Course name is too long (80 characters max).'];
    }

    return [$values, ''];
}
