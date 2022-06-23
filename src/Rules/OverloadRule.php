<?php

namespace MultihandED\Overload\Rules;

use Illuminate\Contracts\Validation\Rule;
use MultihandED\Overload\Overload;

class OverloadRule implements Rule
{
    const FUNCTION_NAME = "/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/";
    const FIRST_LETTER_OF_CLASSNAME = "/^[A-Z]/";

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $segments = explode('\\', $value);

        //? First, the user needs to make sure that he enters the parameters correctly in order to avoid possible misunderstandings.
        foreach($segments as $key => $segment)
        {
            if(!preg_match(self::FUNCTION_NAME, $segment))
            {
                $this->message = 'Invalid segments in namespace. Check PSR-0 and PSR-4 standards';

                return false;
            }
            else if($key == (count($segments) - 1) && !preg_match(self::FIRST_LETTER_OF_CLASSNAME, substr($segment, 0)))
            {
                $this->message = 'The trait name must start with a capital letter in the range A - Z';

                return false;
            }
        }

        if(count($segments) == 1)
        {
            $this->message = 'To avoid unplanned overwriting of trait files, use more than one segment in the namespace';

            return false;
        }

        if(Overload::checkTraitExist($value))
        {
            $this->message = 'Such a trait already exists in the given namespace';
            
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
