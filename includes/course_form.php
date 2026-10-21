<?php /* expects: $values, $error, $submitLabel, $colleges (array) */ ?>
<div class="card form-card">
    <?php if (!empty($error)): ?>
        <div class="alert alert-error alert-inline"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-grid">
            <div class="field full">
                <label for="course_name">Course name</label>
                <input type="text" id="course_name" name="course_name" maxlength="80"
                       placeholder="e.g. Information Technology" value="<?= e($values['course_name']) ?>" required>
            </div>
            <div class="field full">
                <label for="college">College</label>
                <input type="text" id="college" name="college" maxlength="100" list="college-list"
                       placeholder="Pick an existing college or type a new one" value="<?= e($values['college']) ?>" required>
                <datalist id="college-list">
                    <?php foreach ($colleges as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?>
                </datalist>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn"><?= e($submitLabel) ?></button>
            <a class="btn ghost" href="courses.php">Cancel</a>
        </div>
    </form>
</div>
