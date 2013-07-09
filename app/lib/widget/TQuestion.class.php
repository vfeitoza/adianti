<?php
/**
 * Question Dialog
 *
 * @version    1.1 2013-07-09
 * @package    widget_web
 * @subpackage dialog
 * @author     Pablo Dall'Oglio
 * @author     Victor Feitoza <vfeitoza [at] gmail.com>
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TQuestion
{
    private $id;
    
    /**
     * Class Constructor
     * @param  $message    A string containint the question
     * @param  $action_yes Action taken for YES response
     * @param  $action_no  Action taken for NO  response
     */
    public function __construct($message, TAction $action_yes = NULL, TAction $action_no = NULL)
    {
        $this->id = uniqid();
        
        if (TPage::isMobile())
        {
            $img = new TElement('img');
            $img-> src = "lib/adianti/images/question.png";
            
            $yes = new TElement('a');
            $yes-> href      = $action_yes->serialize();
            $yes-> generator = 'adianti';
            $yes->add(TAdiantiCoreTranslator::translate('Yes'));
            
            $no = new TElement('a');
            $no-> href      = $action_no->serialize();
            $no-> generator = 'adianti';
            $no->add(TAdiantiCoreTranslator::translate('No'));
            
            $table = new TTable;
            $table-> width='250px';
            $table-> bgcolor='#E5E5E5';
            $table-> style="border-collapse:collapse";
            $row = $table->addRow();
            $row->addCell($img);
            $table2=new TTable;
            $row->addCell($table2);
            $row=$table2->addRow();
            $c=$row->addCell($message);
            $c-> colspan=2;
            $row=$table2->addRow();
            $row->addCell($yes);
            $row->addCell($no);
            $table->show();
        }
        else
        {
            TPage::include_css('lib/adianti/include/tmessage/tmessage.css');
            
            // creates a layer to show the dialog
            $painel = new TElement('div');
            $painel->{'class'} = "tmessage";
            $painel-> id    = 'tquestion_'.$this->id;
            $url_yes = '';
            $url_no  = '';
            if ($action_yes)
            {
                // convert the actions into URL's
                $url_yes = TAdiantiCoreTranslator::translate('Yes') . ': function () { $( this ).dialog( "close" ); __adianti_load_page(\''.$action_yes->serialize() . '\');},';
            }
            
            if ($action_no)
            {
                $url_no = TAdiantiCoreTranslator::translate('No') . ': function () { $( this ).dialog( "close" ); __adianti_load_page(\''.$action_no->serialize() . '\');},';
            }
            else
            {
            $url_no = TAdiantiCoreTranslator::translate('No') . ': function () { $( this ).dialog( "close" );},';
            }
            
            // creates a table for layout
            $table = new TTable;
            
            // creates a row for the icon and the message
            $row=$table->addRow();
            $row->addCell(new TImage("lib/adianti/images/question.png"));
            
            $scroll=new TScroll;
            $scroll->setSize(350,70);
            $scroll->add($message);
            $scroll->setTransparency(true);
            $cell=$row->addCell($scroll);
            
            // add the table to the pannel
            $painel->add($table);
            // show the pannel
            $painel->show();
            
            $script = new TElement('script');
            $script->add(' $(function() {
                $( "#'.$painel-> id.'" ).dialog({
                    height: 180,
                    width: 440,
                    modal: true,
                    stack: false,
                    zIndex: 3000,
                    buttons: {
                    '.$url_yes . $url_no .
                        TAdiantiCoreTranslator::translate('Cancel').': function() {
                            $( this ).dialog( "close" );
                        }
                    }
                    }).css("visibility", "visible");
                });');
            $script->show();
        }
    }
}
?>