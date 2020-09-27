<?php

/*
 * This file is part of Contao.
 *
 * (c) Leo Feyer
 *
 * @license LGPL-3.0-or-later
 */

namespace Markocupic\FormMultitextfieldBundle\Forms;

use Contao\FormTextField;
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
class FormMultitextField extends FormTextField
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


}