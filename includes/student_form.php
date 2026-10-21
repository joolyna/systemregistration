<?php /* expects: $values (array), $error (string), $submitLabel, $studentId (optional) */ ?>
<div class="card form-card">
    <?php if (!empty($error)): ?>
        <div class="alert alert-error alert-inline"><?= icon('alert') ?><span><?= e($error) ?></span></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-grid">
            <div class="field full">
                <label for="student_name">Full name</label>
                <input type="text" id="student_name" name="student_name" maxlength="120"
                       placeholder="e.g. Maria Santos" value="<?= e($values['student_name']) ?>" required>
            </div>
            <div class="field">
                <label for="course">Course</label>
                <select id="course" name="course" required>
                    <option value="">Select course</option>
                    <?php foreach (course_groups($db) as $college => $programs): ?>
                        <optgroup label="<?= e($college) ?>">
                            <?php foreach ($programs as $program): ?>
                                <option value="<?= e($program) ?>" <?= $values['course'] === $program ? 'selected' : '' ?>><?= e($program) ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                    <?php if ($values['course'] !== '' && !in_array($values['course'], all_courses($db), true)): ?>
                        <optgroup label="Current value">
                            <option value="<?= e($values['course']) ?>" selected><?= e($values['course']) ?></option>
                        </optgroup>
                    <?php endif; ?>
                </select>
            </div>
            <div class="field">
                <label for="year_level">Year level</label>
                <select id="year_level" name="year_level" required>
                    <option value="">Select year level</option>
                    <?php foreach (YEAR_LEVELS as $y): ?>
                        <option value="<?= e($y) ?>" <?= $values['year_level'] === $y ? 'selected' : '' ?>><?= e($y) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field full">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" maxlength="150"
                       placeholder="name@school.edu" value="<?= e($values['email']) ?>" required>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn"><?= e($submitLabel) ?></button>
            <a class="btn ghost" href="students.php">Cancel</a>
        </div>
    </form>
</div>
