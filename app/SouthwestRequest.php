<?php
namespace App;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Crypt;

class SouthwestRequest
{

    protected $client;

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

    public function login(SouthwestAccount $account)
    {
        if (!$account->password || !$account->username)
            throw new \Exception("No login credentials provided");

        $res = $this->client->post('https://api-customer.southwest.com/v1/accounts/login', [
            'headers' => $this->getDefaultHeaders(),
            'json' => [
                'accountNumberOrUserName' => $account->username,
                'password' => Crypt::decrypt($account->password)
            ]
        ]);
        $res = json_decode($res->getBody());

        $account->access_token = $res->accessToken;
        $account->access_token_expires = Carbon::parse($res->accessTokenDetails->hotExpirationDateTimeUtc);
        $account->account_num = $res->accessTokenDetails->accountNumber;
        $account->save();
    }

    public function getReservations(SouthwestAccount $account)
    {
        if (!$account->access_token || !$account->account_num || $account->access_token_expires->lt(Carbon::now()))
            $this->login($account);

        $res = $this->client->get("https://api-extensions.southwest.com/v1/mobile/account-number/" . $account->account_num . "/upcoming-trips", [
            'headers' => $this->getDefaultHeaders() + ['token' => $account->access_token]
        ]);

        $res = json_decode($res->getBody());
        return $res;

    }

    /**
     * @param $confirmation
     * @param $first_name
     * @param $last_name
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
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