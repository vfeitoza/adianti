<?php
/**
 * ComboBox Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TCombo extends TField
{
    protected $items; // array containing the combobox options
    private   $changeaction;
    private   $id;
    
    /**
     * Class Constructor
     * @param  $name widget's name
     */
    public function __construct($name)
    {
        // executes the parent class constructor
        parent::__construct($name);
        $this->id   = 'tcombo_'.uniqid();
        
        TPage::include_css('lib/adianti/include/tcombo/tcombo.css');
        
        // creates a <select> tag
        $this->tag = new TElement('select');
        $this->tag->{'class'} = 'tcombo'; // CSS
    }
    
    /**
     * Add items to the combo box
     * @param $items An indexed array containing the combo options
     */
    public function addItems($items)
    {
        $this->items = $items;
    }
    
    /**
     * Return the post data
     */
    public function getPostData()
    {
        if (isset($_POST[$this->name]))
        {
            $val = $_POST[$this->name];
            
            if ($val == '') // empty option
            {
                return '';
            }
            else
            {
                if (strpos($val, '::'))
                {
                    $tmp = explode('::', $val);
                    return trim($tmp[0]);
                }
                else
                {
                    return $val;
                }
            }
        }
        else
        {
            return '';
        }
    }
    
    /**
     * Define the action to be executed when the user changes the combo
     * @param $action TAction object
     */
    public function setChangeAction(TAction $action)
    {
        $this->changeaction = $action;
    }
    
    /**
     * Reload combobox items after it is already shown
     * @param $formname form name (used in gtk version)
     * @param $name field name
     * @param $items array with items
     */
    public static function reload($formname, $name, $items)
    {
        $script = new TElement('script');
        $script->{'language'} = 'JavaScript';
        $script->setUseSingleQuotes(TRUE);
        $script->setUseLineBreaks(FALSE);
        $code = '$(function() {';
        $code .= '$(\'select[name="'.$name.'"]\').html("");';
        foreach ($items as $key => $value)
        {
            $code .= '$("<option value=\''.$key.'\'>'.$value.'</option>").appendTo(\'select[name="'.$name.'"]\');';
        }
        $code.= '});';
        $script->add($code);
        $script->show();
    }
    
    /**
     * Shows the widget
     */
    public function show()
    {
        // define the tag properties
        $this->tag-> name  = $this->name;    // tag name
        $this->tag-> style = "width:{$this->size}px";  // size in pixels
        
        // creates an empty <option> tag
        $option = new TElement('option');
        $option->add('');
        $option-> value = '';   // tag value
        // add the option tag to the combo
        $this->tag->add($option);
        
        if ($this->items)
        {
            // iterate the combobox items
            foreach ($this->items as $chave => $item)
            {
                if (substr($chave, 0, 3) == '>>>')
                {
                    $optgroup = new TElement('optgroup');
                    $optgroup-> label = $item;
                    // add the option to the combo
                    $this->tag->add($optgroup);
                }
                else
                {
                    // creates an <option> tag
                    $option = new TElement('option');
                    $option-> value = $chave;  // define the index
                    $option->add($item);      // add the item label
                    
                    // verify if this option is selected
                    if (($chave == $this->value) AND ($this->value !== NULL))
                    {
                        // mark as selected
                        $option-> selected = 1;
                    }
                    
                    if (isset($optgroup))
                    {
                        $optgroup->add($option);
                    }
                    else
                    {
                        $this->tag->add($option);
                    }                    
                }
            }
        }
        
        // verify whether the widget is editable
        if (parent::getEditable())
        {
            if (isset($this->changeaction))
            {
                $string_action = $this->changeaction->serialize(FALSE);
                $this->setProperty('onChange', "serialform=(\$('#{$this->formName}').serialize());
                                              ajaxLookup('$string_action&'+serialform, this)");
            }
        }
        else
        {
            // make the widget read-only
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
        }
        // shows the combobox
        $this->tag->show();
    }
}
?>