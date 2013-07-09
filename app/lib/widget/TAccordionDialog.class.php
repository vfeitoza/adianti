<?php
/**
 * Accordion Dialog
 *
 * @version    1.0 2013-07-09
 * @package    widget_web
 * @author     Victor Feitoza <vfeitoza [at] gmail.com>
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TAccordionDialog
{
    private $id;
    private $width;
    private $height;
    private $useOKButton;
    private $accordion;
    private $action;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->id = uniqid();
        $this->useOKButton = TRUE;
    }
    
    /**
     * Define o ID para carregar o conteudo dinamicamente
     * @param $id ID a ser carregada
     */
    public function setAccordion($id)
    {
        $this->accordion = 'open: function() { $("#taccordion_'.$id.'").accordion({ autoHeight: true }); }, ';
    }
    
    /**
     * Define o ID para carregar o conteudo dinamicamente
     * @param $id ID a ser carregada
     * @param $getContentAjax Campo a ser carregado via ajax
     */
    public function setAction(TAction $action, $getContentAjax = null)
    {
        if (is_null($getContentAjax))
        {
            $this->action = '__adianti_load_page(\''.$action->serialize() . '\');';
        }
        else
        {
            $this->action = '__adianti_load_page(\''.$action->serialize() . '&'.$getContentAjax.'=\'+$("#'.$getContentAjax.'").val());';
        }
    }
    
    /**
     * Define se vai ter botão de OK
     * @param $bool booleano
     */
    public function setUseOKButton($bool)
    {
        $this->useOKButton = $bool;
    }
    
    /**
     * Define o título do diálogo
     * @param $title título do diálogo
     */
    public function setTitle($title)
    {
        $this->{'title'} = $title;
    }
    
    /**
     * Define o tamanho do diálogo
     * @param $width largura
     * @param $height altura
     */
    public function setSize($width, $height)
    {
        $this->width  = $width;
        $this->height = $height;
    }
    
    /**
     * Mostra os dados criados dinamicamente
     */
    public function show($context = null)
    {
        
        TPage::include_css('lib/adianti/include/tmessage/tmessage.css');
        
        $ok_button = '';
        if ($this->useOKButton)
        {
            $ok_button = '  OK: function() { '.$this->action.' $( this ).remove(); }';
        }
        
        // creates a pannel to show the dialog
        $painel = new TElement('div');
        $painel->{'class'} = 'tmessage';
        $painel-> id    = 'tmessage_'.$this->id;
        
        if (is_object($context)) 
        {
            // add the table to the pannel
            $painel->add($context);
        }    
     
        // show the pannel
        $painel->show();
        
        $script = new TElement('script');
        $script->{'type'} = 'text/javascript';
        $script->add(' $(function() {
            $( "#'.$painel-> id.'" ).dialog({
                height:'.$this->height.',
                width:'.$this->width.',
                stack: false,
                zIndex: 3000,
                modal: false,
                '. $this->accordion .'
                close: function(ev, ui) { $(this).remove(); },
                buttons: {
                    ' . $ok_button . 
                    '
                }
                }).css("visibility", "visible");
                
                $( "#'.$painel-> id.' a" ).click(function () {
                    window.open($(this).attr(\'href\'));
                }); 
            });');
        $script->show();
    }
}
?>