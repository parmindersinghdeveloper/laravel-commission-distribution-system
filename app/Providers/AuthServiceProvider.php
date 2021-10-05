<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        $this->registerUserPolicies();

    }

    public function registerUserPolicies(){

        Gate::define('hideOptions',function($user){
            return $user->hasAccess('Hide-Options') === 'true'? true:false;
        });
        Gate::define('deleteId',function($user){
            return $user->hasAccess('Delete-ID') === 'true'? true:false;
        });
        Gate::define('blockMemberId',function($user){
            return $user->hasAccess('Block-Member-Id') === 'true'? true:false;
        });
        Gate::define('quickJoining',function($user){
            return $user->hasAccess('Quick-Joining') === 'true'? true:false;
        });
        Gate::define('welcomeLetter',function($user){
            return $user->hasAccess('Edit-Welcome-Letter') === 'true'? true:false;
        });
        Gate::define('happyBirthdayList',function($user){
            return $user->hasAccess('Happy-Birthday-List') === 'true'? true:false;
        });
        Gate::define('upperBarLinks',function($user){
            return $user->hasAccess('Upper-Bar-Links') === 'true'? true:false;
        });
        Gate::define('footerLinks',function($user){
            return $user->hasAccess('Footer-Links') === 'true'? true:false;
        });
        Gate::define('socialLinks',function($user){
            return $user->hasAccess('Social-Links') === 'true'? true:false;
        });
        Gate::define('about',function($user){
            return $user->hasAccess('About-us') === 'true'? true:false;
        });
        Gate::define('addressMaster',function($user){
            return $user->hasAccess('Address-Master') === 'true'? true:false;
        });
        Gate::define('logoMaster',function($user){
            return $user->hasAccess('Logo-Master') === 'true'? true:false;
        });
        Gate::define('popupMaster',function($user){
            return $user->hasAccess('Custom-Popup') === 'true'? true:false;
        });
        Gate::define('termsConditions',function($user){
            return $user->hasAccess('Terms-&-Conditions') === 'true'? true:false;
        });
        Gate::define('legalMaster',function($user){
            return $user->hasAccess('Legal-Docs') === 'true'? true:false;
        });

        Gate::define('sub-admin-rights',function($user){
            return $user->hasAccess('Sub-Admin-Rights') === 'true'? true:false;
        });
        Gate::define('Sub-admin',function($user){
            return $user->hasAccess('Sub-Admin') === 'true'? true:false;
        });
        Gate::define('gst-master',function($user){
            return $user->hasAccess('GST-Master') === 'true'? true:false;
        });
        Gate::define('admin-pass',function($user){
            return $user->hasAccess('Admin-Pass') === 'true'? true:false;
        });
        Gate::define('pan-card-reports',function($user){
            return $user->hasAccess('Pan-Card-Report') === 'true'? true:false;
        });
        Gate::define('withdrawal-request',function($user){
            return $user->hasAccess('Withdrawal-Request') === 'true'? true:false;
        });
        Gate::define('wallets-balance',function($user){
            return $user->hasAccess('E-&-S-Wallet-Bal') === 'true'? true:false;
        });
        Gate::define('clear-ewallet',function($user){
            return $user->hasAccess('Clear-Ewallet') === 'true'? true:false;
        });
        Gate::define('transactions',function($user){
            return $user->hasAccess('All-Transactions') === 'true'? true:false;
        });
        Gate::define('add-debit-funds',function($user){
            return $user->hasAccess('Add-Debit-Funds') === 'true'? true:false;
        });
        Gate::define('payout-delete',function($user){
            return $user->hasAccess('Delete-Payout') === 'true'? true:false;
        });
        Gate::define('per-report',function($user){
            return $user->hasAccess('Per-Payout-Report') === 'true'? true:false;
        });
        Gate::define('payout-report',function($user){
            return $user->hasAccess('Process-payout-Report') === 'true'? true:false;
        });
        Gate::define('payout-process',function($user){
            return $user->hasAccess('Process-payouts') === 'true'? true:false;
        });
        Gate::define('new-joinings',function($user){
            return $user->hasAccess('New-joinings') === 'true'? true:false;
        });
        Gate::define('epin-pending',function($user){
            return $user->hasAccess('Pending-Epins') === 'true'? true:false;
        });
        Gate::define('epin-transactions',function($user){
            return $user->hasAccess('E-pin-Transactions') === 'true'? true:false;
        });
        Gate::define('epin-status',function($user){
            return $user->hasAccess('E-pin-Status') === 'true'? true:false;
        });
        Gate::define('epin-sale',function($user){
            return $user->hasAccess('E-pin-Sale') === 'true'? true:false;
        });
        Gate::define('epin-index',function($user){
            return $user->hasAccess('E-pin-List') === 'true'? true:false;
        });
        Gate::define('epin-create',function($user){
            return $user->hasAccess('E-pin-Generate') === 'true'? true:false;
        });
        Gate::define('member-downline',function($user){
            return $user->hasAccess('My-Downline') === 'true'? true:false;
        });
        Gate::define('member-panel',function($user){
            return $user->hasAccess('Member-Panel') === 'true'? true:false;
        });
        Gate::define('top-up',function($user){
            return $user->hasAccess('Top-Up-ID') === 'true'? true:false;
        });
        Gate::define('edit-profile',function($user){
            return $user->hasAccess('Edit-Profile') === 'true'? true:false;
        });
        Gate::define('Payout-Summary',function($user){
            return $user->hasAccess('Payout-Summary') === 'true'? true:false;
        });
        Gate::define('Welcome-Report',function($user){
            return $user->hasAccess('Welcome-Report') === 'true'? true:false;
        });
        Gate::define('Reciept-Report',function($user){
            return $user->hasAccess('Reciept-Report') === 'true'? true:false;
        });
        Gate::define('Blocked-pay-Ach',function($user){
            return $user->hasAccess('Blocked-Pay-Ach') === 'true'? true:false;
        });
        Gate::define('Blocked-Rwd-Ach',function($user){
            return $user->hasAccess('Blocked-Rwd-Ach') === 'true'? true:false;
        });
        Gate::define('Direct-IDS',function($user){
            return $user->hasAccess('Direct-IDS') === 'true'? true:false;
        });
        Gate::define('Txn-Password',function($user){
            return $user->hasAccess('Txn-Password') === 'true'? true:false;
        });
        Gate::define('Member-Password',function($user){
            return $user->hasAccess('Member-Password') === 'true'? true:false;
        });
        Gate::define('Tree-View',function($user){
            return $user->hasAccess('Tree-View') === 'true'? true:false;
        });
        Gate::define('Member-View',function($user){
            return $user->hasAccess('Member-View') === 'true'? true:false;
        });
        Gate::define('Commission-Settings',function($user){
            return $user->hasAccess('Commission-Settings') === 'true'? true:false;
        });
        Gate::define('Pin-Commission-Settings',function($user){
            return $user->hasAccess('Pin-Commission-Settings') === 'true'? true:false;
        });
        Gate::define('Pair-Commission-Settings',function($user){
            return $user->hasAccess('Pair-Commission-Settings') === 'true'? true:false;
        });
        Gate::define('Deleted-Packages',function($user){
            return $user->hasAccess('Deleted-Packages') === 'true'? true:false;
        });
        Gate::define('Packages',function($user){
            return $user->hasAccess('Packages') === 'true'? true:false;
        });
        Gate::define('Add-Package',function($user){
            return $user->hasAccess('Add-Package') === 'true'? true:false;
        });

        Gate::define('banker-master',function($user){
            return $user->hasAccess('Banker-Master') === 'true'? true:false;
        });

        Gate::define('add-news',function($user){
            return $user->hasAccess('Add-News') === 'true'? true:false;
        });

        Gate::define('add-video',function($user){
            return $user->hasAccess('Add-Video') === 'true'? true:false;
        });

        Gate::define('add-event',function($user){
            return $user->hasAccess('Add-Event') === 'true'? true:false;
        });

        Gate::define('TDS-Report',function($user){
            return $user->hasAccess('TDS-Report') === 'true'? true:false;
        });
        Gate::define('add-photo',function($user){
            return $user->hasAccess('Add-Photo') === 'true'? true:false;
        });

        Gate::define('add-categories',function($user){
            return $user->hasAccess('Add-Categories') === 'true'? true:false;
        });

        Gate::define('sub-categories',function($user){
            return $user->hasAccess('Sub-Categories') === 'true'? true:false;
        });

        Gate::define('Eshop-products',function($user){
            return $user->hasAccess('Eshop-Products') === 'true'? true:false;
        });

        Gate::define('Print-Address-Labels',function($user){
            return $user->hasAccess('Print-Address-Labels') === 'true'? true:false;
        });

        Gate::define('repurchase-stock',function($user){
            return $user->hasAccess('Add-Repurchase-Stock') === 'true'? true:false;
        });
        
        Gate::define('repurchase-report',function($user){
            return $user->hasAccess('Repurchase-Report') === 'true'? true:false;
        });

        Gate::define('new-order',function($user){
            return $user->hasAccess('New-Order') === 'true'? true:false;
        });

        Gate::define('deleted-products',function($user){
            return $user->hasAccess('Deleted-Products') === 'true'? true:false;
        });

        Gate::define('completed-orders',function($user){
            return $user->hasAccess('Completed-Orders') === 'true'? true:false;
        });

        Gate::define('state-wise-id',function($user){
            return $user->hasAccess('State-Wise-Id') === 'true'? true:false;
        });

        Gate::define('joining-comm',function($user){
            return $user->hasAccess('Joining-Comm') === 'true'? true:false;
        });

        Gate::define('approve-kyc',function($user){
            return $user->hasAccess('Approve-KYC') === 'true'? true:false;
        });

        Gate::define('visitors-data',function($user){
            return $user->hasAccess('Visitors-Data') === 'true'? true:false;
        });

        Gate::define('plan-details',function($user){
            return $user->hasAccess('Plan-Details') === 'true'? true:false;
        });
        
        Gate::define('complaints',function($user){
            return $user->hasAccess('View-Grievance') === 'true'? true:false;
        });
                
        Gate::define('delivery-report',function($user){
            return $user->hasAccess('Delivery-Report') === 'true'? true:false;
        });
                        
        Gate::define('sms-history',function($user){
            return $user->hasAccess('SMS-History') === 'true'? true:false;
        });
                              
        Gate::define('repurchase-comm',function($user){
            return $user->hasAccess('Repurchase-Comm') === 'true'? true:false;
        });

        Gate::define('bill-report',function($user){
            return $user->hasAccess('Bill-Report') === 'true'? true:false;
        });

        Gate::define('payout-dist',function($user){
            return $user->hasAccess('Payout-Dist-Chart') === 'true'? true:false;
        });
    }
}

