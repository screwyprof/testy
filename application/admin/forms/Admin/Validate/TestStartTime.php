<?php

class Admin_Validate_TestStartTime extends Zend_Validate_Abstract
{
    const WRONG_FORMAT = 'wrongFormat';
    const PAST_TIME    = 'pastTime';
    const BACKWARDS    = 'backwards';

    protected $_messageTemplates = array(
        self::WRONG_FORMAT => "Неверный формат даты и времени.",
        self::PAST_TIME    => "Вы пытаетесь указать время, которое прошло.",
        self::BACKWARDS    => "Время начала теста не может быть позже даты конца теста.",
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $_stopTime;

    /**
     * Sets validator options
     *
     * @param mixed $token
     */
    public function __construct($stopTime)
    {
        $this->_stopTime = $stopTime;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if a token has been set and the provided value
     * matches that token.
     *
     * @param  string $value
     * @param  string $context
     * @return boolean
     */
    public function validateDate($startTime, $format = 'd.m.Y H:i')
    {
        $d = DateTime::createFromFormat($format, $startTime);
        return $d && $d->format($format) == $startTime;
    }

    public function isValid($startTime, $context = null)
    {
        $this->_setValue($startTime);

        if (isset($context)) {
            $stopTime = $context[$this->_stopTime];
        } else {
            $stopTime = $this->_stopTime;
        }

        if (!$this->validateDate($startTime)) {
            $this->_error(self::WRONG_FORMAT);
            return false;
        }

        // Validate stop time
        if (!$this->validateDate($stopTime)) {
            $this->_error(self::WRONG_FORMAT);
            return false;
        }

        // Parse dates safely
        $startDate = DateTime::createFromFormat('d.m.Y H:i', $startTime);
        $stopDate = DateTime::createFromFormat('d.m.Y H:i', $stopTime);

        if (!$startDate || !$stopDate) {
            $this->_error(self::WRONG_FORMAT);
            return false;
        }

        // Only check that start time is before stop time
        if ($startDate >= $stopDate) {
            $this->_error(self::BACKWARDS);
            return false;
        }

        return true;
    }
}
