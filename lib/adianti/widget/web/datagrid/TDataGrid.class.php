<?php
/**
 * DataGrid Widget: Allows to create datagrids with rows, columns and actions
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage datagrid
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDataGrid extends TTable
{
    private $columns;
    private $actions;
    private $rowcount;
    private $modelCreated;
    private $pageNavigation;
    private $defaultClick;
    
    /**
     * Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->modelCreated = FALSE;
        $this->defaultClick = TRUE;
        
        $this->{'class'} = 'tdatagrid_table';
        $this-> id    = 'tdatagrid_table';
    }
    
    /**
     * disable the default click action
     */
    public function disableDefaultClick()
    {
        $this->defaultClick = FALSE;
    }
    
    /**
     * Define the Height
     * @param $height An integer containing the height
     */
    function setHeight($height)
    {
        // do nothing for GTK compability.
    }
    
    /**
     * Add a Column to the DataGrid
     * @param $object A TDataGridColumn object
     */
    public function addColumn(TDataGridColumn $object)
    {
        if (!$this->modelCreated)
        {
            $this->columns[] = $object;
        }
    }
    
    /**
     * Add an Action to the DataGrid
     * @param $object A TDataGridAction object
     */
    public function addAction(TDataGridAction $object)
    {
        if (!$this->modelCreated)
        {
            $this->actions[] = $object;
        }
    }
    
    /**
     * Clear the DataGrid contents
     */
    function clear()
    {
        if ($this->modelCreated)
        {
            // copy the headers
            $copy = $this->children[0];
            // reset the row array
            $this->children = array();
            // add the header again
            $this->children[] = $copy;
            // restart the row count
            $this->rowcount = 0;
        }
    }
    
    /**
     * Creates the DataGrid Structure
     */
    public function createModel()
    {
        if (!$this->columns)
        {
            return;
        }
        
        // add a row to the table
        $row = parent::addRow();
        
        // add some cells for the actions
        if ($this->actions)
        {
            foreach ($this->actions as $action)
            {
                $cell = $row->addCell('&nbsp;');
                $cell->{'class'} = 'tdatagrid_col';
                $cell-> width = '16px';
            }
        }
        
        // add some cells for the data
        if ($this->columns)
        {
            // iterate the DataGrid columns
            foreach ($this->columns as $column)
            {
                // get the column properties
                $name  = $column->getName();
                $label = '&nbsp;'.$column->getLabel().'&nbsp;';
                $align = $column->getAlign();
                $width = $column->getWidth();
                if (isset($_GET['order']))
                {
                    if ($_GET['order'] == $name)
                    {
                        $label .= '<img src="lib/adianti/images/ico_down.png">';
                    }
                }
                // add a cell with the columns label
                $cell = $row->addCell($label);
                
                $cell->{'class'} = 'tdatagrid_col';
                $cell-> align = $align;
                if ($width)
                {
                    $cell-> width = $width.'px';
                }
                
                // verify if the column has an attached action
                if ($column->getAction())
                {
                    $url = $column->getAction();
                    $cell-> onmouseover = "this.className='tdatagrid_col_over';";
                    $cell-> onmouseout  = "this.className='tdatagrid_col'";
                    $cell-> href        = $url;
                    $cell-> generator   = 'adianti';
                }
            }
        }
        $this->modelCreated = TRUE;
    }
    

    /**
     * Add an object to the DataGrid
     * @param $object An Active Record Object
     */
    public function addItem($object)
    {
        if ($this->modelCreated)
        {
            // define the background color for that line
            $classname = ($this->rowcount % 2) == 0 ? 'tdatagrid_row_even' : 'tdatagrid_row_odd';
            
            // add one row to the DataGrid
            $row = parent::addRow();
            $row->{'class'} = $classname;
            
            // verify if the DataGrid has ColumnActions
            if ($this->actions)
            {
                // iterate the actions
                foreach ($this->actions as $action)
                {
                    // get the action properties
                    $field  = $action->getField();
                    $label  = $action->getLabel();
                    $image  = $action->getImage();
                    
                    if (is_null($field))
                    {
                        throw new Exception(TAdiantiCoreTranslator::translate('Field for action ^1 not defined', $label) . '.<br>' . 
                                            TAdiantiCoreTranslator::translate('Use the ^1 method', 'setField'.'()').'.');
                    }
                    
                    // get the object property that will be passed ahead
                    $key    = $object->$field;
                    $action->setParameter('key', $key);
                    $url    = $action->serialize();
                    
                    // creates a link
                    $link = new TElement('a');
                    $link-> href      = $url;
                    $link-> generator = 'adianti';
                    
                    $first_url = isset($first_url) ? $first_url : $link-> href;
                    
                    // verify if the link will have an icon or a label
                    if ($image)
                    {
                        // add the image to the link
                        if (file_exists("lib/adianti/images/$image"))
                        {
                            $image=new TImage("lib/adianti/images/$image");
                        }
                        else
                        {
                            $image=new TImage("app/images/$image");
                        }
                        $image-> title = $label;
                        $link->add($image);
                    }
                    else
                    {
                        // add the label to the link
                        $link->add($label);
                    }
                    // add the cell to the row
                    $cell = $row->addCell($link);
                    $cell->{'class'} = 'tdatagrid_cell';
                }
            }
            if ($this->columns)
            {
                // iterate the DataGrid columns
                foreach ($this->columns as $column)
                {
                    // get the column properties
                    $name     = $column->getName();
                    $align    = $column->getAlign();
                    $width    = $column->getWidth();
                    $function = $column->getTransformer();
                    $data     = $object->$name;
                    // verify if there's a transformer function
                    if ($function)
                    {
                        // apply the transformer functions over the data
                        $data = call_user_func($function, $data);
                    }
                    
                    if ($editaction = $column->getEditAction())
                    {
                        $editaction_field = $editaction->getField();
                        $div = new TElement('div');
                        $div->{'class'}  = 'inlineediting';
                        $div->{'style'}  = 'padding-left:5px;padding-right:5px';
                        $div->{'action'} = $editaction->serialize();
                        $div->{'field'}  = $name;
                        $div->{'key'}    = $object->{$editaction_field};
                        $div->add($data);
                        $cell = $row->addCell($div);
                        $cell->{'class'} = 'tdatagrid_cell';
                    }
                    else
                    {
                        // add the cell to the row
                        $cell = $row->addCell($data);
                        $cell->{'class'} = 'tdatagrid_cell';
                        $cell-> align = $align;
                        $cell->{'style'} = 'padding-left:5px;padding-right:5px';
                        if (isset($first_url) AND $this->defaultClick)
                        {
                            $cell-> href      = $first_url;
                            $cell-> generator = 'adianti';
                        }
                    }
                    
                    if ($width)
                    {
                        $cell-> width = $width.'px';
                    }
                }
            }
            
            // when the mouse is over the datagrid row
             $row-> onmouseover = "className='tdatagrid_row_sel'; style.cursor='pointer'";
             $row-> onmouseout  = "className='{$classname}';";
            
            // increments the row counter
            $this->rowcount ++;
        }
    }
    
    /**
     * Returns the DataGrid's width
     * @return An integer containing the DataGrid's width
     */
    public function getWidth()
    {
        $width=0;
        if ($this->actions)
        {
            // iterate the DataGrid Actions
            foreach ($this->actions as $action)
            {
                $width += 22;
            }
        }
        
        if ($this->columns)
        {
            // iterate the DataGrid Columns
            foreach ($this->columns as $column)
            {
                $width += $column->getWidth();
            }
        }
        return $width;
    }
    
    /**
     * Shows the DataGrid
     */
    function show()
    {
        TPage::include_css('lib/adianti/include/tdatagrid/tdatagrid.css');
        // shows the datagrid
        parent::show();
        
        $params = $_REQUEST;
        unset($params['class']);
        unset($params['method']);
        // to keep browsing parameters (order, page, first_page, ...)
        $urlparams='&'.http_build_query($params);
        
        // inline editing treatment
        $script = new TElement('script');
        $script->add('$(function() {
        	$(".inlineediting").editInPlace({
        		callback: function(unused, enteredText)
        		{
        		    __adianti_load_page($(this).attr("action")+"'.$urlparams.'&key="+$(this).attr("key")+"&field="+$(this).attr("field")+"&value="+encodeURIComponent(enteredText));
        		    return enteredText;
        		},
        		show_buttons: false,
        		text_size:20,
        		params:column=name
    	    });
        });');
        $script->show();
    }
    
    /**
     * Assign a PageNavigation object
     * @param $pageNavigation object
     */
    function setPageNavigation($pageNavigation)
    {
        $this->pageNavigation = $pageNavigation;
    }
    
    /**
     * Return the assigned PageNavigation object
     * @return $pageNavigation object
     */
    function getPageNavigation()
    {
        return $this->pageNavigation;
    }
}
?>