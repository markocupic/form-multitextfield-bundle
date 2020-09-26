<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Markocupic\FormMultitextfieldBundle\Forms;

use Contao\Widget;
use Contao\Idna;
use Contao\StringUtil;

/**
 * Provide methods to handle text fields.
 *
 * @property integer $maxlength
 * @property boolean $mandatory
 * @property string $placeholder
 * @property boolean $multiple
 * @property boolean $hideInput
 * @property integer $size
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class FormMultitextField extends Widget
{

    /**
     * Submit user input
     * @var boolean
     */
    protected $blnSubmitInput = true;

    /**
     * Add a for attribute
     * @var boolean
     */
    protected $blnForAttribute = true;

    /**
     * Template
     * @var string
     */
    protected $strTemplate = 'form_multitextfield';

    /**
     * The CSS class prefix
     *
     * @var string
     */
    protected $strPrefix = 'widget widget-multitext';

    /**
     * Disable the for attribute if the "multiple" option is set
     *
     * @param array $arrAttributes
     */
    public function __construct($arrAttributes = null)
    {
        parent::__construct($arrAttributes);

        if ($this->multiple)
        {
            $this->blnForAttribute = false;
        }

        $this->addRowLbl = $GLOBALS['TL_LANG']['MSC']['ffl_multitextfield_addRowLbl'];
        $this->deleteRowLbl = $GLOBALS['TL_LANG']['MSC']['ffl_multitextfield_deleteRowLbl'];

    }

    /**
     * Add specific attributes
     *
     * @param string $strKey The attribute key
     * @param mixed $varValue The attribute value
     */
    public function __set($strKey, $varValue)
    {
        switch ($strKey)
        {
            case 'minlength':
                if ($varValue > 0 && $this->rgxp != 'digit')
                {
                    $this->arrAttributes['minlength'] = $varValue;
                }
                break;

            case 'maxlength':
                if ($varValue > 0 && $this->rgxp != 'digit')
                {
                    $this->arrAttributes['maxlength'] = $varValue;
                }
                break;

            case 'mandatory':
                if ($varValue)
                {
                    $this->arrAttributes['required'] = 'required';
                }
                else
                {
                    unset($this->arrAttributes['required']);
                }
                parent::__set($strKey, $varValue);
                break;

            case 'min':
            case 'minval':
                if ($this->rgxp == 'digit')
                {
                    $this->arrAttributes['min'] = $varValue;
                }
                break;

            case 'max':
            case 'maxval':
                if ($this->rgxp == 'digit')
                {
                    $this->arrAttributes['max'] = $varValue;
                }
                break;

            case 'step':
                if ($varValue > 0 && $this->type == 'number')
                {
                    $this->arrAttributes[$strKey] = $varValue;
                }
                else
                {
                    unset($this->arrAttributes[$strKey]);
                }
                break;

            case 'placeholder':
                $this->arrAttributes[$strKey] = $varValue;
                break;

            default:
                parent::__set($strKey, $varValue);
                break;
        }
    }

    /**
     * Return a parameter
     *
     * @param string $strKey The parameter key
     *
     * @return mixed The parameter value
     */
    public function __get($strKey)
    {
        switch ($strKey)
        {
            case 'value':
                // Hide the Punycode format (see #2750)
                if ($this->rgxp == 'url')
                {
                    try
                    {
                        return Idna::decodeUrl($this->varValue);
                    } catch (\InvalidArgumentException $e)
                    {
                        return $this->varValue;
                    }
                }
                elseif ($this->rgxp == 'email' || $this->rgxp == 'friendly')
                {
                    return Idna::decodeEmail($this->varValue);
                }

                return $this->varValue;

            case 'type':
                if ($this->hideInput)
                {
                    return 'password';
                }

                // Use the HTML5 types (see #4138) but not the date, time and datetime types (see #5918)
                switch ($this->rgxp)
                {
                    case 'digit':
                        // Allow floats (see #7257)
                        if (!isset($this->arrAttributes['step']))
                        {
                            $this->addAttribute('step', 'any');
                        }
                    // no break

                    case 'natural':
                        return 'number';

                    case 'phone':
                        return 'tel';

                    case 'email':
                        return 'email';

                    case 'url':
                        return 'url';
                }

                return 'text';

            default:
                return parent::__get($strKey);
        }
    }

    /**
     * Trim the values
     *
     * @param mixed $varInput The user input
     *
     * @return mixed The validated user input
     */
    protected function validator($varInput)
    {
        if (\is_array($varInput))
        {
            return parent::validator($varInput);
        }

        // Convert to Punycode format (see #5571)
        if ($this->rgxp == 'url')
        {
            try
            {
                $varInput = Idna::encodeUrl($varInput);
            } catch (\InvalidArgumentException $e)
            {
            }
        }
        elseif ($this->rgxp == 'email' || $this->rgxp == 'friendly')
        {
            $varInput = Idna::encodeEmail($varInput);
        }

        return parent::validator($varInput);
    }


    /**
     * Generate the widget and return it as string
     *
     * @return string
     */
    public function generate()
    {

        $strType = $this->hideInput ? 'password' : 'text';

        if (!$this->multiple)
        {
            // Hide the Punycode format (see #2750)
            if ($this->rgxp === 'url')
            {
                try
                {
                    $this->varValue = Idna::decodeUrl($this->varValue);
                } catch (\InvalidArgumentException $e)
                {
                }
            }
            elseif ($this->rgxp === 'email' || $this->rgxp === 'friendly')
            {
                $this->varValue = Idna::decodeEmail($this->varValue);
            }

            return sprintf('<input type="%s" name="%s" id="ctrl_%s" class="tl_text%s" value="%s"%s onfocus="Backend.getScrollOffset()">%s',
                $strType,
                $this->strName,
                $this->strId,
                (($this->strClass != '') ? ' ' . $this->strClass : ''),
                StringUtil::specialchars($this->varValue),
                $this->getAttributes(),
                $this->wizard);
        }

        // Return if field size is missing
        if (!$this->size)
        {
            return '<p>Field size is missing</p>';
        }

        if (!\is_array($this->varValue))
        {
            $this->varValue = array($this->varValue);
        }

        $arrFields = array();

        for ($i = 0; $i < $this->size; $i++)
        {
            $arrFields[] = sprintf('<input type="%s" name="%s[]" id="ctrl_%s" class="tl_text_%s" value="%s"%s onfocus="Backend.getScrollOffset()">',
                $strType,
                $this->strName,
                $this->strId . '_' . $i,
                $this->size,
                StringUtil::specialchars(@$this->varValue[$i]), // see #4979
                $this->getAttributes());
        }

        return sprintf('<div id="ctrl_%s" class="tl_text_field%s">%s</div>%s',
            $this->strId,
            (($this->strClass != '') ? ' ' . $this->strClass : ''),
            implode(' ', $arrFields),
            $this->wizard);
    }
}