<?php

/*
CCApiHelper
Developed by Floory
Библиотека для работы с CatCoin API
https://github.com/Floory/ccapihelper
*/
class CCApiHelper {

	protected const API_HREF = 'https://catcoin.ru/api/';
	private $apikey = "";
	private $merchantid = 0;

	public function __construct($merchantid, $apikey) {
		if(version_compare('7.0.0', phpversion()) === 1) {
			exit("Ваша версия php ниже 7.0");
		}

		$this->merchantid = $merchantid;
		$this->apikey = $apikey;
		print("YES");
	}

	public function isValidHook($p) {
		if(isset($p['id'], $p['amount'], $p['payload'], $p['created_at'], $p['from_id'], $p['to_id'], $p['key'])) {
			$key = md5($p['id'].$p['from_id'].$p['amount'].$p['payload'].$this->apikey);
			return $key === $p['key'];
		}
		return false;
	}

	private function request($prms) {
		if(extension_loaded('curl')) {
			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL => self::API_HREF,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $prms,
				CURLOPT_HTTPHEADER => ['Content-Type: application/json']
			]);

			$response = curl_exec($ch);
			$err = curl_error($ch);

			curl_close($ch);

			if($err) {
				return ['isOk' => false, 'error' => $err];
			} else {
				$response = json_decode($response, true);
				return ['isOk' => true, 'response' => isset($response['response']) ? $response['response'] : $response];
			}
		}

		return false;
	}

	public function genPayLink($sum, $fixed = true) {
		$payload = rand(0, 999999999999999);
		$merchantid = $this->merchantid;
		$href = "vk.com/app7044895#x{$merchantid}_{$sum}_{$payload}".($fixed ? "" : "_1");

		return $href;
	}

	public function sendTransfer($to_id, $amount, $mark_as_merchant = true) {
		$prms = [];

		$prms['method'] = 'send';
		$prms['merchantId'] = $this->merchantid;
		$prms['key'] = $this->apikey;
		$prms['toId'] = $to_id;
		$prms['amount'] = $amount;
		$prms['markAsMerchant'] = $mark_as_merchant;

		return $this->request(json_encode($prms, JSON_UNESCAPED_UNICODE)); 
	}

	public function getBalance($user_ids = []) {
		if(empty($user_ids)) {
			$user_ids = [$this->merchantid];
		}

		$prms = [];

		$prms['method'] = 'score';
		$prms['merchantId'] = $this->merchantid;
		$prms['key'] = $this->apikey;
		$prms['userIds'] = $user_ids;

		return $this->request(json_encode($prms, JSON_UNESCAPED_UNICODE));
	}

	public function getTransactions($tx = 1, $last_tx = -1) {
		$prms = [];

		$prms['method'] = 'tx';
		$prms['merchantId'] = $this->merchantid;
		$prms['key'] = $this->apikey;
		$prms['tx'] = [$tx];

		if($last_tx != -1) {
			$prms['lastTx'] = $last_tx;
		}

		return $this->request(json_encode($prms, JSON_UNESCAPED_UNICODE));
	}

	public function getLostTransactions() {
		$prms = [];
		$prms['method'] = 'lost';
		$prms['merchantId'] = $this->merchantid;
		$prms['key'] = $this->apikey;

		return $this->request(json_encode($prms, JSON_UNESCAPED_UNICODE));
	}

	public function setName($name) {
		$prms = [];

		$prms['method'] = 'setName';
		$prms['name'] = $name;
		$prms['merchantId'] = $this->merchantid;
		$prms['key'] = $this->apikey;

		return $this->request(json_encode($prms, JSON_UNESCAPED_UNICODE));
	}

	public function addCallBack($url) {
		$prms = [];

		$prms['method'] = 'set';
		$prms['callback'] = $url;
		$prms['merchantId'] = $this->merchantid;
		$prms['key'] = $this->apikey;

		return $this->request(json_encode($prms, JSON_UNESCAPED_UNICODE));
	}

	public function delCallBack() {
		$prms = [];

		$prms['method'] = 'set';
		$prms['callback'] = null;
		$prms['merchantId'] = $this->merchantid;
		$prms['key'] = $this->apikey;

		return $this->request(json_encode($prms, JSON_UNESCAPED_UNICODE));
	}
}

?>
