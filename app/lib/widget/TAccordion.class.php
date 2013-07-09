<?php
/**
 * TAccordion Container
 * Copyright (c) 2006-2010 Pablo Dall'Oglio
 * @author  Pablo Dall'Oglio <pablo [at] adianti.com.br>
 * @author  Victor Feitoza <vfeitoza [at] gmail.com>
 * @version 2.1, 2013-07-09
 */
class TAccordion extends TElement
{
    protected $elements;
    private $autoHeight = NULL;
    private $useDialog = false;
    
    /**
     * Class Constructor
     */
    public function __construct($id = NULL)
    {
        parent::__construct('div');
        if ($id)
        {
            $this->id = 'taccordion_' . $id;
            $this->useDialog = true;
        }
        else
        {
            $this->id = 'taccordion_' . uniqid();
        }
        $this->elements = array();
    }
    
    /**
     * Add auto height in to accordion
     * @param $bool TRUE to auto height
     */
    public function setAutoHeight($bool)
    {
        if ($bool)
        {
            $this->autoHeight = '{ autoHeight: true }';
        }
    }
    
    /**
     * Add an object to the accordion
     * @param $title  Title
     * @param $objeto Content
     */
    public function appendPage($title, $object)
    {
        $this->elements[] = array($title, $object);
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        foreach ($this->elements as $child)
        {
            $title = new TElement('h3');
            $title->add($child[0]);
            
            $content = new TElement('div');
            $content->add($child[1]);
            
            parent::add($title);
            parent::add($content);
        }
        
    	$script = new TElement('script');
    	$script->{'type'} = 'text/javascript';
    	$code = '
            $(document).ready( function() {
                $( "#'.$this->id.'" ).accordion('.$this->autoHeight.');
            });
            ';
        $script->add($code);

        if (!$this->useDialog)
        {
             parent::add($script);
        }
        parent::show();
    }
}
?>