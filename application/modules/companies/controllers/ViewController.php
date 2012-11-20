<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/18/11
 * Time: 1:31 AM
 */
 
class Companies_ViewController extends Zend_Controller_Action{

    public function preDispatch()
    {

    }


    public function init()
    {

    }

    public function indexAction()
    {
        $req = $this->getRequest()->getParams();
        if($this->validateCompany($req)){
            $session = new Zend_Session_Namespace('accounts');

            $company_url = $req['company_url'];

            $q = Doctrine_Query::create()
                                ->select("c.*, c.desc_".$session->locale." AS desc, sc.name_".$session->locale." AS subcat, r.region_".$session->locale." AS region, l.locality_".$session->locale." AS locality")
                                ->from('Moldova_Model_Companies c')
                                ->leftJoin('c.Moldova_Model_CompaniesSubcategories sc')
                                ->leftJoin('c.Moldova_Model_Regions r')
                                ->leftJoin('c.Moldova_Model_Localities l')
                                ->where('c.company_url = ?', $company_url);

            $breadResult = $q->fetchArray();
            $breadResult[0]['logo_img'] =  "uploads/companies/{$breadResult[0]['company_id']}/{$breadResult[0]['logo_img']}";

            $q_images = Doctrine_Query::create()
                                ->from('Moldova_Model_CompaniesImages ci')
                                ->where('ci.company_id = ?', $breadResult[0]['company_id']);

            $this->view->companyImages = $q_images->fetchArray();

            $this->view->company = $breadResult;
            
            //dump($q->fetchArray());
            $this->view->breadcrumb = array("companies" => $this->view->translate('companies'), $breadResult[0]['company_url'] =>  $breadResult[0]['name']);
        }else {
            throw new Zend_Controller_Action_Exception("Invalid input");
        }
    }

    private function validateCompany($req)
    {
        $valid = false;
        $company_url = $req['company_url'];
        if($company_url != 'n'){
            $filters = array(
                    'company_url' => array('HtmlEntities', 'StripTags', 'StringTrim'),
            );

            $q=Doctrine_Query::create()
                    ->select('c.company_url')
                    ->from('Moldova_Model_Companies c')
                    ->where('c.is_deleted = ?', 0);
            $urls = $q->execute();

            foreach($urls as $url) {
                $comp_haystack[] = $url->company_url;
            }

            $validators = array(
                    'company_url' => array(
                        array('InArray', 'haystack' => $comp_haystack)
                     )
                );

            $input = new Zend_Filter_Input($filters, $validators);
            $input->setData($req);

            if($input->isValid()){
                $valid = true;
            }
        }


        return $valid;
    }
}
