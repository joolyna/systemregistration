<?php /* expects: $students, $events, $values, $error, $submitLabel */ ?>
<div class="card form-card">
    <?php if (!empty($error)): ?>
        <div class="alert alert-error alert-inline"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>

    <?php if (count($students) === 0 || count($events) === 0): ?>
        <div class="notice">
            <?= count($students) === 0 ? 'No students found. ' : '' ?>
            <?= count($events) === 0 ? 'No events found. ' : '' ?>
            Add them first before creating a registration.
        </div>
        <div class="form-actions" style="margin-top:0">
            <?php if (count($students) === 0): ?><a class="btn" href="add_student.php"><?= icon('plus') ?>Add student</a><?php endif; ?>
            <?php if (count($events) === 0): ?><a class="btn" href="add_event.php"><?= icon('plus') ?>Add event</a><?php endif; ?>
        </div>
    <?php else: ?>
    <form method="POST" action="">
        <div class="form-grid">
            <div class="field full">
                <label for="student_id">Student</label>
                <select id="student_id" name="student_id" required>
                    <option value="">Choose a student</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= (int) $s['student_id'] ?>"
                            <?= (string) $values['student_id'] === (string) $s['student_id'] ? 'selected' : '' ?>>
                            <?= e($s['student_name']) ?> (<?= e($s['course']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field full">
                <label for="event_id">Event</label>
                <select id="event_id" name="event_id" required>
                    <option value="">Choose an event</option>
                    <?php foreach ($events as $ev):
                        $left = max(0, (int) $ev['capacity'] - (int) $ev['taken']);
                        $selected = (string) $values['event_id'] === (string) $ev['event_id'];
                    ?>
                        <option value="<?= (int) $ev['event_id'] ?>"
                            <?= $selected ? 'selected' : '' ?>
                            <?= ($left === 0 && !$selected) ? 'disabled' : '' ?>>
                            <?= e($ev['event_name']) ?> &middot; <?= fmt_date($ev['event_date']) ?>
                            (<?= $left === 0 ? 'Full' : $left . ' seat' . ($left === 1 ? '' : 's') . ' left' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field full">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <?php foreach (STATUSES as $opt): ?>
                        <option value="<?= e($opt) ?>" <?= $values['status'] === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn"><?= e($submitLabel) ?></button>
            <a class="btn ghost" href="registrations.php">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</div>
