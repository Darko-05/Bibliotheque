<?php

    session_start();

    $_SESSION = [];

    session_destroy();

    setcookie(
        "email",
        "",
        [
            "expires" => time() - 3600,
            "secure" => true,
            "httponly" => true
        ]
    );

    header("Location: index.php");
    exit();