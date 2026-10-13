CREATE TABLE user_session(
    session_id INT AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,

    session_start DATETIME NOT NULL DEFAULT CURENT_TIMESTAMP,
    session_end DATETIME DEFAULT NULL,
    session_duration INT DEFAULT NULL.

    CONSTRAINT fk_user_sessions_user_id
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
)