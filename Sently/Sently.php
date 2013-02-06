<?php

//To use Sent.ly to send out SMS, you must:
//1. Signup at - http://sent.ly
//2. Download and install the Sent.ly application from the Play Store on your Android phone
//   the URL is: https://play.google.com/store/apps/details?id=io.modem&hl=en
//3. Sign in on the Sent.ly client on your phone.
//Once you have completed the 3 steps, you can change this page to include
//the Sent.ly email and password in the appropriate variables.
//
//This page should be copied to your server and called like:
//http://yourserver.com/sender.php?message=Your+url+encoded+message&to=%2byourfullnumberwithcountrycode
//Example: To send an SMS to a singapore number 83887908, call this page like:
//http://yourserver.com/sender.php?message=Hi+Varun&to=%2b6583887908 

namespace Sently;

class Sently
{
    /**
     * Sent.ly Webservice URL
     */
    const SENTLY_SMS_URL = 'https://sent.ly/command/sendsms';

    /**
     * Sent.ly Account E-Mail
     * 
     * @var string 
     */
    protected $email;

    /**
     * Sent.ly Account Password
     * 
     * @var string 
     */
    protected $password;

    /**
     * Constructor
     * 
     * @param string $email     Sent.ly Account E-Mail
     * @param string $password  Sent.ly Account Password
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Send SMS using Sent.ly
     * 
     * @param string $message
     * @param string $destination
     * 
     * @return boolean
     */
    public function sendSms($message, $destination)
    {
        $paramArray = array(
            'username' => $this->email,
            'password' => $this->password,
            'text' => $message,
            'to' => $destination
        );

        $params = '';
        foreach ($paramArray as $name => $value) {
            $params .= urlencode($name) . '=' . urlencode($value) . '&';
        }
        $params = substr($params, 0, strlen($params) - 1);

        $ch = curl_init(static::SENTLY_SMS_URL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        $isWindows = defined('PHP_WINDOWS_VERSION_MAJOR');
        if ($isWindows) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        try {
            $response = curl_exec($ch);
        } catch (\Exception $e) {
            return false;
        }

        $responseParts = explode(':', $response);

        if (count($responseParts) == 2) {
            if ($responseParts[0] == 'Id') {
                return true;
            }
        }

        return false;
    }

}
