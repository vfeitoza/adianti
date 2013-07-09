<?php
/**
 * DataPicker Widget
 *
 * @version    1.0
 * @package    widget_web
 * @subpackage form
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006-2013 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class TDate extends TEntry
{
    private $mask;
    public $id;
    private static $counter;
    
    /**
     * Class Constructor
     * @param $name Name of the widget
     */
    public function __construct($name)
    {
        parent::__construct($name);
        self::$counter ++;
        //$this->id   = 'tdate'.self::$counter;
        $this->id   = 'tdate_'.uniqid();
        $this->mask = 'yyyy-mm-dd';
        
        $newmask = $this->mask;
        $newmask = str_replace('dd',   '99',   $newmask);
        $newmask = str_replace('mm',   '99',   $newmask);
        $newmask = str_replace('yyyy', '9999', $newmask);
        parent::setMask($newmask);
    }
    
    /**
     * Define the field's mask
     * @param $mask  Mask for the field (dd-mm-yyyy)
     */
    public function setMask($mask)
    {
        $this->mask = $mask;
        $newmask = $this->mask;
        $newmask = str_replace('dd',   '99',   $newmask);
        $newmask = str_replace('mm',   '99',   $newmask);
        $newmask = str_replace('yyyy', '9999', $newmask);
        parent::setMask($newmask);
    }
    
    /**
     * Convert a date to format yyyy-mm-dd
     * @param $date = date in format dd/mm/yyyy
     */
    public static function date2us($date)
    {
        if ($date)
        {
            // get the date parts
            $day  = substr($date,0,2);
            $mon  = substr($date,3,2);
            $year = substr($date,6,4);
            return "{$year}-{$mon}-{$day}";
        }
    }
    
    /**
     * Convert a date to format dd/mm/yyyy
     * @param $date = date in format yyyy-mm-dd
     */
    public static function date2br($date)
    {
        if ($date)
        {
            // get the date parts
            $year = substr($date,0,4);
            $mon  = substr($date,5,2);
            $day  = substr($date,8,4);
            return "{$day}/{$mon}/{$year}";
        }
    }
    
    /**
     * Shows the widget at the screen
     */
    public function show()
    {
        $js_mask = str_replace('yyyy', 'yy', $this->mask);
        
        if (parent::getEditable())
        {
            $script = new TElement('script');
            $script-> type = 'text/javascript';
            $script->add("
            	$(function() {
                $(\"#{$this->id}\").datepicker({
                    showOn: 'button',
                    buttonImage: 'lib/adianti/images/tdate.png',
                    buttonImageOnly: true,    
            		changeMonth: true,
            		changeYear: true,
            		dateFormat: '{$js_mask}',
            		showButtonPanel: true
            	});
            });");
            $script->show();
        }
		
        parent::show();
    }
}
?>