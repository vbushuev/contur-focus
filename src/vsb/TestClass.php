<?php
namespace vsb{
    class TestClass{
        protected $options=[
            'proxy' => [
                'type' => CURLPROXY_HTTP,
                'host' => '192.168.11.7',
                'port' => 8080,
                'auth' => CURLAUTH_NTLM,
                'userpwd' => 'v.bushuev:Vampire04',
            ],
            'trace' => [
                'file' => '../logs/curltrace'
            ],
            'conturf' => [
                'api' => 'https://focus-api.kontur.ru/api2/',
                'keys' => [
                    'optimal' => [
                        'key' => '68e25afac3c766547f7e7009249f67eab150db15',
                        'queries' => 150,
                        'expiries' => '2016-11-01T00:00:00.00'
                    ],
                    'standart' => [
                        'key' => 'bc35816a3e1582f0e68ec017c955c60d74b986ad',
                        'queries' => 6600,
                        'expiries' => '2016-11-01T00:00:00.00'
                    ],
                    'default' => 'optimal'
                ]
            ]
        ];
        protected function query($q=[]){
            $query_data = "";
	        $curlOptions = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
            	CURLOPT_FOLLOWLOCATION => true
	        ];
            $key=(isset($q['key'])&&!empty($q['key'])&&isset($this->options['conturf']['keys'][$q['key']]))
                ? $this->options['conturf']['keys'][$q['key']]
                : $this->options['conturf']['keys'][$this->options['conturf']['keys']['default']]['key'];
            $url = $this->options['conturf']['api'].$q['uri'].'?key='.$key;
            $data = $q['data'];
            if(!empty($this->options['trace'])){
                $fp=fopen($this->options['trace']['file'].date("Y-m-d").'.log', 'wa');
                $curlOptions[CURLOPT_VERBOSE] = 1;
            	$curlOptions[CURLOPT_STDERR] = $fp;
            }
            if(!empty($this->options['proxy'])){
        	  $curlOptions[CURLOPT_PROXYTYPE] = $this->options['proxy']['type'];
        	  $curlOptions[CURLOPT_PROXY] = $this->options['proxy']['host'];
        	  $curlOptions[CURLOPT_PROXYPORT] = $this->options['proxy']['port'];
        	  $curlOptions[CURLOPT_PROXYAUTH] = $this->options['proxy']['auth'];
        	  $curlOptions[CURLOPT_PROXYUSERPWD] = $this->options['proxy']['userpwd'];
        	}

	        if(isset($q['method'])&&($q['method'] == "POST")){
                $curlOptions[CURLOPT_POST] = true;
		        $curlOptions[CURLOPT_POSTFIELDS] = http_build_query($data);
	        }
	        elseif(!empty($data)){
                $url .= strpos($url, "?") > 0 ? "&" : "?";
                $url .= http_build_query($data);
	        }
	        $curl = curl_init($url);
	        curl_setopt_array($curl, $curlOptions);
	        $result = curl_exec($curl);
	        return json_decode($result, 1);
        }
        public function __construct($o=[]){
            $this->options=array_merge($this->options,$o);
        }
        public function Search($q){
            if(!strlen($q))return json_decode('{}');
            $request = [
                'uri' => 'search',
                'data' => ['q'=>$q]
            ];
            return $this->query($request);
        }
        public function Entity($inn,$ogrn){
            if(!strlen($inn)||!strlen($ogrn))return json_decode('{}');
            $request = [
                'uri' => 'ul',
                'data' => ['inn'=>$inn,'ogrn'=>$ogrn]
            ];
            return $this->query($request);
        }
        public function IndividualEntrepreneur($inn,$ogrn){
            if(!strlen($inn)||!strlen($ogrn))return json_decode('{}');
            $request = [
                'uri' => 'ip',
                'data' => ['inn'=>$inn,'ogrn'=>$ogrn]
            ];
            return $this->query($request);
        }
        public function AccountingForms($ogrn){
            if(!strlen($ogrn))return json_decode('{}');
            $request = [
                'uri' => 'buhforms',
                'data' => ['ogrn'=>$ogrn]
            ];
            return $this->query($request);
        }
        public function Licences($ogrn){
            if(!strlen($ogrn))return json_decode('{}');
            $request = [
                'uri' => 'search',
                'data' => ['ogrn'=>$ogrn]
            ];
            return $this->query($request);
        }
        public function Analytics($ogrn){
            if(!strlen($ogrn))return json_decode('{}');
            $request = [
                'uri' => 'analytics',
                'data' => ['ogrn'=>$ogrn]
            ];
            return $this->query($request);
        }
        public function Autocomplete($q){
            if(!strlen($q))return json_decode('{}');
            $request = [
                'uri' => 'autocomplete',
                'data' => ['q'=>$q]
            ];
            return $this->query($request);
        }
        public function Resolve($q){
            if(!strlen($q))return json_decode('{}');
            $request = [
                'uri' => 'resolve',
                'method' => 'POST',
                'data' => json_encode([
                    'names'=>$q,
                    'addresses'=>$q,
                    'phones'=>$q,
                ])
            ];
            return $this->query($request);
        }
        public function Statistics(){
            //$optimal = $this->query(['uri'=>'stat','key'=>'optimal']);
            //$standart = $this->query(['uri'=>'stat','key'=>'standart']);
        }
    }
}
?>
