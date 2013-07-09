<?php
/**
 * Standard page controller for listings
 *
 * @version    1.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TStandardList extends TPage
{
    protected $form;            // registration form
    protected $datagrid;        // listing
    protected $pageNavigation;  // pagination component
    protected $activeRecord;    // Active Record class name
    protected $filterField;     // filtering field name
    protected $database;        // Database name
    protected $loaded;
    protected $limit;
    protected $operator;
    
    public function __construct()
    {
        parent::__construct();
        $this->operator = 'like';
    }
    
    /**
     * method setDatabase()
     * Define the database
     */
    function setDatabase($database)
    {
        $this->database = $database;
    }
    
    /**
     * method setLimit()
     * Define the record limit
     */
    function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    /**
     * method setOperator()
     * Define the filtering operator
     */
    function setOperator($operator)
    {
        $this->operator = $operator;
    }
    
    /**
     * method setActiveRecord()
     * Define wich Active Record class will be used
     */
    function setActiveRecord($activeRecord)
    {
        $this->activeRecord = $activeRecord;
    }
    
    /**
     * method setFilterField()
     * Define wich field will be used for filtering
     */
    function setFilterField($filterField)
    {
        $this->filterField = $filterField;
    }
    
    /**
     * method onInlineEdit()
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            // open a transaction with database
            TTransaction::open($this->database);
            
            // instantiates object {ACTIVE_RECORD}
            $class = $this->activeRecord;
            
            // instantiates object
            $object = new $class($key);
            
            // deletes the object from the database
            $object->{$field} = $value;
            $object->store();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload($param);
            // shows the success message
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onSearch()
     * Register the filter in the session when the user performs a search
     */
    function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // check if the user has filled the form
        if (isset($data->{$this->filterField}) AND $data->{$this->filterField})
        {
            // creates a filter using what the user has typed
            if (stristr($this->operator, 'like'))
            {
                $filter = new TFilter($this->filterField, $this->operator, "%{$data->{$this->filterField}}%");
            }
            else
            {
                $filter = new TFilter($this->filterField, $this->operator, $data->{$this->filterField});
            }
            
            // stores the filter in the session
            TSession::setValue($this->activeRecord.'_filter', $filter);
            TSession::setValue($this->activeRecord.'_'.$this->filterField, $data->{$this->filterField});
        }
        else
        {
            TSession::setValue($this->activeRecord.'_filter', NULL);
            TSession::setValue($this->activeRecord.'_'.$this->filterField, '');
        }
        
        // fill the form with data again
        $this->form->setData($data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * method onReload()
     * Load the datagrid with the database objects
     */
    function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database
            TTransaction::open($this->database);
            
            // instancia um repositório
            $repository = new TRepository($this->activeRecord);
            $limit = isset($this->limit) ? $this->limit : 10;
            // creates a criteria
            $criteria = new TCriteria;
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            
            if (TSession::getValue($this->activeRecord.'_filter'))
            {
                // add the filter stored in the session to the criteria
                $criteria->add(TSession::getValue($this->activeRecord.'_filter'));
            }
            
            // load the objects according to criteria
            $objects = $repository->load($criteria);
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            if (isset($this->pageNavigation))
            {
                $this->pageNavigation->setCount($count); // count of records
                $this->pageNavigation->setProperties($param); // order, page
                $this->pageNavigation->setLimit($limit); // limit
            }
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method onDelete()
     * executed whenever the user clicks at the delete button
     * Ask if the user really wants to delete the record
     */
    function onDelete($param)
    {
        // get the parameter $key
        $key=$param['key'];
        
        // define two actions
        $action = new TAction(array($this, 'Delete'));
        
        // define the action parameters
        $action->setParameter('key', $key);
        
        // shows a dialog to the user
        new TQuestion(TAdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * method Delete()
     * Delete a record
     */
    function Delete($param)
    {
        try
        {
            // get the parameter $key
            $key=$param['key'];
            // open a transaction with database
            TTransaction::open($this->database);
            
            $class = $this->activeRecord;
            
            // instantiates object
            $object = new $class($key);
            
            // deletes the object from the database
            $object->delete();
            
            // close the transaction
            TTransaction::close();
            
            // reload the listing
            $this->onReload();
            // shows the success message
            new TMessage('info', TAdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * method show()
     * Shows the page
     */
    function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded)
        {
            $this->onReload();
        }
        parent::show();
    }
}
?>