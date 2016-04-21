<?php namespace BongardeTracker\Controllers;

use SforceEnterpriseClient as SforceEnterpriseClient;
use SforcePartnerClient as SforcePartnerClient;
use SforceSoapClient as SforceSoapClient;
use stdClass as stdClass;
use SObject as SObject;

use BongardeTracker\Helper as Helper;

class SalesforceController
{
    private $mySforceConnection;
    private $mySforcePartnerConnection;
    private $current_user;

    public function __construct($current_user) {
        $this->current_user = $current_user;

        $this->mySforceConnection = new SforceEnterpriseClient();
        $this->mySforcePartnerConnection = new SforcePartnerClient();

        $this->mySforcePartnerConnection->createConnection(Helper::asset('/sforce/soapclient/partner.wsdl.xml'));

        $this->mySforceConnection->createConnection(Helper::asset('/sforce/soapclient/enterprise.wsdl.xml'));
        $this->mySforcePartnerConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);
        if (isset($_SESSION['enterpriseSessionId'])) {
            $location = $_SESSION['enterpriseLocation'];
            $sessionId = $_SESSION['enterpriseSessionId'];

            $this->mySforceConnection->setEndpoint($location);
            $this->mySforceConnection->setSessionHeader($sessionId);

        } else {
            $this->mySforceConnection->login(USERNAME, PASSWORD.SECURITY_TOKEN);

            $_SESSION['enterpriseLocation'] = $this->mySforceConnection->getLocation();
            $_SESSION['enterpriseSessionId'] = $this->mySforceConnection->getSessionId();

        }
    }

    public function fetchRecords($thisCustomer) {
        try {
            $query = "SELECT Id, Name FROM Account WHERE Id in (SELECT AccountId FROM Contact WHERE Email='$thisCustomer->email') LIMIT 1";
        } catch (Exception $e) {

        }

        $response = $this->mySforceConnection->query($query);
        $sfAccId = null;
        $sfAccName = null;



        foreach ($response->records as $record) {
            $sfAccId = $record->Id;
            $sfAccName = $record->Name;

        }
        if ($sfAccId == null && $sfAccName == null) {
            return $this->createRecords($thisCustomer);
        } else {
            update_user_meta($this->current_user->ID, 'sfAccId', $sfAccId);
            update_user_meta($this->current_user->ID, 'sfAccName', $sfAccName);
            return array('sfAccId' => $sfAccId, 'sfAccName' => $sfAccName);
        }

    }

    public function fetchCustomer($thisCustomer) {

    }

    public function fetchOpp($thisCustomer) {

        $oppQuery = "SELECT Owner.Name, Amount FROM Opportunity WHERE Id in (SELECT OpportunityId FROM OpportunityContactRole WHERE Contact.Email = '$thisCustomer->email') AND Id in (SELECT OpportunityId FROM OpportunityLineItem WHERE Product__r.Family LIKE 'OHS%') ORDER BY CloseDate DESC LIMIT 1";

        try {
            $oppResponse = $this->mySforceConnection->query($oppQuery);
        } catch (Exception $e) {

        }
        $oppAmount = null;
        $oppOwnerName = null;

        foreach ($oppResponse->records as $record) {
            $oppAmount = $record->Amount;
            $oppOwnerName = $record->Owner->Name;
            // $_SESSION['oppAmount'] = $record->Amount;
            // $_SESSION['oppOwnerName'] = $record->Owner;
        }
        if ($oppAmount && $oppOwnerName) {
            update_user_meta($this->current_user->ID, 'sfOppAmount', $oppAmount);
            update_user_meta($this->current_user->ID, 'sfOppOwner', $oppOwnerName);
            update_user_meta($this->current_user->ID, 'sfRefreshDate', time());
        }
            return array('oppAmount'=>$oppAmount, 'oppOwner' => $oppOwnerName);
    }

    public function createRecords($thisCustomer) {
        $chkQuery = "SELECT Id, Name FROM Account WHERE Name='$thisCustomer->company' LIMIT 1";

        try {
            $chkResponse = $this->mySforceConnection->query($chkQuery);
        } catch (Exception $e) {

        }
        $chkAccId = null;
        $chkAccName = null;

        foreach ($chkResponse as $record) {
            $chkAccId = $record->Id;
            $chkAccName = $record->Name;

        }
        if ($chkAccId == null && $chkAccName == null) {
            try {

                // Start Partner API Call
                $accFields = array (
                    'Name' => $thisCustomer->company
                );

                $accsObject = new SObject();
                $accsObject->fields = $accFields;
                $accsObject->type = 'Account';

                echo "**** Creating the following:\r\n";
                $createAccResponse = $this->mySforcePartnerConnection->create(array($accsObject));


                $AccIds = array();
                $newSfAccId = null;
                $sfAccName = null;
                foreach ($createAccResponse as $createResult) {
                    $newSfAccId = $createResult->id;
                    $sfAccName = $thisCustomer->company;
                }
                //End Partner API calls


            } catch (Exception $e) {
                echo $e->faultstring;
                echo $this->mySforceConnection->getLastRequest();
            }
            if ($newSfAccId && $sfAccName) {
                update_user_meta($this->current_user->ID, 'sfAccId', $newSfAccId);
                update_user_meta($this->current_user->ID, 'sfAccName',$sfAccName);
                update_user_meta($this->current_user->ID, 'sfRefreshDate', time());

                try {

                    // Start Partner API Call
                    $cFields = array (
                        'FirstName' => $thisCustomer->firstName,
                        'LastName' => $thisCustomer->lastName,
                        'Email' => $thisCustomer->email,
                        'AccountId' => $newSfAccId

                    );

                    $cObject = new SObject();
                    $cObject->fields = $cFields;
                    $cObject->type = 'Contact';

                    echo "**** Creating the following:\r\n";
                    $createContactResponse = $this->mySforcePartnerConnection->create(array($cObject));

                    //End Partner API calls


                } catch (Exception $e) {
                    echo $e->faultstring;
                    echo $this->mySforceConnection->getLastRequest();
                }

                return array('sfAccId' => $newSfAccId, 'sfAccName' => $sfAccName);
            }




        } else {
            update_user_meta($this->current_user->ID, 'sfAccId', $chkAccId);
            update_user_meta($this->current_user->ID, 'sfAccName', $chkAccName);
            update_user_meta($this->current_user->ID, 'sfRefreshDate', time());
            return array('sfAccId' => $chkAccId, 'sfAccName' => $chkAccName);
        }

    }

}