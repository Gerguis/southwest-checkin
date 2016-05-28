<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\SouthwestAccount;
use App\SouthwestRequest;
use App\User;
use Illuminate\Mail\Mailer;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class CheckInJob extends Job
{
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SouthwestRequest $southwest)
    {
        foreach (SouthwestAccount::all() as $account) {

            $checkedIn = [];
            $reservations = $southwest->getReservations($account);

            sleep(rand(2, 5));
            foreach ($reservations->trips as $trip) {
                foreach ($trip->flights as $flight) {
                    foreach ($flight->passengers as $passenger) {

                        if ($passenger->checkinEligibilities) {
                            foreach ($passenger->checkinEligibilities as $eligibility) {

                                //Available to check-in and hasn't already done so
                                if (
                                    $eligibility->checkinDocumentReason == SouthwestRequest::AVAILABLE_TO_CHECK_IN &&
                                    !$eligibility->boardingGroup
                                ) {

                                    //Do check in
                                    $southwest->checkIn($flight->recordLocator, $passenger->firstName, $passenger->lastName);

                                    
                                    //Notify of check in
                                    Mail::raw("Checked in passenger $passenger->firstName $passenger->lastName under confirmation #$flight->recordLocator", function ($message) use ($account) {
                                        $message->to($account->user->email)->subject("Southwest check-in complete");
                                    });
                                    break;
                                }
                            }
                        }
                    }
                }
                sleep(rand(10, 20));
            }

            if (count($checkedIn) == 0) {

            }

        }
    }
}
