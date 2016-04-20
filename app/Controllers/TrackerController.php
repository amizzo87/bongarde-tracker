<?php namespace BongardeTracker\Controllers;

use BongardeTracker\Controllers\SalesforceController;

use ChargeBee_Plan as ChargeBee_Plan;
use ChargeBee_Customer as ChargeBee_Customer;
use ChargeBee_Subscription as ChargeBee_Subscription;
use SforceEnterpriseClient as SforceEnterpriseClient;
use stdClass as stdClass;

class TrackerController {

    private $current_user;
    private $meta_sfAccId;
    private $meta_sfAccName;
    private $meta_sfOppAmount;
    private $meta_sfOppOwner;
    private $meta_sfRefreshDate;
    private $thisCustomer;
    private $thisSub;
    private $thisPlan;

    public function __construct($current_user) {
        if(is_user_logged_in() /* && !is_admin() */) {

            $this->current_user = $current_user;

            // $cbsubscription = apply_filters("cb_get_subscription", $user->ID);

            try {
                $customerResult = ChargeBee_Customer::retrieve($this->current_user->ID);
                $subResult = ChargeBee_Subscription::retrieve($this->current_user->ID);


                $this->thisCustomer = $customerResult->customer();
                $this->thisSub = $subResult->subscription();

                $planResult = ChargeBee_Plan::retrieve($this->thisSub->planId);
                $this->thisPlan = $planResult->plan();

            } catch (Exception $e) {
                throw $e;
            }


            if($this->thisCustomer || $this->thisSub) {

                    $this->meta_sfAccId = get_user_meta($this->current_user->ID, 'sfAccId', true);
                    $this->meta_sfAccName = get_user_meta($this->current_user->ID, 'sfAccName', true);
                    $this->meta_sfOppAmount = get_user_meta($this->current_user->ID, 'sfOppAmount', true);
                    $this->meta_sfOppOwner = get_user_meta($this->current_user->ID, 'sfOppOwner', true);
                    $this->meta_sfRefreshDate = get_user_meta($this->current_user->ID, 'sfRefreshDate', true);

                if(!$this->meta_sfAccName || !$this->meta_sfAccId || $this->meta_sfRefreshDate) {

                    $sfController = new SalesforceController($this->current_user);
                    $sfController->fetchRecords($this->thisCustomer);
                    $sfController->fetchOpp($this->thisCustomer);
                }


            }
        }
        // $cbsubscription = apply_filters("cb_get_subscription", $user->ID);
    }

    /**
     * Render a twig template to enqueue. BAM!
     */
    public function TotangoCore()
    {

        return view('@BongardeTracker/totango.twig', ['service_id'   => get_option( 'bongarde_tracker_options' )['totango_sid'],
            'user_id' => $this->thisCustomer->email,
            'user_firstName' => $this->thisCustomer->firstName,
            'user_lastName' => $this->thisCustomer->lastName,
            'acc_id' => $this->meta_sfAccId,
            'acc_name' => $this->meta_sfAccName,
            'acc_status' => $this->thisSub->status,
            'acc_ofid' => $this->meta_sfAccId,
            'module' => null,
            'planId' => $this->thisPlan,
            'product' => get_option( 'bongarde_tracker_options' )['product_name'],
            'acc_createdAt' => date('c', $this->thisSub->startedAt)

        ]);
    }
}