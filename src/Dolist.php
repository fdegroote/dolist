<?php

class Dolist
{
    const WSDL = "http://api.emt.dolist.net/V3/";

    const WSDL_CLIENT = self::WSDL."AuthenticationService.svc?wsdl";
    const WSDL_MESSAGE = self::WSDL."MessageService.svc?wsdl";
    const WSDL_TEMPLATE = self::WSDL."TemplateService.svc?wsdl";

    const SOAP_CLIENT = self::WSDL."AuthenticationService.svc/soap1.1";
    const SOAP_MESSAGE = self::WSDL."MessageService.svc/soap1.1";
    const SOAP_TEMPLATE = self::WSDL."TemplateService.svc/soap1.1";


    private $soapClient;
    private $soapMessage;
    private $soapTemplate;
    private $accountId;
    private $authenticationKey;

    public function __construct()
    {
        $this->accountId = "";
        $this->authenticationKey = "";
        $this->soapClient = new SoapClient(self::WSDL_CLIENT, array("trace" => 1, "location" => self::SOAP_CLIENT));
        $this->soapMessage = new SoapClient(self::WSDL_MESSAGE, array("trace" => 1, "location" => self::SOAP_MESSAGE));
        $this->soapTemplate = new SoapClient(self::WSDL_TEMPLATE, array("trace" => 1, "location" => self::SOAP_TEMPLATE));
    }

    public function connectDoList()
    {
        try {
            if (file_exists("cache/cache.txt") && filemtime("cache/cache.txt") > 5) {
                $key = json_decode(file_get_contents('cache/cache.txt'));

                $token = array(
                    'AccountID' => $this->accountId,
                    'Key' => $key
                );

            } else {
                $params = array("AuthenticationKey" => $this->authenticationKey
                , "AccountID" => $this->accountId);

                $result = $this->soapClient->GetAuthenticationToken(array("authenticationRequest" => $params));

                $token = array(
                    'AccountID' => $this->accountId,
                    'Key' => $result->GetAuthenticationTokenResult->Key
                );

                $key = $result->GetAuthenticationTokenResult->Key;

                file_put_contents('cache/cache.txt', json_encode($key));
            }

            return $token;

        } catch (SoapFault $fault) {
            print $fault;

            $detail = $fault->detail;

            return $detail->ServiceException->Description;
        }
    }

    public function sendEmail($type, $attachements, $data, $isTest, $recipient, $contentType) {
        $token = $this->connectDoList();

        $message = array(
            'Attachments' => $attachements,
            'Data' => $data,
            'IsTest' => $isTest,
            'MessageContentType'=> $contentType,
            'Recipient'=> $recipient,
            'TemplateID' => $type
        );

        $email = $this->soapMessage->SendMessage(array("token" => $token, "message" => $message));

        echo $email->SendMessageResult;
    }
}
