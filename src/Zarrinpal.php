<?php
/**
 * Created by PhpStorm.
 * User: TD-PLUS
 * Date: 10/13/2018
 * Time: 10:06 PM
 */

namespace Tohidplus\Zarrinpal;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use SoapClient;
use Tohidplus\Zarrinpal\Models\ZarrinpalLog;

class Zarrinpal
{
    /**
     * @var SoapClient
     */
    protected $client;
    protected $amount;
    protected $description;
    protected $email;
    protected $mobile;
    protected $callBackUrl;


    public function __construct(SoapClient $client)
    {
        $this->client = $client;
    }

    public function redirect($error)
    {
        $this->validate();

        $result=$this->client->PaymentRequest($this->getRedirectData());

        if($result->Status==100){
            $this->log($result);
            return Redirect::to('https://zarinpal.com/pg/StartPay/'.$result->Authority);
        }else{
            return $error($result->Status);
        }
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description?:config('zarrinpal.description');
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * @return mixed
     */
    public function getCallBackUrl()
    {
        return $this->callBackUrl ?: config('zarrinpal.callBackUrl');
    }


    /**
     * @param $amount
     * @param string $email
     * @param string $mobile
     * @param string $description
     * @param null $callBackUrl
     */
    public function setData($amount, $email='', $mobile='',$description='', $callBackUrl=null)
    {
        $this->amount=$amount;
        $this->email=$email;
        $this->mobile=$mobile;
        $this->description=$description;
        $this->callBackUrl=$callBackUrl;
    }

    /**
     * @return bool
     */
    public function isConvertToRial(): bool
    {
        return $this->convertToRial;
    }

    /**
     * @param Request $request
     * @param $success
     * @param $error
     * @return mixed
     */
    public function verify(Request $request, $success, $error)
    {
        $authority=$request->get('Authority');
        $log=ZarrinpalLog::where('authority',$authority)->latest()->first();
        if($request->get('Status')=='OK'){

            $amount=$log->price;
            $result=$this->client->PaymentVerification([
                'MerchantID' => config('zarrinpal.merchantId'),
                'Authority' => $authority,
                'Amount' => $amount,
            ]);
            if($result->Status==100){
                $log->update([
                    'status'=>'successful',
                    'status_code'=>100,
                    'ref_id'=>$result->RefID
                ]);
                return $success($result->RefID);
            }else{
                $log->update([
                    'status'=>'unsuccessful',
                    'status_code'=>$result->Status
                ]);
               return $error('unsuccessful',$result->Status);
            }
        }else{
            $log->update([
                'status'=>'canceled'
            ]);
            return $error('canceled');
        }
    }



    /**
     * @return array
     */
    protected function getRedirectData(): array
    {
        return [
            'MerchantID' => config('zarrinpal.merchantId'),
            'Amount' => $this->getAmount(),
            'Description' => $this->getDescription(),
            'Email' => $this->getEmail(),
            'Mobile' => $this->getMobile(),
            'CallbackURL' => $this->getCallBackUrl()
        ];
    }

    /**
     * @throws ZarrinpalException
     */
    protected function validate()
    {
        if(!$this->getAmount() || !$this->getCallBackUrl() || !$this->getDescription()){
            throw new ZarrinpalException('Essential fields are null',500);
        }
        if(!config('zarrinpal.merchantId')){
            throw new ZarrinpalException('MerchantId cannot be null',500);
        }
    }

    /**
     * @param $result
     */
    protected function log($result)
    {
        ZarrinpalLog::create([
            'authority'=>$result->Authority,
            'price'=>$this->getAmount()
        ]);
    }

}
