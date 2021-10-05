<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\tbl_zoho_contacts;
use App\components\ZohoApi;

class FetchStoreZohoContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:fetch_and_store_zoho_contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Zoho Contacts and store in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public $contacts_data = [];
    public $contacts_data_count = 0;
    public $contacts_data_offset = 0;

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
      // the message
    $msg = "Cron For Automatic contacts run";

    $msg = wordwrap($msg,70);

    mail("kuldeepsinghnetweb@gmail.com","Cron contacts Test",$msg);
      $this->zoho_access_token   = ZohoApi::getAuthToken();
      $post_params = array (
        'per_page' => '200',
      );
      $this->ZohoStoreContacts();
      if(is_array($this->contacts_data) && sizeof($this->contacts_data)){
        foreach ($this->contacts_data as $key => $contact) {
          $find_contact  = tbl_zoho_contacts::where('contact_id', $contact->id)->first();
          if(!$find_contact){
              $new_contact                         = new tbl_zoho_contacts();
              $new_contact->contact_id             = $contact->id;
              $new_contact->Owner_first_name       = $contact->{'Owner.first_name'};
              $new_contact->Owner_last_name        = $contact->{'Owner.last_name'};
              $new_contact->First_Name             = $contact->First_Name;
              $new_contact->Full_Name              = $contact->Full_Name; 
              $new_contact->Owner_id               = $contact->Owner->id; 
              $new_contact->Modified_Time          = $contact->Modified_Time;
              if($contact->Modified_Time){
                $new_contact->Modified_Time2       = substr($contact->Modified_Time, 0, 10);
              } 

              $new_contact->Last_Name              = $contact->Last_Name;
              if($contact->Created_Time){
                $new_contact->Created_Time         = $contact->Created_Time;
                $new_contact->Created_Time2        = substr($contact->Created_Time, 0, 10);
              }

              $new_contact->Docs_LP_Sale_Date      = $contact->Docs_LP_Sale_Date;
              
              if($contact->Docs_LP_Sale_Date){
                $new_contact->Docs_LP_Sale_Date     = substr($contact->Docs_LP_Sale_Date, 0, 10);
              } 
              $new_contact->save();
          }        
        }
      }
    }
    
    public function ZohoStoreContacts($offset=NULL){
    $post              =  array();
    $post["scope"]     =  "zohocrm.coql.read,zohocrm.modules.all";
    $zoho_url          =  "https://www.zohoapis.com/crm/v2/coql";
    $per_page          =  200;
    $post_params       = array (
                          'per_page' => $per_page,
                        );
    $last_2_hours      =  date('c', strtotime('-2 hours'));
    $end_time          =  date('c', time());

    if($offset==NULL || $offset==0){
      $limit_query = 'LIMIT 200';
    }else{
      $limit_query = "LIMIT $offset,200";
    }
    $post['select_query'] = "select Full_Name, First_Name, Last_Name, Modified_Time, Docs_LP_Sale_Date, Owner, Owner.first_name, Owner.last_name, Created_Time from Contacts where Created_Time between '$last_2_hours' and '$end_time' $limit_query";
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
    
    $this->contacts_data_offset += $per_page; 
    if($jsonArrayResponse){
      $this->contacts_data_count += $jsonArrayResponse->info->count;
      foreach ($jsonArrayResponse->data as $contacts__) {
        array_push($this->contacts_data, $contacts__);
      }

      if($jsonArrayResponse->info){
        if($jsonArrayResponse->info->more_records == 1) {
          $this->ZohoStoreContacts($this->contacts_data_offset);
        }        
      }
    }
    return NULL;
  }
}
