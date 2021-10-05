<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\tbl_zoho_leads;
use App\components\ZohoApi;

class FetchStoreZohoLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:fetch_and_store_zoho_leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Zoho Leads and store in database';

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

        // Mail::send([], [], function ($message) {
        //   $message->to('kuldeepsinghnetweb@gmail.com')
        //     ->subject('Rings Fetch Api Run')
        //     ->setBody('Hi, New Cron Job Run For Rings Automatic fetch run');
        // });
        $msg = "Cron For Automatic leads run";

    $msg = wordwrap($msg,70);

    mail("kuldeepsinghnetweb@gmail.com","Cron contacts Test",$msg);
      $this->zoho_access_token   = ZohoApi::getAuthToken();
      $this->ZohoNewLeadsApi();
      if(is_array($this->leads_data) && sizeof($this->leads_data)){
        foreach ($this->leads_data as $key => $lead) {
          $find_lead  = tbl_zoho_leads::where('lead_id', $lead->id)->first();
          if(!$find_lead){
              $new_lead                         = new tbl_zoho_leads();
              $new_lead->lead_id                = $lead->id;
              $new_lead->Owner_first_name       = $lead->{'Owner.first_name'};
              $new_lead->Owner_last_name        = $lead->{'Owner.last_name'};
              $new_lead->First_Name             = $lead->First_Name;
              $new_lead->Full_Name              = $lead->Full_Name; 
              $new_lead->Owner_id               = $lead->Owner->id; 
              $new_lead->Modified_Time          = $lead->Modified_Time;
              if($lead->Modified_Time){
                $new_lead->Modified_Time2       = substr($lead->Modified_Time, 0, 10);
              } 
              $new_lead->Initial_Payment        = $lead->Initial_Payment;

              $new_lead->Last_Name              = $lead->Last_Name;
              if($lead->Created_Time){
                $new_lead->Created_Time         = $lead->Created_Time;
                $new_lead->Created_Time2        = substr($lead->Created_Time, 0, 10);
              }
              
              if($lead->Initial_Payment_Date){
                $new_lead->Initial_Payment_Date = substr($lead->Initial_Payment_Date, 0, 10);
              } 
              $new_lead->save();
          }        
        }
      }

    }
    public function ZohoNewLeadsApi($offset=NULL){
      
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
      $post['select_query'] = "select Initial_Payment, Initial_Payment_Date, Owner, Owner.first_name, Owner.last_name, Last_Name, First_Name, Full_Name, Created_Time,Modified_Time from Leads where Created_Time between '$last_2_hours' and '$end_time' $limit_query";
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
            $this->ZohoNewLeadsApi($this->leads_data_offset);
          }
        }
      }
      return NULL;
  }
}
