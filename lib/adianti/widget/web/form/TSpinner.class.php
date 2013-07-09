<?php
/**
 * Spinner Widget (also known as spin button)
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TSpinner extends TField
{
    public $id;
    private static $counter;
    private $min;
    private $max;
    private $step;
    private $exitaction;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        self::$counter ++;
        $this->id   = 'tspinner_'.uniqid();
    }
    
    /**
     * Define the field's range
     * @param $min Minimal value
     * @param $max Maximal value
     * @param $step Step value
     */
    public function setRange($min, $max, $step)
    {
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
        
        parent::setValue($min);
    }
    
    /**
     * Define the action to be executed when the user leaves the form field
     * @param $action TAction object
     */
    function setExitAction($action)
    {
        $this->exitaction = $action;
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
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
            $change_action = '';
            if (isset($this->exitaction))
            {
                $string_action = $this->exitaction->serialize(FALSE);
                $change_action = "serialform=(\$('#{$this->formName}').serialize());
                                                  ajaxLookup('$string_action&'+serialform, this);";
            }
            
            $script = new TElement('script');
            $script->add(' $(function() {
                        $( "#'.$this->id.'" ).spinner({
                            step: '.$this->step.',
                            numberFormat: "n",
                            spin: function( event, ui ) {
                                '.$change_action.'
                                if ( ui.value > '.$this->max.' ) {
                                    $( this ).spinner( "value", '.$this->min.' );
                                    return false;
                                } else if ( ui.value < '.$this->min.' ) {
                                    $( this ).spinner( "value", '.$this->max.' );
                                    return false;
                                }
                            }
                        });
                        });');
            $script->show();
            

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
    }
}
?>