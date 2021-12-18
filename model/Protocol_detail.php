<?php

class M_Protocol_detail extends Model {

    const KEY_TVL          = 'tvl';
    const KEY_TOKENS       = 'tokens';
    const KEY_TOKEN_IN_USD = 'tokensInUsd';
    
    function __construct(){
        $this->table = TB_PREFIX.'protocol_detail';
        parent::__construct();
    }

    // 今天是否抓取过了
    public function has_today_done(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $data = $this->Where($where)->SelectOne();
        if($data){
            return true;
        }

        return false;
    }

    public function save($original_name, $data){
        $data = json_decode($data, true);
        if(!$data || !isset($data['chainTvls'])){
            return;
        }

        $data = $data['chainTvls'];
        foreach($data as $key => $val){
            if(!$val) continue;

            Logger::info('Key => '.$key);

            $i = [];
            $i['chain']    = $key;
            $i['slug']     = $original_name;
            $i['add_date'] = date('Y-m-d');

            // key: Avalanche, Harmony, Ethereum => chain
            // k: tvl, tokensInUsd, tokens => currency
            foreach($val as $k => $v){
                Logger::info('K => '.$k);
                if(!$v) continue;

                $i['currency'] = $k;

                if(is_array($v)){
                    if($k == self::KEY_TVL){
                        foreach($v as $tvl_val){
                            $i['key']  = '';
                            $i['date'] = $tvl_val['date'];
                            $i['num']  = $tvl_val['totalLiquidityUSD'];
                            $this->Insert($i);
                        }
                    }else if($k == self::KEY_TOKEN_IN_USD){
                        foreach($v as $token_key => $token_val){
                            foreach($token_val as $tk => $tv){
                                if($tk == 'date'){
                                    $i['date'] = $tv;
                                }else{
                                    if($tv){
                                        foreach($tv as $kkk => $vvv){
                                            $i['key'] = $kkk;
                                            $i['num'] = $vvv;
                                            $this->Insert($i);
                                        }
                                    }
                                }
                            }
                        }
                    }else if($k == self::KEY_TOKENS){
                        foreach($v as $token_key => $token_val){
                            foreach($token_val as $tk => $tv){
                                if($tk == 'date'){
                                    $i['date'] = $tv;
                                }else{
                                    if($tv){
                                        foreach($tv as $kkk => $vvv){
                                            $i['key'] = $kkk;
                                            $i['num'] = $vvv;
                                            $this->Insert($i);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function remove_today_data(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $this->Where($where)->Delete();
    }

}