<?php


namespace App\Services\Password;


class PasswordStrength
{
	/**
	 * The level zero of password strength
	 */
	const PASSWORD_STRENGTH_ZERO = 0;

	/**
	 * The low level of password strength
	 */
	const PASSWORD_STRENGTH_LOW = 1;

	/**
	 * The high level of password strength
	 */
	const PASSWORD_STRENGTH_HIGH = 2;

	/**
	 * @param string $value Waits for a string to check for lower characters
	 * @return bool Return true when Regex match lower characters
	 */
	public static function hasLower(string $value): bool
	{
		return preg_match('/[a-z]+/', $value);
	}

	/**
	 * @param string $value Waits for a string to check for upper characters
	 * @return bool Return true when Regex match upper characters
	 */
	public static function hasUpper(string $value): bool
	{
		return preg_match('/[A-Z]+/', $value);
	}

	/**
	 * @param string $value Waits for a string to check for digital characters
	 * @return bool Return true when Regex match digital characters
	 */
	public static function hasDigit(string $value): bool
	{
		return preg_match('/[0-9]+/', $value);
	}

	/**
	 * @param string $value Waits for a string to check for special characters
	 * @return bool Return true when Regex match special characters
	 */
	public static function hasSpecial(string $value): bool
	{
		return preg_match('/[\@\#\&\_\-\$\~]+/', $value);
	}

	/**
	 * @param string $string The string of the password for strength check
	 * @return int The password strength value
	 */
	public static function calculatePassword(string $string): int
	{
		// Init result to 0
		$result = 0;
		// Calculate password strength
		$result += strlen($string) >= 8 ? self::PASSWORD_STRENGTH_LOW : self::PASSWORD_STRENGTH_ZERO;
		$result += strlen($string) >= 16 ? self::PASSWORD_STRENGTH_LOW : self::PASSWORD_STRENGTH_ZERO;
		if ($result !== self::PASSWORD_STRENGTH_ZERO) {
			$result += self::hasLower($string) ? self::PASSWORD_STRENGTH_HIGH : self::PASSWORD_STRENGTH_ZERO;
			$result += self::hasUpper($string) ? self::PASSWORD_STRENGTH_HIGH : self::PASSWORD_STRENGTH_ZERO;
			$result += self::hasDigit($string) ? self::PASSWORD_STRENGTH_HIGH : self::PASSWORD_STRENGTH_ZERO;
			$result += self::hasSpecial($string) ? self::PASSWORD_STRENGTH_HIGH : self::PASSWORD_STRENGTH_ZERO;
		}

		return $result;
	}
}