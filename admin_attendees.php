<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
check_auth(true);

if (empty($_SESSION['attendee_csrf'])) {
    $_SESSION['attendee_csrf'] = bin2hex(random_bytes(32));
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['attendee_csrf'], $token)) {
        $error = 'Your session expired. Refresh the page and try again.';
    } else {
        $action = $_POST['action'] ?? '';
        try {
            if ($action === 'save_attendee') {
                $email = strtolower(trim($_POST['email'] ?? ''));
                $status = $_POST['status'] ?? 'unverified';
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
                    $error = 'Please enter a valid email address.';
                } elseif (!in_array($status, ['verified', 'unverified'], true)) {
                    $error = 'Please select a valid verification status.';
                } else {
                    $verifiedAt = $status === 'verified' ? date('Y-m-d H:i:s') : null;
                    $stmt = $db->prepare(
                        "INSERT INTO attendee_eligibility (email, status, verified_at)
                         VALUES (?, ?, ?)
                         ON DUPLICATE KEY UPDATE status = VALUES(status),
                         verified_at = VALUES(verified_at)"
                    );
                    $stmt->execute([$email, $status, $verifiedAt]);
                    $success = 'Attendee email saved successfully.';
                }
            } elseif ($action === 'update_status') {
                $id = (int) ($_POST['id'] ?? 0);
                $status = $_POST['status'] ?? '';
                if ($id < 1 || !in_array($status, ['verified', 'unverified'], true)) {
                    $error = 'Invalid attendee or status.';
                } else {
                    $verifiedAt = $status === 'verified' ? date('Y-m-d H:i:s') : null;
                    $stmt = $db->prepare("UPDATE attendee_eligibility SET status = ?, verified_at = ? WHERE id = ?");
                    $stmt->execute([$status, $verifiedAt, $id]);
                    $success = 'Attendee verification status updated.';
                }
            } elseif ($action === 'delete_attendee') {
                $id = (int) ($_POST['id'] ?? 0);
                if ($id < 1) {
                    $error = 'Invalid attendee record.';
                } else {
                    $stmt = $db->prepare("DELETE FROM attendee_eligibility WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Attendee email removed.';
                }
            } else {
                $error = 'Unknown request.';
            }
        } catch (PDOException $e) {
            error_log('Attendee eligibility admin error: ' . $e->getMessage());
            $error = 'Could not save the attendee record. Please try again.';
        }
    }
}

$attendees = $db->query("SELECT id, email, status, verified_at, created_at FROM attendee_eligibility ORDER BY created_at DESC")->fetchAll();
require_once __DIR__ . '/includes/header.php';
?>

<main class="mx-auto max-w-6xl px-4 sm:px-6 py-10">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-black uppercase tracking-widest text-emerald-500">AWS Student Community Day 2026</p>
            <h1 class="text-3xl font-black mt-2">Verified Attendees</h1>
            <p class="text-sm opacity-70 mt-2">Only email addresses marked as verified can generate an attendee card.</p>
        </div>
        <a href="admin.php" class="rounded-xl border border-slate-300 dark:border-white/20 px-4 py-2 text-sm font-bold">Back to Admin</a>
    </div>

    <?php if ($success): ?>
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 text-emerald-600 dark:text-emerald-300 p-4 mb-6 text-sm font-bold"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 text-red-600 dark:text-red-300 p-4 mb-6 text-sm font-bold"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <section class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.02] p-6 mb-8">
        <h2 class="text-lg font-black mb-4">Add or update attendee email</h2>
        <form method="post" class="grid gap-4 md:grid-cols-[1fr_220px_auto] items-end">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['attendee_csrf'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="action" value="save_attendee">
            <label class="block text-xs font-bold">
                Email address
                <input type="email" name="email" required maxlength="254" placeholder="attendee@example.com"
                    class="mt-2 w-full rounded-xl border border-slate-300 dark:border-white/15 bg-transparent px-4 py-3 text-sm">
            </label>
            <label class="block text-xs font-bold">
                Payment / verification status
                <select name="status" class="mt-2 w-full rounded-xl border border-slate-300 dark:border-white/15 bg-transparent px-4 py-3 text-sm">
                    <option value="verified">Verified / Paid</option>
                    <option value="unverified">Unverified / Not paid</option>
                </select>
            </label>
            <button type="submit" class="rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-3 text-sm">Save attendee</button>
        </form>
        <p class="text-xs opacity-60 mt-3">If the email already exists, its status will be updated instead of creating a duplicate.</p>
    </section>

    <section class="rounded-3xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-white/10 flex items-center justify-between gap-3">
            <h2 class="text-lg font-black">Attendee email records</h2>
            <span class="text-xs opacity-60"><?= count($attendees) ?> record(s)</span>
        </div>
        <?php if (!$attendees): ?>
            <p class="p-6 text-sm opacity-70">No attendee emails have been added yet.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-100 dark:bg-white/5 text-xs uppercase tracking-wider">
                        <tr><th class="p-4">Email</th><th class="p-4">Status</th><th class="p-4">Verified at</th><th class="p-4">Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($attendees as $attendee): ?>
                        <tr class="border-t border-slate-200 dark:border-white/10">
                            <td class="p-4 font-semibold"><?= htmlspecialchars($attendee['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="p-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold <?= $attendee['status'] === 'verified' ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-300' : 'bg-amber-500/15 text-amber-700 dark:text-amber-300' ?>">
                                    <?= htmlspecialchars(ucfirst($attendee['status']), ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td class="p-4 text-xs opacity-70"><?= $attendee['verified_at'] ? htmlspecialchars($attendee['verified_at'], ENT_QUOTES, 'UTF-8') : '—' ?></td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-2">
                                    <form method="post">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['attendee_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?= (int) $attendee['id'] ?>">
                                        <input type="hidden" name="status" value="<?= $attendee['status'] === 'verified' ? 'unverified' : 'verified' ?>">
                                        <button class="rounded-lg border border-slate-300 dark:border-white/20 px-3 py-2 text-xs font-bold"><?= $attendee['status'] === 'verified' ? 'Mark unverified' : 'Mark verified' ?></button>
                                    </form>
                                    <form method="post" onsubmit="return confirm('Remove this attendee email?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['attendee_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="action" value="delete_attendee">
                                        <input type="hidden" name="id" value="<?= (int) $attendee['id'] ?>">
                                        <button class="rounded-lg border border-red-400 text-red-600 dark:text-red-300 px-3 py-2 text-xs font-bold">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
