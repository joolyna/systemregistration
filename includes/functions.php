<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const YEAR_LEVELS = ['1st Year', '2nd Year', '3rd Year', '4th Year'];
const STATUSES    = ['Registered', 'Cancelled'];
const ATTENDANCE  = ['Pending', 'Present', 'Absent', 'Excused'];

// College => list of programs (used for the Course dropdown)
const COURSES = [
    'College of Business Education (CBE)' => [
        'Accountancy',
        'Accounting Technology',
        'Business Administration (Financial Management)',
        'Business Management',
        'Hospitality Management',
        'Tourism',
    ],
    'College of Teacher Education (CTE)' => [
        'Bachelor of Elementary Education',
        'Bachelor of Secondary Education major in English',
        'Bachelor of Secondary Education major in Mathematics',
        'Bachelor of Secondary Education major in Science',
        'Bachelor of Secondary Education major in Social Studies',
    ],
    'College of Computer Studies (CCS)' => [
        'Computer Science',
        'Information Technology',
        'Associate in Computer Technology',
    ],
    'College of Criminal Justice Education (CCJE)' => [
        'Bachelor of Science in Criminology',
    ],
    'Arts and Sciences / Psychology' => [
        'Bachelor of Science in Psychology',
    ],
];

/**
 * Courses grouped by college, read from the `courses` table.
 * Falls back to the built-in COURSES list if the table doesn't exist yet
 * (i.e. migrate_courses.sql hasn't been run).
 */
function course_groups(PDO $db): array
{
    try {
        $rows = $db->query("SELECT college, course_name FROM courses ORDER BY college, course_name")->fetchAll();
    } catch (PDOException $e) {
        return COURSES;
    }
    $groups = [];
    foreach ($rows as $r) {
        $groups[$r['college']][] = $r['course_name'];
    }
    return $groups;
}

function all_courses(PDO $db): array
{
    $groups = course_groups($db);
    return $groups ? array_merge(...array_values($groups)) : [];
}

/** "College of Computer Studies (CCS)" -> "CCS"; empty string when there is no abbreviation. */
function college_abbr(string $college): string
{
    return preg_match('/\(([^)]+)\)\s*$/', $college, $m) ? trim($m[1]) : '';
}

/** "College of Computer Studies (CCS)" -> "College of Computer Studies" */
function college_label(string $college): string
{
    return trim(preg_replace('/\s*\([^)]+\)\s*$/', '', $college));
}

function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function fmt_date(?string $date, string $format = 'M d, Y'): string
{
    if (!$date) return '';
    $ts = strtotime($date);
    return $ts ? date($format, $ts) : e($date);
}

function attendance_badge(string $attendance): string
{
    $class = 'badge-' . strtolower($attendance);
    return '<span class="badge ' . e($class) . '">' . e($attendance) . '</span>';
}

function status_badge(string $status): string
{
    $class = 'badge-' . strtolower($status);
    return '<span class="badge ' . e($class) . '">' . e($status) . '</span>';
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name));
    $first = mb_substr($parts[0] ?? '', 0, 1);
    $last  = count($parts) > 1 ? mb_substr(end($parts), 0, 1) : '';
    return e(mb_strtoupper($first . $last));
}

function icon(string $name): string
{
    $paths = [
        'home'     => '<path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/>',
        'users'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'plus'     => '<path d="M12 5v14M5 12h14"/>',
        'search'   => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'edit'     => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'trash'    => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>',
        'check'    => '<path d="M20 6 9 17l-5-5"/>',
        'alert'    => '<path d="M12 9v4M12 17h.01"/><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>',
        'ticket'   => '<path d="M3 9a2 2 0 0 0 0 6v3a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-3a2 2 0 0 1 0-6V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1z"/><path d="M13 5v14" stroke-dasharray="2 3"/>',
        'pin'      => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        'book'     => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/>',
        'arrow'    => '<path d="M5 12h14M13 5l7 7-7 7"/>',
        'lock'     => '<rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/>',
        'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'user'     => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'logout'   => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>',
    ];
    return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
         . ($paths[$name] ?? '') . '</svg>';
}

function page_head(string $title, string $subtitle = '', string $actions = ''): void
{
    echo '<div class="page-head"><div><h1>' . e($title) . '</h1>';
    if ($subtitle !== '') echo '<p class="subtitle">' . e($subtitle) . '</p>';
    echo '</div>';
    if ($actions !== '') echo '<div class="page-actions">' . $actions . '</div>';
    echo '</div>';
}

// ---- Login protection: every page requires a login unless it sets PUBLIC_PAGE first ----
require_once __DIR__ . '/auth.php';
if (!defined('PUBLIC_PAGE')) {
    require_login();
}
