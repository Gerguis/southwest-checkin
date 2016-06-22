# Southwest Automatic Checkin

Built on Laravel PHP framework

##Installation
Clone the repository and then run the following commands
```
composer install
php artisan migrate
```

Then, you'll need to setup the laravel scheduler feature via a cron job on the server.

Add the following cron entry to your server
```* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1```


Finally, navigate to the `/register` endpoint and sign up for an account.
From there, you can add southwest accounts (via username/password) to watch for flights available for check-in.

