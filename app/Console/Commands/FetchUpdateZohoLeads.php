<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\tbl_zoho_leads;
use App\components\ZohoApi;

class FetchUpdateZohoLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:fetch_and_update_zoho_leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Zoho Leads and update in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public $leads_data        = [];
    public $leads_data_count  = 0;
    public $leads_data_offset = 0;
    public $zoho_access_token = '';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        date_default_timezone_set('America/Los_Angeles');
        $this->zoho_access_token   = ZohoApi::getAuthToken();
        $this->ZohoUpdateLeadsApi();
        $msg = "Cron For Automatic leads update run";

      $msg = wordwrap($msg,70);

      mail("kuldeepsinghnetweb@gmail.com","Cron leads update Test",$msg);
        if(is_array($this->leads_data) && sizeof($this->leads_data)){
          foreach ($this->leads_data as $key => $lead) {
            $update_lead  = tbl_zoho_leads::where('lead_id', $lead->id)->first();
            if($update_lead){
              $update_lead->Owner_first_name      = $lead->{'Owner.first_name'};
              $update_lead->Owner_last_name       = $lead->{'Owner.last_name'};
              $update_lead->First_Name            = $lead->First_Name;
              $update_lead->Full_Name             = $lead->Full_Name; 
              $update_lead->Owner_id              = $lead->Owner->id; 
              $update_lead->Modified_Time         = $lead->Modified_Time; 
              $update_lead->Initial_Payment       = $lead->Initial_Payment; 
              $update_lead->Last_Name             = $lead->Last_Name; 
              $update_lead->Created_Time          = $lead->Created_Time; 
              $update_lead->Initial_Payment_Date  = $lead->Initial_Payment_Date; 
              $update_lead->save();
            }        
          }
        }


        ////////////////code start for update contactdetials in converted leads
        $post_params = array (
            'per_page' => '200',
            'converted' => 'true'
        );
        $leadsApiResult2 = $this->RunGetCurl('all','leads',$post_params);
        if($leadsApiResult2){
            $leadsApiResult2 = $leadsApiResult2->data;
            if(is_array($leadsApiResult2) && sizeof($leadsApiResult2)){
              foreach ($leadsApiResult2 as $key2 => $lead2) {
                $new_lead                         = tbl_zoho_leads::where('lead_id', $lead2->id)->first();
                if(!$new_lead){
                    $new_lead                     = new tbl_zoho_leads();
                }

                $new_lead->lead_id                = $lead2->id;
                $new_lead->converted_contact_id   = $lead2->{'$converted_detail'}->contact;
                $new_lead->convert_date           = $lead2->{'$converted_detail'}->convert_date;
                if($lead2->{'$converted_detail'}->convert_date){
                  $new_lead->convert_date2        = substr($lead2->{'$converted_detail'}->convert_date, 0, 10);
                }
                $new_lead->converted_by           = $lead2->{'$converted_detail'}->converted_by;


                $new_lead->Owner_full_name        = $lead2->{'Owner'}->name;
                $new_lead->First_Name             = $lead2->First_Name;
                $new_lead->Full_Name              = $lead2->Full_Name;
                $new_lead->Owner_id               = $lead2->Owner->id; 
                $new_lead->Modified_Time          = $lead2->Modified_Time;
                if($lead2->Modified_Time){
                  $new_lead->Modified_Time2       = substr($lead2->Modified_Time, 0, 10);
                }
                $new_lead->Initial_Payment        = $lead2->Initial_Payment;
                $new_lead->Last_Name              = $lead2->Last_Name;
                
                if($lead2->Created_Time){
                  $new_lead->Created_Time         = $lead2->Created_Time;
                  $new_lead->Created_Time2        = substr($lead2->Created_Time, 0, 10);
                }

                if($lead2->Initial_Payment_Date){
                  $new_lead->Initial_Payment_Date = substr($lead2->Initial_Payment_Date, 0, 10);
                }
                $new_lead->save();
            }
          }
        }
    }

    public function RunGetCurl($scope = "all", $module = "Leads", $post_params=null){
        $wfTrigger = array("abc");
        $post              = array();
        // $post['data']      = $data;
        // $post['trigger']   = $wfTrigger;
        $post["scope"]     = "ZohoCRM.modules.$module.$scope";
        $zoho_url          = "https://www.zohoapis.com/crm/v2/$module";
        
        if($post_params==null){
          $post_params = array (
              'per_page' => '10'
             
          );      
        }

            
        $params = '';
        foreach($post_params as $key=>$value){
            $params .= $key.'='.$value.'&';
        }
        $params = trim($params, '&');
        
        
        
          /* set url to send post request */
        $headers = array();
        $last_1_hours      =  date('c', strtotime('-2 hours'));
        array_push($headers,'Authorization: Bearer ' . $this->zoho_access_token);
        array_push($headers,'If-Modified-Since:'.$last_1_hours);
          
        $cURLConnection = curl_init();
        echo $zoho_url.'?'.$params;
        curl_setopt($cURLConnection, CURLOPT_URL, $zoho_url.'?'.$params);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, $headers);
        $leadList = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        
        $jsonArrayResponse = json_decode($leadList);
        
        // var_dump( $jsonArrayResponse);
        // exit;   
        if($jsonArrayResponse){ 
          return $jsonArrayResponse;
        }
        return NULL;
    }

    public function ZohoUpdateLeadsApi($offset=NULL){
      $post              =  [];
      $post["scope"]     =  "zohocrm.coql.read,zohocrm.modules.all";
      $zoho_url          =  "https://www.zohoapis.com/crm/v2/coql";
      $per_page          =  200;
      $post_params       =  [
                              'per_page' => $per_page,
                            ];
      $last_2_hours      =  date('c', strtotime('-2 hours'));
      $end_time          =  date('c', time());
      if($offset==NULL || $offset==0){
        $limit_query = 'LIMIT 200';
      }else{
        $limit_query = "LIMIT $offset,200";
      }

      // if($start_date==$end_date){
      //   $end_date          =  date('c', strtotime($end_date.' noon'));
      // }
      $post['select_query'] = "select Initial_Payment, Initial_Payment_Date, Owner, Owner.first_name, Owner.last_name, Last_Name, First_Name, Full_Name, Created_Time,Modified_Time from Leads where Modified_Time between '$last_2_hours' and '$end_time' $limit_query";
      // echo $post['select_query'];    
      $params = '';
      foreach($post_params as $key=>$value){
          $params .= $key.'='.$value.'&';
      }
      $params = trim($params, '&');
      
      /* set url to send post request */
      $headers = array();
        
      
      $ch = curl_init();
      
      /* set url to send post request */
      curl_setopt($ch, CURLOPT_URL, $zoho_url);
      $headers = array();

      // array_push($headers,'Authorization: Bearer ' . $auth_token);
      array_push($headers,'Authorization: Zoho-oauthtoken ' . $this->zoho_access_token);


      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      /* allow redirects */
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      /* return a response into a variable */
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      /* times out after 30s */
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      /* set POST method */
      curl_setopt($ch, CURLOPT_POST, 1);
      /* add POST fields parameters */
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));               
      // Set the request as a POST FIELD for curl.

      //Execute cUrl session
      $response = curl_exec($ch);
      curl_close($ch);  
      
      $jsonArrayResponse = json_decode($response);
      // var_dump($jsonArrayResponse);
      // exit;
      $this->leads_data_offset += $per_page; 
      if($jsonArrayResponse){
        // echo "<pre>";
        if(isset($jsonArrayResponse->info)){
          $this->leads_data_count += $jsonArrayResponse->info->count;
          foreach ($jsonArrayResponse->data as $lead_) {
            array_push($this->leads_data, $lead_);
          }
          if($jsonArrayResponse->info->more_records == 1) {
            $this->ZohoUpdateLeadsApi($this->leads_data_offset);
          }
        }
      }
      return NULL;
  }
}
