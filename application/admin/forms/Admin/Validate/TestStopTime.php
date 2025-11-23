<?php

class Admin_Validate_TestStopTime extends Zend_Validate_Abstract
{
    public const WRONG_FORMAT = 'wrongFormat';
    public const PAST_TIME    = 'pastTime';
    public const BACKWARDS    = 'backwards';

    protected $_messageTemplates = array(
        self::WRONG_FORMAT => "Неверный формат даты и времени.",
        self::PAST_TIME    => "Вы пытаетесь указать время, которое прошло.",
        self::BACKWARDS    => "Дата конца теста не может быть меньше даты начала теста.",
    );

    /**
     * Original token against which to validate
     * @var string
     */
    protected $_startTime;

    /**
     * Sets validator options
     *
     * @param mixed $token
     */
    public function __construct($startTime)
    {
        $this->_startTime = $startTime;
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
    public function validateDate($stopTime, $format = 'd.m.Y H:i')
    {
        $d = DateTime::createFromFormat($format, $stopTime);
        return $d && $d->format($format) == $stopTime;
    }

    public function isValid($stopTime, $context = null)
    {
        $this->_setValue($stopTime);

        if (isset($context)) {
            $startTime = $context[$this->_startTime];
        } else {
            $startTime = $this->_startTime;
        }

        if (!$this->validateDate($stopTime)) {
            $this->_error(self::WRONG_FORMAT);
            return false;
        }

        // Validate start time
        if (!$this->validateDate($startTime)) {
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

        // Only check that stop time is after start time
        if ($startDate >= $stopDate) {
            $this->_error(self::BACKWARDS);
            return false;
        }

        return true;
    }
}
