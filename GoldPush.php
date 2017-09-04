#!/usr/bin/php -q
<?php

include("simple_html_dom.php");
include("phpagi.php");
require_once('phpagi-asmanager.php');

ob_implicit_flush(false);
set_time_limit(30);
error_reporting( E_ALL );

//Asterisk Manager Configuration
$ast_host="127.0.0.1";
$ast_user="admin";
$ast_pass="pass";
//API credential (you can get it from http://goldphone.goldnetgroup.com.au for free)
$api_user='user';
$api_pass='pass';

$agi = new AGI();

$agi -> verbose("Start GoldNet SIP Push Notification Solution");

$exten = $agi->get_variable("EXTEN");
$exten = $exten["data"];

$agi -> verbose($exten);

  $asm = new AGI_AsteriskManager();
  if($asm->connect($ast_host,$ast_user,$ast_pass))
  {
    $peer = $asm->command("sip show peer {$exten}");
    if(strpos($peer['data'], ':'))
    {
      $data = array();
      foreach(explode("\n", $peer['data']) as $line)
      {
        $a = strpos('z'.$line, ':') - 1;
        if($a >= 0) $data[trim(substr($line, 0, $a))] = trim(substr($line, $a + 1));
      }
      $contacts=explode(";",$data["Reg. Contact"]);
      $token=explode("=",$contacts[3]);
      $token=$token[1];
      $agi -> verbose($token);		
    }
    $asm->disconnect();
  }

if ($token != "")
{
    $url="http://goldpush.goldnetgroup.com/index.php";
    $agi -> verbose($url);
    $method="POST";
//    $data="token=$token&user=$api_user&pass=&$api_pass";
    $data= array(
        'token' => "$token",
        'user' => "$api_user",
        'pass' => "$api_pass"
    );


    $result=CallAPI($method,$url,$data);
    $agi -> verbose($result);
    
    $SIPPush1="Yes";
    $agi -> set_variable("SIPPush",$SIPPush1);
    $agi -> verbose($SIPPush);
}   
    function CallAPI($method, $url, $data=false,$api_user,$api_pass)
    {
        $curl = curl_init();
    
        switch ($method)
        {
            case "POST":
    	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    	    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/text'));
                curl_setopt($curl, CURLOPT_POST, 1);
    //             echo $data; 
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
    
    // Optional Authentication:
    // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // curl_setopt($curl, CURLOPT_USERPWD, "$api_user:$api_pass");
    
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
        $result = curl_exec($curl);
    
        curl_close($curl);
    
        return $result;
    }

?>
