<?php
/**
 * Entry Widget (also known as Edit, Input)
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TEntry extends TField
{
    public $id;
    private $mask;
    private $completion;
    private $exitAction;
    
    /**
     * Define the field's mask
     * @param $mask A mask for input data
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
    }
    
    /**
     * Define max length
     * @param  $length Max length
     */
    public function setMaxLength($length)
    {
        if ($length > 0)
        {
            $this->tag-> maxlength = $length;
        }
    }
    
    /**
     * Define options for completion
     * @param $options array of options for completion
     */
    function setCompletion($options)
    {
        $this->completion = $options;
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction(TAction $action)
    {
        $this->exitAction = $action;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        TPage::include_js('lib/adianti/include/tentry/tentry.js');
        
        // define the tag properties
        $this->tag-> name  = $this->name;    // TAG name
        $this->tag-> value = $this->value;   // TAG value
        $this->tag-> type  = 'text';         // input type
        $this->tag-> style = "width:{$this->size}px";  // size
        
        if ($this->id)
        {
            $this->tag-> id    = $this->id;
        }
        
        // verify if the widget is non-editable
        if (parent::getEditable())
        {
            if (isset($this->exitAction))
            {
                $string_action = $this->exitAction->serialize(FALSE);
                $this->setProperty('onBlur', "serialform=(\$('#{$this->formName}').serialize());
                                              ajaxLookup('$string_action&'+serialform, this)");
            }
            
            if ($this->mask)
            {
                TPage::include_js('app/lib/include/jquery.meiomaskmoney.js');
                
                if ($this->mask == 'msk:money')
                {
                    $this->tag->{'class'} = 'tfield money';
                    $this->tag->{'id'} = $this->name;
                }
                else if ($this->mask == 'msk:decimal')
                {
                    $this->tag->{'class'} = 'tfield decimal';
                    $this->tag->{'id'} = $this->name;
                }
                else if (substr($this->mask, 3) == 'msk')
                {
                    $this->tag->{'msk'} = substr($this->mask, 4);
                    $this->tag->{'id'} = $this->name;
                }
                else
                {
                    $this->tag-> onKeyPress="return entryMask(this,event,'{$this->mask}')";
                }
            }
        }
        else
        {
            $this->tag-> readonly = "1";
            $this->tag->{'class'} = 'tfield_disabled'; // CSS
            $this->tag-> style = "width:{$this->size}px;".
                                 "-moz-user-select:none;";
            $this->tag-> onmouseover = "style.cursor='default'";
        }
        
        // shows the tag
        $this->tag->show();
        
        if (isset($this->completion))
        {
            $options = json_encode($this->completion);
            $script = new TElement('script');
            $script->add("\$('input[name=\"{$this->name}\"]').autocomplete({source: {$options} });");
            $script->show();
        }
    }
}
?>