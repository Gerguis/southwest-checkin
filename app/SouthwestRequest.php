<?php
namespace App;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;

class SouthwestRequest
{

    protected $accessToken;

    const AVAILABLE_TO_CHECK_IN = "withinCheckinTimeWindow";
    const PAST_DEPARTURE = "passDepartureTime";

    public function __construct()
    {
        $this->client = new Client();
    }

    protected function getDefaultHeaders()
    {
        return
            [
                'User-Agent' => 'Southwest/3.5.13 (iPhone; iOS 9.3.2; Scale/2.00)',
                'Content-Type' => " application/vnd.swacorp.com.accounts.login-v1.0+json",
                "X-API-KEY" => "l7xx8d8bfce4ee874269bedc02832674129b",
                'Accept-Language' => 'en-US;q=1'
            ];
    }

    public function login(User $user)
    {
        if (!$user->sw_password || !$user->sw_username)
            throw new \Exception("No login credentials provided");

        $res = $this->client->post('https://api-customer.southwest.com/v1/accounts/login', [
            'headers' => $this->getDefaultHeaders(),
            'json' => [
                'accountNumberOrUserName' => $user->sw_username,
                'password' => Crypt::decrypt($user->sw_password)
            ]
        ]);
        $res = json_decode($res->getBody());

        $user->sw_access_token = $res->accessToken;
        $user->sw_access_token_expires = Carbon::parse($res->accessTokenDetails->hotExpirationDateTimeUtc);
        $user->sw_account = $res->accessTokenDetails->accountNumber;
        $user->save();
    }

    public function getReservations(User $user)
    {
        if (!$user->sw_access_token || !$user->sw_account || $user->sw_access_token_expires->lt(Carbon::now()))
            $this->login($user);

        $res = $this->client->get("https://api-extensions.southwest.com/v1/mobile/account-number/" . $user->sw_account . "/upcoming-trips", [
            'headers' => $this->getDefaultHeaders() + ['token' => $user->sw_access_token]
        ]);

        $res = json_decode($res->getBody());
        return $res;

    }

    public function checkIn($confirmation, $first_name, $last_name)
    {
        $res = $this->client->get("https://api-extensions.southwest.com/v1/mobile/reservations/record-locator/" . $confirmation, [
            'headers' => $this->getDefaultHeaders(),
            'query' => [
                'first' => $first_name,
                'last' => $last_name
            ]
        ]);
        $res = json_decode($res->getBody());
        return $res;
    }
}