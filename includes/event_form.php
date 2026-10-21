<?php /* expects: $values, $error, $submitLabel */ ?>
<div class="card form-card">
    <?php if (!empty($error)): ?>
        <div class="alert alert-error alert-inline"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-grid">
            <div class="field full">
                <label for="event_name">Event name</label>
                <input type="text" id="event_name" name="event_name" maxlength="150"
                       placeholder="e.g. IT Week 2026" value="<?= e($values['event_name']) ?>" required>
            </div>
            <div class="field">
                <label for="event_date">Event date</label>
                <input type="date" id="event_date" name="event_date" value="<?= e($values['event_date']) ?>" required>
            </div>
            <div class="field">
                <label for="capacity">Capacity (seats)</label>
                <input type="number" id="capacity" name="capacity" min="1" max="10000"
                       value="<?= e($values['capacity']) ?>" required>
            </div>
            <div class="field full">
                <label for="venue">Venue <span style="color:var(--muted);font-weight:500">(optional)</span></label>
                <input type="text" id="venue" name="venue" maxlength="150"
                       placeholder="e.g. Main Auditorium" value="<?= e($values['venue']) ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn"><?= e($submitLabel) ?></button>
            <a class="btn ghost" href="events.php">Cancel</a>
        </div>
    </form>
</div>
