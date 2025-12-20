<?php
class Validator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePhone($phone) {
        return preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone);
    }
    
    public static function validateNumbers($numbers) {
        if (!is_array($numbers) || count($numbers) !== 5) {
            return false;
        }
        
        foreach ($numbers as $num) {
            if (!is_numeric($num) || $num < 1 || $num > 75) {
                return false;
            }
        }
        
        return count(array_unique($numbers)) === 5; // No duplicates
    }
    
    public static function validateBonusNumbers($bonusNumbers) {
        if (!is_array($bonusNumbers) || count($bonusNumbers) !== 2) {
            return false;
        }
        
        foreach ($bonusNumbers as $num) {
            if (!is_numeric($num) || $num < 1 || $num > 75) {
                return false;
            }
        }
        
        return count(array_unique($bonusNumbers)) === 2; // No duplicates
    }
    
    public static function sanitizeString($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
?>