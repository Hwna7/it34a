
<?php

// Start user session in the database
function startUserSession($pdo) {

    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        INSERT INTO user_session (
            user_id,
            session_start
        )
        VALUES (
            :user_id,
            NOW()
        )
    ");

    $stmt->execute([
        'user_id' => $user_id
    ]);

    // Save the database session ID
    $session_id = $pdo->lastInsertId();
    $_SESSION['session_id'] = $session_id;

    // Initialize last activity
    $_SESSION['last_activity'] = time();

    return $session_id;
}


// End user session in the database
function endUserSession($pdo) {

    if (
        !isset($_SESSION['user_id']) ||
        !isset($_SESSION['session_id'])
    ) {
        return false;
    }

    $session_id = $_SESSION['session_id'];

    $stmt = $pdo->prepare("
        UPDATE user_session
        SET
            session_end = NOW(),
            session_duration = TIMESTAMPDIFF(
                SECOND,
                session_start,
                NOW()
            )
        WHERE session_id = :session_id
          AND user_id = :user_id
          AND session_end IS NULL
    ");

    return $stmt->execute([
        'session_id' => $session_id,
        'user_id' => $_SESSION['user_id']
    ]);
}


// Check if the user has an active database session
function hasActiveUserSession($pdo, $user_id) {

    $stmt = $pdo->prepare("
        SELECT session_id
        FROM user_session
        WHERE user_id = :user_id
        AND session_end IS NULL
        LIMIT 1
    ");

    $stmt->execute([
        'user_id' => $user_id
    ]);

    return (bool) $stmt->fetchColumn();
}


// Check session timeout
function checkSessionTimeout() {
    global $pdo;

    // 10 seconds for testing
    // Change to 1800 for 30 minutes
    $timeout = 10;

    if (!isset($_SESSION['user_id'])) {
        return;
    }

    // Check the last activity time
    if (isset($_SESSION['last_activity'])) {

        $inactive = time() - $_SESSION['last_activity'];

        if ($inactive >= $timeout) {

            // End the database session
            endUserSession($pdo);

            // Destroy the PHP session
            $_SESSION = [];
            session_unset();
            session_destroy();

            // Redirect to login page
            header('Location: /index.php?timeout=1');
            exit();
        }
    }

    // Update activity after checking the timeout
    $_SESSION['last_activity'] = time();
}

?>
