<?php
/**
 * Message Dialog
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TMessage
{
    private $id;
    
    /**
     * Class Constructor
     * @param $type    Type of the message (info, error)
     * @param $message Message to be shown
     */
    public function __construct($type, $message)
    {
        $this->id = uniqid();
        
        if (TPage::isMobile())
        {
            $img = new TElement('img');
            $img-> src = "lib/adianti/images/{$type}.png";
            
            $table = new TTable;
            $table-> width='250px';
            $table-> bgcolor='#E5E5E5';
            $table-> style="border-collapse:collapse";
            
            $row = $table->addRow();
            $row->addCell($img);
            $row->addCell($message);
            $table->show();
        }
        else
        {
            TPage::include_css('lib/adianti/include/tmessage/tmessage.css');
            
            // creates a pannel to show the dialog
            $painel = new TElement('div');
            $painel->{'class'} = 'tmessage';
            $painel-> id    = 'tmessage_'.$this->id;
            
            // creates a table for layout
            $table = new TTable;
            
            // creates a row for the icon and the message
            $row=$table->addRow();
            $row->addCell(new TImage("lib/adianti/images/{$type}.png"));
            
            $scroll=new TScroll;
            $scroll->setSize(350,150);
            $scroll->add($message);
            $cell=$row->addCell($scroll);
            
            // add the table to the pannel
            $painel->add($table);
            // show the pannel
            $painel->show();
            
            $script = new TElement('script');
            $script->{'type'} = 'text/javascript';
            $script->add(' $(function() {
                $( "#'.$painel-> id.'" ).dialog({
                    height: 280,
                    width: 440,
                    stack: false,
                    zIndex: 3000,
                    modal: true,
                    buttons: {
                        OK: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                    }).css("visibility", "visible");
                    
                	$( "#'.$painel-> id.' a" ).click(function () {
                	    window.open($(this).attr(\'href\'));
                    }); 
                });');
            $script->show();
        }
    }
}
?>