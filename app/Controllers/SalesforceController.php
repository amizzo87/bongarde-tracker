<?php namespace BongardeTracker\Controllers;

use SforceEnterpriseClient as SforceEnterpriseClient;
use stdClass as stdClass;

class SalesforceController
{
    private $mySforceConnection;
    private $current_user;

    public function __construct($current_user) {
        $this->current_user = $current_user;

        $this->mySforceConnection = new SforceEnterpriseClient();
        $this->mySforceConnection->createConnection(ABSPATH . "/sforce/soapclient/enterprise.wsdl.xml");
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

        $query = "SELECT Id, Name FROM Account WHERE Id in (SELECT AccountId FROM Contact WHERE Email='$thisCustomer->email') LIMIT 1";


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


        $oppResponse = $this->mySforceConnection->query($oppQuery);
        $oppAmount = null;
        $oppOwnerName = null;

        foreach ($oppResponse->records as $record) {
            $oppAmount = $record->Amount;
            $oppOwnerName = $record->Owner->Name;
            // $_SESSION['oppAmount'] = $record->Amount;
            // $_SESSION['oppOwnerName'] = $record->Owner;
        }
            update_user_meta($this->current_user->ID, 'sfOppAmount', $oppAmount);
            update_user_meta($this->current_user->ID, 'sfOppOwner',$oppOwnerName);
            update_user_meta($this->current_user->ID, 'sfRefreshDate', time());
            return array('oppAmount'=>$oppAmount, 'oppOwner' => $oppOwnerName);
    }

    public function createRecords($thisCustomer) {
        $chkQuery = "SELECT Id, Name FROM Account WHERE Name='$thisCustomer->company' LIMIT 1";

        $chkResponse = $this->mySforceConnection->query($chkQuery);
        $chkAccId = null;
        $chkAccName = null;

        foreach ($chkResponse as $record) {
            $chkAccId = $record->Id;
            $chkAccName = $record->Name;

        }
        if ($chkAccId == null && $chkAccName == null) {

            $account = new stdclass();
            $account->Name = $thisCustomer->company;
            $accResponse = $this->mySforceConnection->create(array($account), 'Account');

            $newSfAccId = null;
            $sfAccName = null;
            foreach ($accResponse as $accResult) {
                //print_r($accResult);
                //echo 'ID is: ' . $accResult->id;
                $newSfAccId = $accResult->id;
                $sfAccName = $thisCustomer->company;
            }
            if ($newSfAccId && $sfAccName) {
                update_user_meta($this->current_user->ID, 'sfAccId', $newSfAccId);
                update_user_meta($this->current_user->ID, 'sfAccName',$sfAccName);
                update_user_meta($this->current_user->ID, 'sfRefreshDate', time());
            }
            $cRecords = new stdclass();
            $cRecords->FirstName = $thisCustomer->firstName;
            $cRecords->LastName = $thisCustomer->lastName;
            $cRecords->Email = $thisCustomer->email;
            $cRecords->AccountId = $newSfAccId;

            return $this->mySforceConnection->create(array($cRecords), 'Contact');


        } else {
            update_user_meta($this->current_user->ID, 'sfAccId', $chkAccId);
            update_user_meta($this->current_user->ID, 'sfAccName', $chkAccName);
            update_user_meta($this->current_user->ID, 'sfRefreshDate', time());
            return array('sfAccId' => $chkAccId, 'sfAccName' => $chkAccName);
        }

    }

}