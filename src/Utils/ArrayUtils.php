<?php


namespace App\Utils;


use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class ArrayUtils
{
	/**
	 * A function that generates from a given array, a new array by randomly taking the starting values.
	 * @param array $array The starting array used by the random picker
	 * @param int $number The number of returned values
	 *
	 * @return array Returns an array with the selected number of values, an exception is returned when the number is greater than the array length
	 */
	public static function arrayRand(array $array, int $number = 1): array
	{
		// When number chosen is 0, return an empty array
		if ($number === 0) return [];
		// When number is greater than array, return false
		if (count($array) < $number) throw new UnexpectedValueException("The number is greater than array values");
		// Generate array with random values
		$newArray = [];
		for($i = 0; $i < $number; $i++) {
			array_push($newArray, $array[array_rand($array)]);
			$newArray = array_unique($newArray);
			if ($i >= count($newArray)) $i--;
		}
		// Return generated array
		return $newArray;
	}
}
