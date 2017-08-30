<?php namespace Dataloader\Validators;

class IssnValidator extends Validator
{
    /**
     * Check if an ISSN is valid.
     * If the string without dashes is shorter or longer than 8 characters,
     * don't even trigger the check digit test and just return false.
     *
     * @param  string $issn
     * @return boolean
     */
    public function validate(string $issn) : bool
    {
        if (strlen($this->clean($issn)) != 8) return false;

        return $this->checkDigit($issn) == $this->lastDigit($issn);
    }

    /**
     * Calculate the ISSN check digit.
     * Procedure: https://www.loc.gov/issn/check.html
     *
     * @param  string $issn
     * @return string
     */
    public function checkDigit(string $issn) : string
    {
        $digits = str_split($this->clean($issn));

        $index = 0;
        $weightingFactor = 8;
        $sum = 0;
        while ($index < 7) {
            $sum += $digits[$index++] * $weightingFactor--;
        }

        $remainder = $sum % 11;
        $checkDigit = 11 - $remainder;

        if ($checkDigit == 10) return 'X';
        return $checkDigit;
    }

    /**
     * Return the last digit of the given ISSN.
     *
     * @param  string $issn
     * @return string
     */
    public function lastDigit(string $issn) : string
    {
        $digits = str_split($this->clean($issn));
        return $digits[7];
    }
}
