<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 2/23/11
 * Time: 4:12 PM
 */
 
class Moldova_Auth_Adapter_AccLogin implements Zend_Auth_Adapter_Interface{
    // array containing authenticated user record
    protected $_resultArray;

    // constructor
    // accepts email and password
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    // main authentication method
    // queries database for match to authentication credentials
    // returns Zend_Auth_Result with success/failure code
    public function authenticate()
    {
        $hashedPass =  Moldova_Utils::encryptpass($this->password);
        $q = Doctrine_Query::create()
            ->from('Moldova_Model_Accounts a')
            ->where('a.email = ? AND a.password = ?', array($this->email, $hashedPass));
        $result = $q->fetchArray();
        if (count($result) == 1) {
            $this->_resultArray = $result[0];
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->email, array());
        } else {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, null, array('Authentication unsuccessful'));
        }
    }

    // returns result array representing authenticated user record
    // excludes specified user record fields as needed
    public function getResultArray($excludeFields = null)
    {
        if (!$this->_resultArray) {
            return false;
        }

        if ($excludeFields != null) {
            $excludeFields = (array)$excludeFields;
            foreach ($this->_resultArray as $key => $value) {
                if (!in_array($key, $excludeFields)) {
                    $returnArray[$key] = $value;
                }
            }
            return $returnArray;
        } else {
            return $this->_resultArray;
        }
    }
}
