
[![GitHub issues](https://img.shields.io/github/issues/tohidplus/zarrinpal.svg)](https://github.com/tohidplus/zarrinpal/issues)
[![Total Downloads](https://img.shields.io/packagist/dt/tohidplus/zarrinpal.svg)](https://packagist.org/packages/tohidplus/zarrinpal)
[![GitHub stars](https://img.shields.io/github/stars/tohidplus/zarrinpal.svg)](https://github.com/tohidplus/zarrinpal/stargazers)

# Zarrinpal package for Laravel
This package is built for connecting Iranian websites to Zarrinpal gateway.

---

### Installation
1. Run the command below
```bash
composer require tohidplus/zarrinpal
```

2. Add the following code to end of the providers array in **config/app.php** file.
```php
'providers'=>[
    Tohidplus\Zarrinpal\ZarrinpalServiceProvider::class,
];
```

3. Add the following code to end of the aliases array in **config/app.php** file.
```php
'aliases' => [
   'Zarrinpal'=>Tohidplus\Zarrinpal\Facades\Zarrinpal::class,
];
```

4. Run the command below
```bash
php artisan vendor:publish --provider=Tohidplus\Zarrinpal\ZarrinpalServiceProvider
```

5. Migrate the database
```bash
php artisan migrate
```

6. Now you can see a new config file named **zarrinpal.php** is added to config directory. So open the file...
```php
<?php
return [
    'merchantId'=>'XXXXX XXXXX XXXXX',
    'callBackUrl'=>'http://yourwebsite.com/verifyPayment',
    'description'=>'Some text here',
];
```
Add the **merchantId , description and** **the** **callBackUrl** which you want to redirect the user from bank after transaction is finished.

> Notice: you can leave callbackUrl and description here blanked and define it when you call **setData** method dynamically as we will explain  in the next part.

---

### Methods
**setData** method

*Before redirecting user to the gateway you have to initialize the fields using this method otherwise you will get an exception.*

Parameters:
- **amount** (Required)
- **email** (Optional)
- **mobile** (Optional)
- **description** ( *If you haven't defined it in config file you must set it here* )
- **callBackUrl** ( *If you haven't defined it in config file you must set it here* )

**redirect** method

*after initializing the fields you can redirect user to the bank using this method.*

Parameters

- It only accepts one parameter as a callback funtion and if there is an error while redirection the callback function will be triggered with status code as parameter.

**verify** method

*This method indicates if transaction is successful or not.*


Parameters
- **request** is the request which you get from bank and you have to pass it to **verify method**.
- **success**  is a callback function which will be triggered if transaction is successful and accepts refId as parameter.
- **error** is a callback function which will be triggered if transaction is unsuccessful.

---
### Full example
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tohidplus\Zarrinpal\Facades\Zarrinpal;

class PaymentController extends Controller
{
    public function redirectUserToBank()
    {

        Zarrinpal::setData(100,'someone@example.com','09XXXXXXXXX','Some descripion','another/callback/url');

        return Zarrinpal::redirect(function($status){
            // Do something if there was a problem while redirection
        });
    }

    public function verifyPayment(Request $request)
    {
        return Zarrinpal::verify($request,
        function ($refId){
            // The transaction is successfull    
        },function ($message,$status=null){
            // The trasnsaction is unsuccessful
            // if message was canceled it means user has canceled transaction them self
            // if message was unsuccessful it means an error has occurred 
        });
    }
}

```

---

### Transaction Logs
Simply all events are saved in **zarrinpal_logs** table which is associated with **Tohidplus\Zarrinpal\Models\ZarrinpalLog** model
```php
<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Tohidplus\Zarrinpal\Models\ZarrinpalLog;

class ZarrinpalLogController extends Controller
{
    public function index()
    {
        $successfulTransactions = ZarrinpalLog::successful()->get();
        $unsuccessfulTransactions = ZarrinpalLog::unsuccessful()->get();
        $successfulTransactions = ZarrinpalLog::canceled()->get();
        $pendingTransactions = ZarrinpalLog::pending()->get();
    }
}

```
