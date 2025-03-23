<?php
class ResetPasswordValidation {
    public static function validatePasswords($newPassword, $confirmPassword) {
        if ($newPassword !== $confirmPassword) {
            return "Passwords do not match.";
        }

        // Add password strength validation
        $passwordRegex = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/';
        if (!preg_match($passwordRegex, $newPassword)) {
            return "Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.";
        }

        return ""; // No errors
    }
}
?>