<?php
require_once '../../config/config.php';

requireRole('admin');

$section = $_GET ['section'] ?? 'activity-logs';
$action = $_GET ['action'] ?? '';

#Query #3 Get All Activity Logs
$stmt = $pdo->query("
    SELECT *
    FROM activity_logs
    ORDER BY activity_log_created_at DESC
");

$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($action == 'export'){
    if($section == 'activity-logs'){
    $stmt = $pdo->query("
    SELECT 
        activity_log_id,
        user_id,
        user_email,
        activity_log_action,
        activity_log_status,
        activity_log_ip_address,
        activity_log_user_agents,
        activity_log_created_at
    FROM activity_logs
    ORDER BY activity_log_created_at
    ");
    
  $rows = $stmt->fetchALL(PDO::FETCH_ASSOC);

  logActivity(
    $pdo,
    $_SESSION['user_id'],
    $_SESSION['user_email'],
    'export_activity_logs',
    'success'
  );

  exportCsv(
    'activity_logs',
    [
        'Record ID', 'User_ID', 'User_Email', 'Action',
        'Status', 'IP Address', 'User Agent', 'Date & Time',
    ],
    $rows
  );

    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity logs</title>
</head>
<body>
    <h1>Hello Admin</h1>
    <a href="../auth/signout.php">Sign Out</a>

    <?php if($section === 'activity-logs'):?>

        <h4>Activity Logs
            <a href="index.php?section=activity-logs&amp;action=export"> Export CSV</a>
        </h4>
    <table id="Example" class="table-stripped" style = "width:auto">
        <thead>
            <tr>
                <th>Record ID</th>
                <th>User ID</th>
                <th>Email</th>
                <th>Action</th>
                <th>Status</th>
                <th>IP Address</th>
                <th>User Agents</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($activities as $activity): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars(
                            $activity['activity_log_id']
                        )?>
                    </td>
                    <td>
                        <?= htmlspecialchars(
                            $activity['user_id']
                        )?>
                    </td>
                    <td>
                        <?= htmlspecialchars(
                            $activity['user_email']
                        )?>
                    </td>
                    <td>
                        <?= htmlspecialchars(
                            $activity['activity_log_action']
                        )?>
                    </td>
                    <td>
                        <?= htmlspecialchars(
                            $activity['activity_log_status']
                        )?>
                    </td>
                    <td>
                        <?= htmlspecialchars(
                            $activity['activity_log_ip_address']
                        )?>
                    </td>
                    <td>
                        <?= htmlspecialchars(
                            $activity['activity_log_user_agent']
                        )?>
                    </td>
                    <td>
                        <?= htmlspecialchars(
                            $activity['activity_log_created_at']
                        )?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    

    
</body>
</html>