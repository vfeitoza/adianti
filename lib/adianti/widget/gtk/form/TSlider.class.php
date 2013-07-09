<?php
/**
 * Slider Widget
 *
 * @version    1.0
 * @package    widget_gtk
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSlider extends GtkHScale
{
    private $wname;
    private $validations;
    protected $formName;
    
    /**
     * Class Constructor
     * @param  $name Field Name
     */
    public function __construct($name)
    {
        parent::__construct();
        parent::set_size_request(200, -1);
        $this->wname = $name;
        $this->validations = array();
    }
    
    /**
     * Define the widget's name 
     * @param $name Widget's Name
     */
    public function setName($name)
    {
        $this->wname = $name;
    }
    
    /**
     * Returns the name of the widget
     */
    public function getName()
    {
        return $this->wname;
    }
    
    /**
     * Define the widget's content
     * @param  $value  widget's content
     */
    public function setValue($value)
    {
        parent::set_value($value);
    }

    /**
     * Return the widget's content
     */
    public function getValue()
    {
        return parent::get_value();
    }
    
    /**
     * Define the name of the form to wich the button is attached
     * @param $name    A string containing the name of the form
     * @ignore-autocomplete on
     */
    public function setFormName($name)
    {
        $this->formName = $name;
    }
    
    /**
     * Define the field's range
     * @param $min Minimal value
     * @param $max Maximal value
     * @param $step Step value
     */
    public function setRange($min, $max, $step)
    {
        
        parent::set_range($min, $max);
        parent::set_increments($step, $step*10);
    }
    
    /**
     * Define the widget's size
     * @param $size Widget's size in pixels
     */
    public function setSize($size)
    {
        $this->set_size_request($size,-1);
    }
    
    /**
     * Define if the widget is editable
     * @param $boolean  A boolean indicating if the widget is editable
     */
    public function setEditable($editable)
    {
        parent::set_sensitive($editable);
    }
    
    /**
     * Define widget properties
     * @param  $property Property's name
     * @param  $value    Property's value
     */
    public function setProperty($property, $value)
    {
        if ($property=='readonly')
            parent::set_editable(false);
    }
    
    /**
     * Add a field validator
     * @param $validator TFieldValidator object
     */
    public function addValidation($label, TFieldValidator $validator, $parameters = NULL)
    {
        $this->validations[] = array($label, $validator, $parameters);
    }
    
    /**
     * Validate a field
     * @param $validator TFieldValidator object
     */
    public function validate()
    {
        if ($this->validations)
        {
            foreach ($this->validations as $validation)
            {
                $label      = $validation[0];
                $validator  = $validation[1];
                $parameters = $validation[2];
                
                $validator->validate($label, $this->getValue(), $parameters);
            }
        }
    }
    
    /**
     * Register a tip
     * @param $text Tooltip Text
     */
    function setTip($text)
    {
        if (method_exists($this, 'set_tooltip_text'))
        {
            $this->set_tooltip_text($text);
        }
        else
        {
            $tooltip = TooltipSingleton::getInstance();
            $tooltip->set_tip($this, $text);
        }
    }
}
?>