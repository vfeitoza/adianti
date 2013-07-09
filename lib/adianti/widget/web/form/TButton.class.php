<?php
/**
 * Button Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TButton extends TField
{
    private $action;
    private $label;
    private $image;
    private $function;
    
    /**
     * Define the action of the button
     * @param  $action TAction object
     * @param  $label  Button's label
     */
    public function setAction(TAction $action, $label)
    {
        $this->action = $action;
        $this->label  = $label;
    }
    
    /**
     * Define the icon of the button
     * @param  $image  image path
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
    
    /**
     * Define the label of the button
     * @param  $label button label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Add a JavaScript function to be executed by the button
     * @param $function A piece of JavaScript code
     * @ignore-autocomplete on
     */
    public function addFunction($function)
    {
        $this->function = $function;
    }
    
    /**
     * Show the widget at the screen
     */
    public function show()
    {
        if ($this->action)
        {
            // get the action as URL
            //$this->action->setParameter('encoding', 'utf8');
            //$this->action->setParameter('isajax',   '1');
            $url = $this->action->serialize(FALSE);
            
            $wait_message = TAdiantiCoreTranslator::translate('Loading');
            // define the button's action (ajax post)
            $action = "
                      $.blockUI({ 
                            message: '<h1>{$wait_message}</h1>',
                            css: { 
                                border: 'none', 
                                padding: '15px', 
                                backgroundColor: '#000', 
                                'border-radius': '5px 5px 5px 5px',
                                opacity: .5, 
                                color: '#fff' 
                            }
                        });
                       {$this->function};
                       $.post('engine.php?{$url}',
                              \$('#{$this->formName}').serialize(),
                              function(result)
                              {
                                  __adianti_load_html(result);
                                  $.unblockUI();
                              });
                       return false;";
                        
            $button = new TElement('button');
            $button->{'class'} = 'btn btn-small';
            $button-> onclick   = $action;
            $button-> id   = $this->name;
            $button-> name = $this->name;
            $action = '';
        }
        else
        {
            $action = $this->function;
            // creates the button using a div
            $button = new TElement('div');
            $button-> id   = $this->name;
            $button-> name = $this->name;
            $button->{'class'} = 'btn btn-small';
            $button-> onclick  = $action;
        }
        
        $span = new TElement('span');
        if ($this->image)
        {
            if (file_exists('lib/adianti/images/'.$this->image))
            {
                $image = new TImage('lib/adianti/images/'.$this->image);
            }
            else
            {
                $image = new TImage('app/images/'.$this->image);
            }
            $image->{'style'} = 'padding-right:4px';
            $span->add($image);
        }
        $span->add($this->label);
        $button->add($span);
        $button->show();
    }
}
?>