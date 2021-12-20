<?php

class M_Protocols extends Model {

    private $fields = ['name', 'address', 'symbol', 'url', 'description', 'chain', 'logo', 'audits', 'audit_note', 'gecko_id', 'cmcId', 'category', 'chains', 'module', 'twitter', 'audit_links', 'oracles', 'slug', 'tvl', 'chainTvls', 'change_1h', 'change_1d', 'change_7d', 'staking', 'fdv', 'mcap', 'pool2', 'forkedFrom', 'listedAt', 'audit'];
    
    function __construct(){
        $this->table = TB_PREFIX.'protocols';
        parent::__construct();
    }

    public function get_today_slugs(){
        return $this->Field('name')->Where('status', '=', 1)->Where('add_date', '=', date('Y-m-d'))->Select();
    }

    public function get_today_slugs_with_curve(){
        return $this->Field('name')->Where('name', '=', 'curve')->Select();
    }

    public function disable($original_name){
        $where = $update = [];
        $where['name'] = $original_name;
        $update['status'] = 0;
        return $this->Where($where)->Update($update);
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

    public function save($protocols){
        foreach($protocols as &$protocol){
            foreach($protocol as $key => $val){
                if(!in_array($key, $this->fields)){
                    unset($protocol[$key]);
                }

                unset($protocol['id']);

                if(is_array($val)){
                    $protocol[$key] = addslashes(json_encode($val, 256));
                }else{
                    $protocol[$key] = addslashes($val);
                }
            }

            $protocol['add_time'] = time();
            $protocol['add_date'] = date('Y-m-d');
            $this->Insert($protocol);
        }

        return true;
    }

    public function get_today_chains(){
        $sql = "SELECT distinct(chains) FROM ".TB_PREFIX."protocols WHERE add_date = '".date('Y-m-d')."'";
        return $this->Query($sql);
    }

    public function remove_today_data(){
        $where = [];
        $where['add_date'] = date('Y-m-d');
        $this->Where($where)->Delete();
    }

    public function dashboard(){
        $retval = [];
        $retval['tvl'] = $this->get_total_tvl_by_date(date('Y-m-d'));
        $yesterday_tvl = $this->get_total_tvl_by_date(date_of_yesterday());

        $retval['24h_change'] = calc_24h_change($retval['tvl'], $yesterday_tvl);
        $curve_tvl = $this->get_today_total_tvl_by_protocol('curve');
        $retval['curve_dominance'] = calc_dominance($retval['tvl'], $curve_tvl);

        return $retval;
    }

    public function get_today_total_tvl_by_protocol($protocol){
        $sql = "SELECT sum(tvl) AS total FROM ".TB_PREFIX."protocols WHERE add_date = '".date('Y-m-d')."' AND name = '".$protocol."'";
        $data = $this->QueryOne($sql);
        return $data['total'];
    }

    public function get_total_tvl_by_date($date){
        $sql = "SELECT sum(tvl) AS total FROM ".TB_PREFIX."protocols WHERE add_date = '".$date."'";
        $data = $this->QueryOne($sql);
        return $data['total'];
    }

    public function get_protocols_by_chain($chain, $page){
        $sql = "SELECT id, name, symbol, tvl, chains, chainTvls, change_1h, change_1d, change_7d, mcap FROM ".TB_PREFIX."protocols AND add_date= '".date('Y-m-d')."'";
        if($chain){
            $sql .= " AND FIND_IN_SET(chains, '".$chain."')";
        }

        $sql .= " ORDER BY mcap DESC, tvl DESC";
        if(!$page) $page = 1;
        $page_size = 10;
        $start = ($page - 1) * $page_size;
        $sql .= " LIMIT ".$start.",".$page_size;
        return $this->Query($sql);
    }

}