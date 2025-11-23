<?php
// FILE: /app/helpers/ValidationHelper.php

class ValidationHelper {

    public static function required($value) {
        return !empty($value) || $value === '0' || $value === 0;
    }

    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function min($value, $min) {
        return strlen($value) >= $min;
    }

    public static function max($value, $max) {
        return strlen($value) <= $max;
    }

    public static function numeric($value) {
        return is_numeric($value);
    }

    public static function integer($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function inArray($value, $array) {
        return in_array($value, $array);
    }

    public static function url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function slug($slug) {
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug);
    }

    public static function alphanumeric($value) {
        return ctype_alnum($value);
    }

    public static function alpha($value) {
        return ctype_alpha($value);
    }

    public static function validateData($data, $rules) {
        $errors = array();

        foreach ($rules as $field => $fieldRules) {
            $value = isset($data[$field]) ? $data[$field] : null;
            $fieldRulesArray = explode('|', $fieldRules);

            foreach ($fieldRulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = isset($ruleParts[1]) ? $ruleParts[1] : null;

                switch ($ruleName) {
                    case 'required':
                        if (!self::required($value)) {
                            $errors[$field][] = ucfirst($field) . ' is required';
                        }
                        break;

                    case 'email':
                        if ($value && !self::email($value)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid email';
                        }
                        break;

                    case 'min':
                        if ($value && !self::min($value, $ruleParam)) {
                            $errors[$field][] = ucfirst($field) . " must be at least $ruleParam characters";
                        }
                        break;

                    case 'max':
                        if ($value && !self::max($value, $ruleParam)) {
                            $errors[$field][] = ucfirst($field) . " must not exceed $ruleParam characters";
                        }
                        break;

                    case 'numeric':
                        if ($value && !self::numeric($value)) {
                            $errors[$field][] = ucfirst($field) . ' must be numeric';
                        }
                        break;

                    case 'integer':
                        if ($value && !self::integer($value)) {
                            $errors[$field][] = ucfirst($field) . ' must be an integer';
                        }
                        break;

                    case 'in':
                        $allowedValues = explode(',', $ruleParam);
                        if ($value && !self::inArray($value, $allowedValues)) {
                            $errors[$field][] = ucfirst($field) . ' must be one of: ' . implode(', ', $allowedValues);
                        }
                        break;

                    case 'url':
                        if ($value && !self::url($value)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid URL';
                        }
                        break;

                    case 'slug':
                        if ($value && !self::slug($value)) {
                            $errors[$field][] = ucfirst($field) . ' must be a valid slug (lowercase, alphanumeric, hyphens)';
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    public static function sanitizeString($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public static function sanitizeUrl($url) {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    public static function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
}
