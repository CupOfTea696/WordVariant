<?php namespace CupOfTea\WordVariant;

class WordVariant
{
    
    /**
     * The object instance.
     *
     * @var array
     */
    protected static $instance;
    
    /**
     * Get the WordVariant instance.
     *
     * @return mixed
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new WordVariantCore;
        }
        
        return static::$instance;
    }
    
    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();
        
        switch (count($args)) {
            case 0:
                return $instance->$method();
                
            case 1:
                return $instance->$method($args[0]);
                
            case 2:
                return $instance->$method($args[0], $args[1]);
                
            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);
                
            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
                
            default:
                return call_user_func_array([$instance, $method], $args);
        }
    }
    
}
