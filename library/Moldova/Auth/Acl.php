<?php
/**
 * Created by Moldova Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 2/24/11
 * Time: 11:43 PM
 */
 
class Moldova_Auth_Acl extends Zend_Acl
{
    public function __construct(){
        $this->addRole(new Zend_Acl_Role(Moldova_Auth_Roles::GUEST));
        $this->addRole(new Zend_Acl_Role(Moldova_Auth_Roles::USER),Moldova_Auth_Roles::GUEST);
        $this->addRole(new Zend_Acl_Role(Moldova_Auth_Roles::ADMIN));

        $this->addResource(new Zend_Acl_Resource(Moldova_Auth_Resources::GUEST_AREA));
        $this->addResource(new Zend_Acl_Resource(Moldova_Auth_Resources::USER_AREA));
        $this->addResource(new Zend_Acl_Resource(Moldova_Auth_Resources::ADMIN_AREA));

        $this->allow(Moldova_Auth_Roles::GUEST, Moldova_Auth_Resources::GUEST_AREA);
        $this->allow(Moldova_Auth_Roles::USER, Moldova_Auth_Resources::USER_AREA);
        $this->allow(Moldova_Auth_Roles::ADMIN, Moldova_Auth_Resources::ADMIN_AREA);


    }
}
