<?php
namespace App\Http\Controllers\Pay;

use App\Exceptions\RuleValidationException;
use App\Http\Controllers\PayController;
use Illuminate\Http\Request;

class MgateController extends PayController
{

    public function gateway(string $payway, string $orderSN)
    {
        try {
            // 加载网关
            $this->loadGateWay($orderSN, $payway);
            //组装支付参数
            $params = [
                'app_id' =>  $this->payGateway->merchant_id,
                'out_trade_no' => $this->order->order_sn,
                'return_url' => url('detail-order-sn', ['orderSN' => $this->order->order_sn]),
                'notify_url' => url($this->payGateway->pay_handleroute . '/notify_url'),
                'total_amount'  => $this->order->actual_price * 100
            ];
            ksort($params);
            $str = http_build_query($params) . $this->payGateway->merchant_pem;
            $params['sign'] = md5($str);
            $result = $this->request($this->payGateway->merchant_key . '/v1/gateway/fetch', $params);
            $result = json_decode($result);
            if (!$result) {
                throw new RuleValidationException('网络异常');
            }
            if (isset($result->errors)) {
                $errors = (array)$result->errors;
                throw new RuleValidationException($errors[array_keys($errors)[0]][0]);
            }
            if (isset($result->message)) {
                throw new RuleValidationException($result->message);
            }

            return redirect($result->data->pay_url);
        } catch (RuleValidationException $exception) {
            return $this->err($exception->getMessage());
        }
    }

    public function notifyUrl(Request $request)
    {
        $params = $request->all();
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        reset($params);
        $order = $this->orderService->detailOrderSN($params['out_trade_no']);
        if (!$order) {
            return 'fail';
        }
        $payGateway = $this->payService->detail($order->pay_id);
        $str = http_build_query($params) . $payGateway->merchant_pem;
        if ($sign !== md5($str)) {
            return 'fail';
        }
        $this->orderProcessService->completedOrder($params['out_trade_no'], $params['total_amount'] / 100, $params['trade_no']);
        return 'success';
    }

    public function request($url, $data)
    {
        $headers = array('content-type: application/x-www-form-urlencoded', 'user-agent: dujiaoka-mgate');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }


    public function returnUrl(Request $request)
    {
        $oid = $request->input('out_trade_no');
        sleep(1);
        return redirect(url('detail-order-sn', ['orderSN' => $oid]));
    }

}