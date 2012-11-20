<?php
/**
 * Created by Cojocaru Vadim.
 * User: vadim
 * Email: cojocaru.vadim@gmail.com
 * Website: www.cojocaruvadim.com
 * Date: 3/15/11
 * Time: 10:17 PM
 */
 
class Moldova_Utils {



    public static $companies_status = array(
            array('status' => 0, 'name' => 'New'),
            array('status' => 1, 'name' => 'Published')
    );

    public static function customPrint($array){
        echo "/*";
        print_r($array);
        echo "*/";
    }


    public static function checkAdmin($url){
        $return = false;
        $session = new Zend_Session_Namespace('admins');
        $acl = new Moldova_Auth_Acl();
        if(!$acl->isAllowed($session->role, Moldova_Auth_Resources::ADMIN_AREA)){
            $session->requestURL = $url;
            $session->role = Moldova_Auth_Roles::GUEST;

        }else{
            $return = true;
        }
        return $return;
    }

    public static function checkAccount($url){
        $return = false;
        $session = new Zend_Session_Namespace('accounts');
        $acl = new Moldova_Auth_Acl();
        if(!$acl->isAllowed($session->role, Moldova_Auth_Resources::USER_AREA)){
            $session->requestURL = $url;
            $session->role = Moldova_Auth_Roles::GUEST;

        }else{
            $return = true;
        }
        return $return;
    }

    public static function cleanUrl($str, $replace=array(), $delimiter='-') {
        if( !empty($replace) ) {
            $str = str_replace((array)$replace, '', $str);
        }

        $str = self::Transliterate($str);

        //aăaâaîașaț     a\u0103a\u00e2a\u00eea\u015fa\u0163
        //$str = preg_replace(array("/ă/","/â/", "/î/", "/ș/", "/ț/", "/Ă/","/Â/", "/Î/", "/Ș/", "/Ț/"), array("a", "a", "i", "s", "t", "a", "a", "i", "s", "t"), $str);
        $clean = preg_replace(array("/\x{0103}/u", "/\x{00e2}/u", "/\x{00ee}/u", "/\x{0219}/u", "/\x{021b}/u", "/Ă/","/Â/", "/Î/", "/Ș/", "/Ț/", "/\x{015f}/u", "/\x{0163}/u"), array("a", "a", "i", "s", "t", "a", "a", "i", "s", "t", "s", "t"), $str);


        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $clean);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public static function Transliterate($str, $encIn='utf-8', $encOut='utf-8'){

        $cyr=array(
            "Щ",  "Ш", "Ч", "Ц","Ю", "Я", "Ж", "А","Б","В","Г","Д","Е","Ё","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х", "Ь","Ы","Ъ","Э","Є","Ї",
            "щ",  "ш", "ч", "ц","ю", "я", "ж", "а","б","в","г","д","е","ё","з","и","й","к","л","м","н","о","п","р","с","т","у","ф","х", "ь","ы","ъ","э","є","ї");

        $lat=array(
            "Shh","Sh","Ch","C","Ju","Ja","Zh","A","B","V","G","D","Je","Jo","Z","I","J","K","L","M","N","O","P","R","S","T","U","F","Kh","I","Y","I","E","Je","Ji",
            "shh","sh","ch","c","ju","ja","zh","a","b","v","g","d","je","jo","z","i","j","k","l","m","n","o","p","r","s","t","u","f","kh","i","y","i","e","je","ji"
        );

        $str = iconv($encIn, "utf-8", $str);
        for($i=0; $i<count($cyr); $i++){
            $c_cyr = $cyr[$i];
            $c_lat = $lat[$i];
            $str = str_replace($c_cyr, $c_lat, $str);
        }
        $str = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]e/", "\${1}e", $str);
        $str = preg_replace("/([qwrtpsdfghklzxcvbnmQWRTPSDFGHKLZXCVBNM]+)[jJ]/", "\${1}'", $str);
        $str = preg_replace("/([eyuioaEYUIOA]+)[Kk]h/", "\${1}h", $str);
        $str = preg_replace("/^kh/", "h", $str);
        $str = preg_replace("/^Kh/", "H", $str);

        return iconv("utf-8", $encOut, $str);
    }

    public static function encryptpass($password) {
        if(!empty($password)) {
            $key = 	'oYenhuobE577FzAixKPe9qQkptHbFx'.
                    'uoC0PcdPfNuQGnELzvI3FGVWl27k3v'.
                    'mqoymbRV09QWwdmq6c7AWysFP43LtM'.
                    'x8MDriq73T2PVJBGiyxQUxe4viLiHQ'.
                    'In4buglQcq3024DCw9sVFO0mFVe6Jq'.
                    'cPUuCjzYWyfgaSe97H6DBLIvAY9qbN'.
                    'xozZtZ0Id9Coy7daJDfx4w8BsyfFNr';

            $hash1 = sha1(md5($key));
            $hash2 = sha1(md5($password));
            $password = md5(sha1($hash1 . $hash2));

            return $password;
        }
    }
    
    public static function saveFeedback(){
        die;
        /*$f = new Moldova_Model_Feedback();
        $f->name = 'Irina';
        $f->message_en_US = 'After I created my own blog on the internet, I\'ve registered it on this site and I noticed that the number of visitors of the blog has increased. And this number increases continuously.';
        $f->message_ro_MD = 'Dupa ce am creat propriul meu blog pe internet, l-am înregistrat și pe acest site și am observat că numarul de vizitatori ai blogului s-a marit. Și acest număr crește în continuu.';
        $f->message_ru_RU = 'Когда я создала свой собственный блог в Интернете, я зарегистрировала его на этом сайте, и возросло количество посетителей блога. И это число постоянно увеличивается.';
        $f->duty_en_US = 'Journalist';
        $f->duty_ro_MD = 'Jurnalist';
        $f->duty_ru_RU = 'Журналист';
        $f->save();*/

        $fe = Doctrine::getTable('Moldova_Model_Feedback')->find(9);
        $fe->message_ru_RU = "При регистрации свого бизнеса на этом сайте я начал получать больше звонков и писем от новых клиентов. Я сэкономил время и деньги для создания собственного сайта, но в то же время пользуюсь поддержкой и продвижением в интернете.";
        $fe->save();
        //
    }

    public static function far2Cels($farenheit){
        $celsius = ($farenheit -32 ) * 5/9;
        return round($celsius);
    }

    public static function initiateMail(){
        //$tr = new Zend_Mail_Transport_Smtp('mail.example.com');
        //Zend_Mail::setDefaultTransport($tr);
        $config = array('name' => 'office@delta-md.com');
        $transport = new Zend_Mail_Transport_Smtp('mail.delta-md.com', $config);

        Zend_Mail::setDefaultFrom('office@delta-md.com', 'Delta.md');
        Zend_Mail::setDefaultReplyTo('office@delta-md.com','Delta.md');

        $mail = new Zend_Mail();
        $mail->setBodyText('This is the text of the mail.');
        //$mail->setFrom('cojocatuvadim10@gmail.com', 'Vadim Test');
        $mail->addTo('cojocaru.vadim@gmail.com', 'Vadim Real');
        $mail->setSubject('TestSubject');
        $mail->send($transport);

        Zend_Mail::clearDefaultFrom();
        Zend_Mail::clearDefaultReplyTo();
    }
    
/*
    public static function insertLoc(){
        $cont = '
            <option value="100000">del</option>
        ';

        $cont = file_get_contents('loc.php');
        //echo $cont; die;

        $pattern = "/value=\"(.*)\">(.*)</";

        preg_match_all($pattern, $cont, $matches);

        //print_r($matches);

        $skip = false;
        $current_reg_id = 0;

        for($i=0; $i<count($matches[0]); $i++){

            if($skip === true){
              $skip = false;
              continue;
            }

            if($matches[2][$i] == 'del'){
                //insert region $matches[2][$i+1]

                $region = new Moldova_Model_Regions();
                $region->region_en_US = $matches[2][$i+1];
                $region->region_ro_MD = $matches[2][$i+1];
                $region->region_ru_RU = $matches[2][$i+1];
                $region->save();
                $current_reg_id = $region->getIncremented();
                //echo "Region: ".$matches[2][$i+1]."<br />";
                //$current_reg_id++;
                $skip = true;
                continue;
            }
            //echo "Locality-$current_reg_id: " . $matches[2][$i]."<br />";
                $loc = new Moldova_Model_Localities();
                $loc->locality_en_US = $matches[2][$i];
                $loc->locality_ro_MD = $matches[2][$i];
                $loc->locality_ru_RU = $matches[2][$i];
                $loc->region_id = $current_reg_id;
                $loc->save();
        }

    }

    public static function insertLocTwo(){

           $q_r = Doctrine_Query::create()
                            ->select('r.region_ro_MD, r.region_id')
                            ->from('Moldova_Model_RegionsT r')
                            ->where('r.region_id != ?', 1)
                            ->andWhere('r.region_id != ?', 2)
                            ->orderBy('r.region_ro_MD');
        
        $result_r = $q_r->fetchArray();
        foreach($result_r as $item){
            $region = new Moldova_Model_Regions();
            $region->region_en_US = $item['region_ro_MD'];
            $region->region_ro_MD = $item['region_ro_MD'];
            $region->region_ru_RU = $item['region_ro_MD'];
            $region->save();
            $current_reg_id = $region->getIncremented();

                    $q_l = Doctrine_Query::create()
                            ->select('l.locality_ro_MD')
                            ->from('Moldova_Model_LocalitiesT l')
                            ->where('l.region_id = ?', $item['region_id']);
            
            $result_l = $q_l->fetchArray();

            foreach($result_l as $item_l){
                $loc = new Moldova_Model_Localities();
                $loc->locality_en_US = $item_l['locality_ro_MD'];
                $loc->locality_ro_MD = $item_l['locality_ro_MD'];
                $loc->locality_ru_RU = $item_l['locality_ro_MD'];
                $loc->region_id = $current_reg_id;
                $loc->save();
                //dump($item_l);
            }

            //dump($item);
        }

        //return $result;
    }

    public static function insertlocurl(){
                   $q_r = Doctrine_Query::create()
                            ->select('l.locality_ro_MD, l.locality_id, l.region_id, r.region_url')
                            ->from('Moldova_Model_Localities l')
                           ->leftJoin('l.Moldova_Model_Regions r');
                            //->where('l.locality_id > ?', 1);

                    $result_r = $q_r->fetchArray();

                    //dump($result_r); die;

                    foreach($result_r as $item){
                        $url = self::cleanUrl($item['locality_ro_MD']);

                        $q_t = Doctrine_Query::create()
                            ->select('l.locality_url')
                            ->from('Moldova_Model_Localities l')
                            ->where('l.locality_url = ?', $url);

                        if(count($q_t->fetchArray()) > 0){
                            //echo $item['Moldova_Model_Regions']['region_url'];
                            $comosed = $item['Moldova_Model_Regions']['region_url'] . "-" .$item['locality_ro_MD'];
                            echo $comosed . "<br />";
                            $url = self::cleanUrl($comosed);
                        }

                        $q = Doctrine_Query::create()
                        ->update('Moldova_Model_Localities l')
                        ->set('l.locality_url', '?', $url)
                        ->addWhere('l.locality_id = ?', $item['locality_id']);
                        $q->execute();


                    }

    }
 
 */
/*
    public static function DFL(){
            $cont = file_get_contents('http://meteo2.md/ro/Raioane/');

            $pattern = '/(<ul \w*>)(.*)(<\/ul>)/ismxU';
            $pattern_li = '/<li><a .* http:\/\/meteo2.md\/ro\/Localitati\/(.*)\/ .*>(.*)<\/a><\/li>/ismxU';

            preg_match_all($pattern, $cont, $matches);
            $result_urls = array();
            $result_names = array();
            foreach($matches[0] as $match){
              //echo $match . "\n\n";
              preg_match_all($pattern_li, $match, $matchess);
              //print_r($matchess);
                $i = 0;
              foreach($matchess as $key => $regions){
                  $i++;

                    if($i == 1){
                      continue;
                    }
                    foreach($regions as $region){
                      if($i == 2){
                        $result_urls[] = $region;
                      }
                      if($i == 3){
                        $result_names[] = $region;
                      }
                    }
                    //print_r($regions);
                    //echo "<br />";
                  if($i==3) $i=0;

                }
            }
            //print_r($result_urls);
            //print_r($result_names);

            for($i=0; $i<count($result_urls); $i++){
                echo $i . ": " . $result_urls[$i] . " - " . $result_names[$i] . "<br />";

                if($i != 32 && $i != 33){
                    $region = new Moldova_Model_Regions();
                    $region->region_en_US = $result_names[$i];
                    $region->region_ro_MD = $result_names[$i];
                    $region->region_ru_RU = $result_names[$i];
                    $region->region_url = $result_urls[$i];
                    $region->save();
                }
            }
            die;
    }
    public static function DFL2(){

            
           $q_r = Doctrine_Query::create()
                            ->select('r.*')
                            ->from('Moldova_Model_Regions r');

            $results = $q_r->fetchArray();


            foreach($results as $result_r){

                $cont = file_get_contents('http://meteo2.md/ro/Localitati/'.$result_r['region_url'].'/');

                $pattern = '/(<ul \w*>)(.*)(<\/ul>)/ismxU';
                $pattern_li = '/<a .* http:\/\/meteo2.md\/ro\/Prognoza_Meteo\/'.$result_r['region_url'].'\/(.*)\/ .*>(.*)<\/a>/ismxU';
            //echo $pattern_li; die;

                preg_match_all($pattern, $cont, $matches);


                $result_urls = array();
                $result_names = array();
                foreach($matches[0] as $match){
                  //echo $match . "\n\n";
                  preg_match_all($pattern_li, $match, $matchess);
                    $i = 0;
                  foreach($matchess as $key => $regions){
                      $i++;

                        if($i == 1){
                          continue;
                        }
                        foreach($regions as $region){
                          if($i == 2){
                            $result_urls[] = $region;
                          }
                          if($i == 3){
                            $result_names[] = $region;
                          }
                        }
                        //print_r($regions);
                        //echo "<br />";
                      if($i==3) $i=0;

                    }
                }
                //print_r($result_urls);
                //print_r($result_names);

                for($i=0; $i<count($result_urls); $i++){
                    echo $result_r['region_id'] . ": " . $result_urls[$i] . " - " . $result_names[$i] . "<br />";


                $loc = new Moldova_Model_Localities();
                $loc->locality_en_US = $result_names[$i];
                $loc->locality_ro_MD = $result_names[$i];
                $loc->locality_ru_RU = $result_names[$i];
                $loc->locality_url = $result_urls[$i];
                $loc->region_id = $result_r['region_id'];
                $loc->save();

                }
                echo "<hr />";
            }


            die;
    }*/
/*    public static function generateXML(){
        die;
                           $q_r = Doctrine_Query::create()
                            ->select('l.locality_url, r.region_url')
                            ->from('Moldova_Model_Localities l')
                            ->leftJoin('l.Moldova_Model_Regions r');
                            $xml = '';
                            foreach($q_r->fetchArray() as $item){
                                //echo $item['locality_url'];
                                $xml .= <<<TEXT
<url>
  <loc>http://www.delta.md/companies/localities/{$item['Moldova_Model_Regions']['region_url']}/{$item['locality_url']}</loc>
  <lastmod>2011-06-13T12:43:13+00:00</lastmod>
  <changefreq>weekly</changefreq>
  <priority>0.51</priority>
</url>

TEXT;

                            }
echo $xml; die;
                            //dump($q_r->fetchArray()); die;
                            //->where('l.locality_id > ?', 1);
    }*/

    public static function getCoord(){
           die; 
           $q_r = Doctrine_Query::create()
                    ->select('l.locality_url, r.region_url, l.locality_id, l.region_id, r.region_ro_MD, l.locality_ro_MD')
                    ->from('Moldova_Model_Localities l')
                    ->leftJoin('l.Moldova_Model_Regions r')
                    ->where('l.has_coordinates = ?', 0)
                    ->limit(50);

            $results = $q_r->fetchArray();

            //dump($results); die;

            foreach($results as $res){
                //echo $res['region_url'];
                $address = 'Moldova+'.$res['Moldova_Model_Regions']['region_ro_MD'].'+'.$res['locality_ro_MD'];
                $loc = self::getGeoLocation($address);
                if($loc != false){
                    echo "Este hahaha <br />";
                    $locb = Doctrine::getTable('Moldova_Model_Localities')->find($res['locality_id']);
                    $locb->has_coordinates = 1;
                    $locb->lat = $loc->lat;
                    $locb->lng = $loc->lng;
                    $locb->save();
                    //dump($loc);
                }else{
                    echo "http://maps.google.com/maps/api/geocode/json?address={$address}&sensor=false<br />";
                }

                //echo "<hr />";
            }
            die;
    }

    public static function getGeoLocation ($address) {
		 $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";
		 //echo $url; die();
		 $geo_data = file_get_contents($url);
		 $geo_data = json_decode($geo_data);
		 if($geo_data->status === "ZERO_RESULTS"){
			return false;
		 }else if($geo_data->status === "OK"){
			$location = $geo_data->results['0']->geometry->location;
			return $location;
		 }

	}
}
