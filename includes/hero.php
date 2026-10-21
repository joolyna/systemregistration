<?php require_once __DIR__ . '/art.php'; ?>
<section class="hero">
    <?= hero_art() ?>
    <div class="hero-copy">
        <span class="hero-tag"><?= icon('calendar') ?>Student portal</span>
        <h1>Manage School<br>Events Easily</h1>
        <p>Register students, track seats and mark attendance for every school event, all in one friendly place.</p>
        <a class="btn outline" href="events.php">Browse events</a>
    </div>
</section>

<form class="quick-search" id="quickSearch" method="GET" action="students.php" role="search">
    <div class="qs-fields">
        <div class="qs-field qs-grow">
            <?= icon('search') ?>
            <input type="search" name="q" placeholder="Search students, events, courses..." aria-label="Search">
        </div>
        <div class="qs-field qs-select">
            <select id="qsTarget" aria-label="Search in">
                <option value="students.php">Students</option>
                <option value="events.php">Events</option>
                <option value="courses.php">Courses</option>
                <option value="registrations.php">Registrations</option>
            </select>
        </div>
    </div>
    <button class="btn dark" type="submit">Search</button>
</form>
<script>
    document.getElementById('qsTarget').addEventListener('change', function () {
        document.getElementById('quickSearch').action = this.value;
    });
</script>
