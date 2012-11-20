<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 2/28/11
 * Time: 5:32 PM
 */
 
class Moldova_Log_Writer_Doctrine extends Zend_Log_Writer_Abstract{
    // constructor
    // accepts model name and column map
    public function __construct($modelName, $columnMap)
    {
        $this->_modelName = $modelName;
        $this->_columnMap = $columnMap;
    }

    // stub function
    // to deny formatter coupling
    public function setFormatter($formatter)
    {
        require_once 'Zend/Log/Exception.php';
        throw new Zend_Log_Exception(get_class() . ' does not support formatting');
    }

    // main log write method
    // maps database fields to log message fields
    // saves log messages as database records using model methods
    protected function _write($message)
    {
        $data = array();
        foreach ($this->_columnMap as $messageField => $modelField) {
            $data[$modelField] = $message[$messageField];
        }
        $model = new $this->_modelName();
        $model->fromArray($data);
        $model->save();
    }

    // static factory method
    static public function factory($config)
    {
        return new self(self::_parseConfig($config));
    }
}
