<?php

namespace App\Tests\Validator;

use App\Services\Password\PasswordStrength;
use PHPUnit\Framework\TestCase;

class PasswordStrengthTest extends TestCase
{
	public function testPasswordZero(): void
	{
		// Return 0
		$this->assertEquals(0, PasswordStrength::calculatePassword(''));
		$this->assertEquals(0, PasswordStrength::calculatePassword('t'));
		$this->assertEquals(0, PasswordStrength::calculatePassword('tt'));
		$this->assertEquals(0, PasswordStrength::calculatePassword('000'));
		$this->assertEquals(0, PasswordStrength::calculatePassword('tttt'));
		$this->assertEquals(0, PasswordStrength::calculatePassword('@@@@@'));
		$this->assertEquals(0, PasswordStrength::calculatePassword('tttttt'));
		$this->assertEquals(0, PasswordStrength::calculatePassword('TTTTTTT'));
	}

	public function testPasswordThree(): void
	{
		// Return 3
		$this->assertEquals(3, PasswordStrength::calculatePassword('tttttttt'));
		$this->assertEquals(3, PasswordStrength::calculatePassword('TTTTTTTT'));
		$this->assertEquals(3, PasswordStrength::calculatePassword('00000000'));
		$this->assertEquals(3, PasswordStrength::calculatePassword('@@@@@@@@'));
	}

	public function testPasswordFour(): void
	{
		// Return 4
		$this->assertEquals(4, PasswordStrength::calculatePassword('tttttttttttttttt'));
		$this->assertEquals(4, PasswordStrength::calculatePassword('TTTTTTTTTTTTTTTT'));
		$this->assertEquals(4, PasswordStrength::calculatePassword('0000000000000000'));
		$this->assertEquals(4, PasswordStrength::calculatePassword('@@@@@@@@@@@@@@@@'));
	}

	public function testPasswordFive(): void
	{
		// Return 5
		$this->assertEquals(5, PasswordStrength::calculatePassword('tttttttT'));
		$this->assertEquals(5, PasswordStrength::calculatePassword('TTTTTTTt'));
		$this->assertEquals(5, PasswordStrength::calculatePassword('0000000t'));
		$this->assertEquals(5, PasswordStrength::calculatePassword('@@@@@@@7'));
	}

	public function testPasswordSix(): void
	{
		// Return 6
		$this->assertEquals(6, PasswordStrength::calculatePassword('tttttttTtttttttT'));
		$this->assertEquals(6, PasswordStrength::calculatePassword('TTTTTTTtTTTTTTTt'));
		$this->assertEquals(6, PasswordStrength::calculatePassword('0000000t0000000t'));
		$this->assertEquals(6, PasswordStrength::calculatePassword('@@@@@@@7@@@@@@@7'));
	}

	public function testPasswordSeven(): void
	{
		// Return 7
		$this->assertEquals(7, PasswordStrength::calculatePassword('tttttt8T'));
		$this->assertEquals(7, PasswordStrength::calculatePassword('TTTTTT9t'));
		$this->assertEquals(7, PasswordStrength::calculatePassword('000000_t'));
		$this->assertEquals(7, PasswordStrength::calculatePassword('@@@@@@t7'));
	}

	public function testPasswordHeight(): void
	{
		// Return 8
		$this->assertEquals(8, PasswordStrength::calculatePassword('tttttt8Ttttttt8T'));
		$this->assertEquals(8, PasswordStrength::calculatePassword('TTTTTT9tTTTTTT9t'));
		$this->assertEquals(8, PasswordStrength::calculatePassword('000000_t000000_t'));
		$this->assertEquals(8, PasswordStrength::calculatePassword('@@@@@@t7@@@@@@t7'));
	}

	public function testPasswordNine(): void
	{
		// Return 9
		$this->assertEquals(9, PasswordStrength::calculatePassword('@ttttt8T'));
		$this->assertEquals(9, PasswordStrength::calculatePassword('#TTTTT9t'));
		$this->assertEquals(9, PasswordStrength::calculatePassword('A00000_t'));
		$this->assertEquals(9, PasswordStrength::calculatePassword('@@B@@@t7'));
	}

	public function testPasswordTeen(): void
	{
		// Return 10
		$this->assertEquals(10, PasswordStrength::calculatePassword('@ttttt8T@ttttt8T'));
		$this->assertEquals(10, PasswordStrength::calculatePassword('#TTTTT9t#TTTTT9t'));
		$this->assertEquals(10, PasswordStrength::calculatePassword('A00000_tA00000_t'));
		$this->assertEquals(10, PasswordStrength::calculatePassword('@@B@@@t7@@B@@@t7'));
	}

	public function testLongPasswordWithVariants(): void
	{
		// More than 16 character
		$this->assertEquals(4, PasswordStrength::calculatePassword('tttttttttttttttttt'));
		$this->assertEquals(6, PasswordStrength::calculatePassword('AtAtAtAtAtAtAtAtAtAtAtAt'));
		$this->assertEquals(8, PasswordStrength::calculatePassword('A@pA@pA@pA@pA@pA@pA@pA@pA@p'));
		$this->assertEquals(10, PasswordStrength::calculatePassword('Ap8_Ap9_Ap8_Ap9_Ap8_Ap9_'));
	}
}
