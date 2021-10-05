<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\tbl_rings_feed;
use App\components\RingApi;

class FetchStoreRings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:fetch_and_store_rings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Rings and store in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        
        // Mail::send([], [], function ($message) {
        //   $message->to('kuldeepsinghnetweb@gmail.com')
        //     ->subject('Rings Fetch Api Run')
        //     ->setBody('Hi, New Cron Job Run For Rings Automatic fetch run');
        // });
 

         
        //echo "<pre>";
        $rings = [];
        $RingApiObj = new RingApi();
        $ringApiResults = $RingApiObj->GetRingList();
        if(isset($ringApiResults->updated) &&  $ringApiResults->updated && sizeof($ringApiResults->updated)){
            foreach($ringApiResults->updated as $ringApiID){
                $find = tbl_rings_feed::where('ring_id', $ringApiID)->first();
                if(!$find){
                  $fetch_single = $RingApiObj->GetSingleRing($ringApiID);
                  if($fetch_single->ring){
                      $ring_obj = $fetch_single->ring;
                      $save_log = new tbl_rings_feed();
                      $save_log->ring_id        = $ring_obj->id ?? '';
                      $save_log->called_number  = $ring_obj->called_number ?? '';
                      $save_log->callerid       = $ring_obj->callerid ?? '';
                      $save_log->start_time     = $ring_obj->start_time ?? '';
                      $save_log->start_date_tz  = $ring_obj->start_date_tz ?? '';
                      $save_log->duration       = $ring_obj->duration ?? '';
                      $save_log->duration_in_seconds = $ring_obj->duration_in_seconds ?? '';
                      $save_log->flagTag             = $ring_obj->flagTag ?? '';
                      $save_log->outcome             = $ring_obj->outcome ?? '';
                      $save_log->checked             = $ring_obj->checked ?? '';
                      $save_log->private_call        = $ring_obj->private_call ?? '';
                      $save_log->ring_local          = $ring_obj->ring_local ?? '';
                      $save_log->ring_local_number   = $ring_obj->ring_local_number ?? '';
                      $save_log->voicemail_drop      = $ring_obj->voicemail_drop ?? '';
                      $save_log->disposition_label   = $ring_obj->disposition_label ?? '';
                      $save_log->contact_number      = $ring_obj->contact_number ?? '';
                      $save_log->user_number         = $ring_obj->user_number ?? '';
                      $save_log->remote_id           = $ring_obj->remote->id ?? '';
                      $save_log->remote_type         = $ring_obj->remote->type ?? '';
                      $save_log->remote_url          = $ring_obj->remote->url ?? '';
                      $save_log->time_to_selection  = $ring_obj->time_to_selection ?? '';
                      $save_log->time_in_queue      = $ring_obj->time_in_queue ?? '';
                      $save_log->time_abandoned     = $ring_obj->time_abandoned ?? '';
                      $save_log->time_queue_exit    = $ring_obj->time_queue_exit ?? '';
                      $save_log->time_talk          = $ring_obj->time_talk ?? '';
                      $save_log->queue_exit         = $ring_obj->queue_exit ?? '';
                      $save_log->business_hours     = $ring_obj->business_hours ?? '';
                      $save_log->external_transfer  = $ring_obj->external_transfer ?? '';
                      $save_log->abandoned      = $ring_obj->abandoned ?? '';
                      $save_log->number_label   = $ring_obj->number_label ?? '';
                      $save_log->number_dialed  = $ring_obj->number_dialed ?? '';
                      $save_log->kind           = $ring_obj->kind ?? '';
                      $save_log->history        = $ring_obj->history ?? '';
                      $save_log->long_history   = $ring_obj->long_history ?? '';
                      $save_log->direction      = $ring_obj->direction ?? '';
                      $save_log->from_id        = $ring_obj->from_id ?? '';
                      $save_log->from_type      = $ring_obj->from_type ?? '';
                      $save_log->from_name      = $ring_obj->from_name ?? '';
                      $save_log->from_hidden    = $ring_obj->from_hidden ?? '';
                      $save_log->to_id          = $ring_obj->to_id ?? '';
                      $save_log->to_type        = $ring_obj->to_type ?? '';
                      $save_log->to_name        = $ring_obj->to_name ?? '';
                      $save_log->to_company     = $ring_obj->to_company ?? '';
                      $save_log->to_hidden      = $ring_obj->to_hidden ?? '';
                      $save_log->call_created   = substr($ring_obj->start_time, 0, 10);
                      $save_log->save();

                  }  
              }
            }
        }else{
          echo "response not found";
        }
    }
}
