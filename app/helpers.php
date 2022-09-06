<?php

use Jenssegers\Agent\Agent;
use hisorange\BrowserDetect\Parser as Browser;


function xx_clean($str){

    $newTag = "";
    $org_script = array("<script>","</script>",
        "'","{{","}}");
    $new_script = array("❮script❯","❮/script❯",
        "׳","｛｛","｝｝");
    if(strpos($str,'<script>') !== false ||
        strpos($str,"'") !== false ||
        strpos($str,"{{") !== false ||
        strpos($str,"}}") !== false):
        $newTag = str_replace($org_script,$new_script,$str);
        else:
        $newTag = $str;
    endif;

    return $newTag;



}


function write2text($file,$text){
    // $file = file path
    $f2 = fopen($file,"a+");
    $content = " {$text} ";
    fwrite($f2,$content);
    fclose($f2);

}




// getUserIp
function getUserIp(){
    return Request::ip();
}

// get browser 
function getUserBrowser(){
  //  return Request::userAgent();
    $agent = new Agent();

    $browser = $agent->browser();
    $browser_ver = $agent->version($browser); 
    return $browser.' '.$browser_ver;

}

function getUserOs(){
  $agent = new Agent();
  $platform = $agent->platform();
  $platform_ver = $agent->version($platform);
  return $platform.' '.$platform_ver; 
}

function getUserDevice(){
  $agent = new Agent();
  $device = $agent->device();
  return $device;
}

