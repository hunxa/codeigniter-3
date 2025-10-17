<?php

declare(strict_types=1);

/**
 * Environment Helper
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		CodeIgniter Dev Team
 * @link		https://codeigniter.com/userguide3/helpers/env_helper.html
 */

if (!function_exists('env')) {
    /**
     * Get an environment variable value
     *
     * @param string $key The environment variable key
     * @param mixed $default The default value if the key is not found
     * @return mixed The environment variable value or default
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        if ($value === false) {
            return $default;
        }

        // Convert string representations of boolean values
        if (in_array(strtolower($value), ['true', 'false'], true)) {
            return strtolower($value) === 'true';
        }

        // Convert string representations of null
        if (strtolower($value) === 'null') {
            return null;
        }

        // Convert string representations of arrays (comma-separated values)
        if (str_contains($value, ',')) {
            return array_map('trim', explode(',', $value));
        }

        return $value;
    }
}

if (!function_exists('env_bool')) {
    /**
     * Get an environment variable as boolean
     *
     * @param string $key The environment variable key
     * @param bool $default The default boolean value
     * @return bool
     */
    function env_bool(string $key, bool $default = false): bool
    {
        $value = env($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['true', '1', 'yes', 'on'], true);
    }
}

if (!function_exists('env_int')) {
    /**
     * Get an environment variable as integer
     *
     * @param string $key The environment variable key
     * @param int $default The default integer value
     * @return int
     */
    function env_int(string $key, int $default = 0): int
    {
        $value = env($key, $default);

        if (is_int($value)) {
            return $value;
        }

        return (int) $value;
    }
}

if (!function_exists('env_array')) {
    /**
     * Get an environment variable as array
     *
     * @param string $key The environment variable key
     * @param array $default The default array value
     * @return array
     */
    function env_array(string $key, array $default = []): array
    {
        $value = env($key, $default);

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && str_contains($value, ',')) {
            return array_map('trim', explode(',', $value));
        }

        return $default;
    }
}
