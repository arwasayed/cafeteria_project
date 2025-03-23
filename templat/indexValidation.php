<?php
class InputValidator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validatePassword($password) {
        // Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, and one number.
        $passwordRegex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/';
        return preg_match($passwordRegex, $password);
    }
}
?>