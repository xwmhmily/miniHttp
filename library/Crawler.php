<?php
/**
 * Author: 大眼猫
 */

class Crawler {

	const DEFI_URL_PROTOCOLS        = 'https://api.llama.fi/protocols';

	const DEFI_URL_PROTOCOL_DETAIL  = "https://api.llama.fi/protocol/";

	const DEFI_URL_PROTOCOL_SLUG    = 'https://api.llama.fi/protocol/';

	const DEFI_URL_CHARTS           = 'https://api.llama.fi/charts';

	const DEFI_URL_CHAIN_CHARTS     = 'https://api.llama.fi/charts/';

	const DEBANK_URL_DETAIL         = 'https://api.debank.com/project/v2/detail?id=';

	const DEBANK_URL_PORTFOLIOS     = 'https://api.debank.com/project/portfolios/user_list?id=';

	const DEBANK_URL_CONTRACT_CALL  = 'https://api.debank.com/project/chart?type=contract_call&id=';

	const DEBANK_URL_CONTRACT_USER  = 'https://api.debank.com/project/chart?type=contract_user&id=';

	// 每天都写入新的
	public static function protocols($reget = false){
		$m_protocols = Helper::load('Protocols');

		if(!$reget){
			$has_today_done = $m_protocols->has_today_done();
			if($has_today_done){
				$error = "Protocols are already fetched today";
				Logger::log($error);
				return $error;
			}
		}else{
			// Remove today's data
			$m_protocols->remove_today_data();
		}

		$protocols = file_get_contents(self::DEFI_URL_PROTOCOLS);
		if($protocols){
			$protocols = json_decode($protocols, true);
			$m_protocols->save($protocols);
		}

		return "DONE";
	}

	// 每天都写入新的
	public static function protocol_detail($reget = false){
		$m_protocols_detail = Helper::load('Protocol_detail');

		if(!$reget){
			$has_today_done = $m_protocols_detail->has_today_done();
			if($has_today_done){
				$error = "Protocol detail are already fetched today";
				Logger::log($error);
				return $error;
			}
		}else{
			// Remove today's data
			$m_protocols_detail->remove_today_data();
		}

		$m_protocols = Helper::load('Protocols');
		$slugs = $m_protocols->get_today_slugs();
		//$slugs = $m_protocols->get_today_slugs_with_curve();
		foreach($slugs as $slug){
			$original_name = $slug['name'];
			$slug = convert_slug($original_name);
			$data = file_get_contents(self::DEFI_URL_PROTOCOL_DETAIL.$slug);
			if($data){
				$m_protocols_detail->save($original_name, $data);
			}
		}

		return "DONE";
	}

	public static function portfolios($reget = false){
		$m_slug = Helper::load('Slug');
		$m_protocols     = Helper::load('Protocols');
		$m_portfolios    = Helper::load('Portfolios');
		$m_contract_call = Helper::load('Contract_call');
		$m_contract_user = Helper::load('Contract_user');

		if(!$reget){
			$has_today_done = $m_portfolios->has_today_done();
			if($has_today_done){
				$error = "Portfolios are already fetched today";
				Logger::log($error);
				return $error;
			}
		}else{
			// Remove today's data
			$m_portfolios->remove_today_data();
			$m_contract_call->remove_today_data();
			$m_contract_user->remove_today_data();
		}

		$slugs = $m_protocols->get_today_slugs();

		foreach($slugs as $slug){
			$i = 0;
			$original_name = $slug['name'];
			$slug = convert_slug($original_name);
			// 无则写入, 有则更新
			self::detail($m_slug, $original_name, $slug);

			// 每天写入新的
			self::contract_call($m_contract_call, $slug);

			// 每天写入新的
			self::contract_user($m_contract_user, $slug);

			// 每天写入新的
			self::get_portfolios($m_portfolios, $slug);

			$i++;
			if($i == 10){
				sleep(1);
				$i = 0;
			}
		}

		return true;
	}

	private static function detail($m_slug, $original_name, $slug){
		Logger::log('Slug => '.$slug.', detail URL => '.self::DEBANK_URL_DETAIL.$slug);
		$detail = file_get_contents(self::DEBANK_URL_DETAIL.$slug);
		if($detail){
			$m_slug->save($original_name, $detail);
		}

		return true;
	}

	private static function get_portfolios($m_portfolios, $slug){
		Logger::log('Slug => '.$slug.', portfolios URL => '.self::DEBANK_URL_PORTFOLIOS.$slug);
		$portfolios = file_get_contents(self::DEBANK_URL_PORTFOLIOS.$slug);
		if($portfolios){
			$m_portfolios->save($slug, $portfolios);
		}

		return true;
	}

	private static function contract_call($m_contract_call, $slug){
		Logger::log('Slug => '.$slug.', call URL => '.self::DEBANK_URL_CONTRACT_CALL.$slug);
		$contract_call = file_get_contents(self::DEBANK_URL_CONTRACT_CALL.$slug);
		if($contract_call){
			$m_contract_call->save($slug, $contract_call);
		}

		return true;
	}

	private static function contract_user($m_contract_user, $slug){
		Logger::log('Slug => '.$slug.', user URL => '.self::DEBANK_URL_CONTRACT_USER.$slug);
		$contract_user = file_get_contents(self::DEBANK_URL_CONTRACT_USER.$slug);
		if($contract_user){
			$m_contract_user->save($slug, $contract_user);
		}

		return true;
	}

	public static function charts($reget = false){
		$m_chart = Helper::load('Chart');

		if(!$reget){
			$has_today_done = $m_chart->has_today_done();
			if($has_today_done){
				$error = "Charts are already fetched today";
				Logger::log($error);
				return $error;
			}
		}else{
			// Remove today's data
			$m_chart->remove_today_data();
		}

		$charts = file_get_contents(self::DEFI_URL_CHARTS);
		if($charts){
			$m_chart->save($charts);
		}

		return "DONE";;
	}

	public static function chains($reget = false){
		$m_protocols = Helper::load('Protocols');
		$m_chains = Helper::load('Chains');

		if(!$reget){
			$has_today_done = $m_chains->has_today_done();
			if($has_today_done){
				$error = "Chains are already fetched today";
				Logger::log($error);
				return $error;
			}
		}else{
			// Remove today's data
			$m_chains->remove_today_data();
		}

		$m_chains->save($m_protocols);
		return "DONE";;
	}

	public static function chain_chart($reget = false){
		$m_chain_chart = Helper::load('Chain_chart');

		if(!$reget){
			$has_today_done = $m_chain_chart->has_today_done();
			if($has_today_done){
				$error = "Chain charts are already fetched today";
				Logger::log($error);
				return $error;
			}
		}else{
			// Remove today's data
			$m_chain_chart->remove_today_data();
		}

		$m_chain = Helper::load('Chain');
		$chains = $m_chain->get_chains_by_date(date('Y-m-d'));
		if($chains){
			foreach($chains as $chain){
				Logger::log('Chain => '.$chain);
				// $charts = file_get_contents(self::DEFI_URL_CHAIN_CHARTS.$chain);
				// if($charts){
				// 	$m_chain_chart->save($charts);
				// }
			}
		}

		return "DONE";;
	}

}