<?php namespace Dataloader\Validators;

class IsbnValidator extends Validator
{
    /**
     * Check if an ISBN is valid.
     *
     * @param  string $isbn
     * @return boolean
     */
    public function validate($isbn)
    {
        return $this->checkDigit($isbn);
    }

    /**
     * Verify is the ISBN contains 10 or 13 characters, otherwise returns false.
     *
     * @param  string $isbn
     * @return mixed
     */
    public function verifyScheme($isbn)
    {
        $isbn = $this->clean($isbn);

        if (preg_match('/\d{13}/i', $isbn)) {
            return 13;
        }

        if (preg_match('/\d{9}[0-9xX]/i', $isbn)) {
            return 10;
        }

        return false;
    }

    /**
     * Verify the checkDigit.
     *
     * @param string $isbn
     * @return boolean
     */
    public function checkDigit($isbn)
    {
        $isbn = $this->clean($isbn);

        // Validate the ISBN-13 checkDigit.
        if ($this->verifyScheme($isbn) == 13) {
            $check = 0;
            for ($i = 0; $i < 13; $i += 2) {
                $check += substr($isbn, $i, 1);
            }
            for ($i = 1; $i < 12; $i += 2) {
                $check += 3 * substr($isbn, $i, 1);
            }
            return $check % 10 === 0;
        }

        // Validate the ISBN-10 checkDigit.
        if ($this->verifyScheme($isbn) == 10) {
            $check = 0;
            for ($i = 0; $i < 10; $i++) {
                if ($isbn[$i] === 'X') {
                    $check += 10 * intval(10 - $i);
                } else {
                    $check += intval($isbn[$i]) * intval(10 - $i);
                }
            }
            return $check % 11 === 0;
        }

        return false;
    }
}
