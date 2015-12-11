<?php
namespace vsb{
    class ConturFocus{
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
            $url = $this->options['conturf']['api'].$q['uri'];
            $data = [
                'key' => $this->options['conturf']['keys'][$this->options['conturf']['keys']['default']]['key'],
                'q' => $q['data']
            ];
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
                'data' => $q
            ];
            return $this->query($request);
        }
        public function Entity($q){}
        public function IndividualEntrepreneur($q){}
        public function AccountingForms($q){}
        public function Licences($q){}
        public function Autocomplete($q){}
        public function Statistics(){}
    }
}
?>
