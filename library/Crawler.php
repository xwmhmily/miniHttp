<?php
/**
 * Author: 大眼猫
 */

class Crawler {

	const DEFI_URL_PROTOCOLS     = 'https://api.llama.fi/protocols';

	const DEFI_URL_PROTOCOL_SLUG = 'https://api.llama.fi/protocol/';

	const DEFI_URL_CHARTS        = 'https://api.llama.fi/charts';

	const DEBANK_URL_PORTFOLIOS  = 'https://api.debank.com/project/portfolios/user_list?id=';

	public static function protocols($reget = false){
		$m_protocols  = Helper::load('Protocols');
		$m_portfolios = Helper::load('Portfolios');

		if(!$reget){
			$has_today_done = $m_protocols->has_today_done();
			if($has_today_done){
				Logger::log("Protocols are already fetched today");
				return;
			}
		}else{
			// Remove today's data
			$m_protocols->remove_today_data();
			$m_portfolios->remove_today_data();
		}

		$protocols = file_get_contents(self::DEFI_URL_PROTOCOLS);
		if($protocols){
			$protocols = json_decode($protocols, true);
			$m_protocols->save($protocols);

			$i = 0;
			foreach($protocols as $slug){
				self::portfolios($m_portfolios, $slug['name']);
				$i++;

				if($i == 10){
					sleep(1);
					$i = 0;
				}
			}
		}

		return true;
	}

	public static function charts($reget = false){
		$m_chart = Helper::load('Chart');

		if(!$reget){
			$has_today_done = $m_chart->has_today_done();
			if($has_today_done){
				Logger::log("Charts are already fetched today");
				return;
			}
		}else{
			// Remove today's data
			$m_chart->remove_today_data();
		}

		$charts = file_get_contents(self::DEFI_URL_CHARTS);
		if($charts){
			$m_chart->save($charts);
		}

		return true;
	}

	private static function portfolios($m_portfolios, $slug){
		$slug = $m_portfolios->convert_slug($slug);
		$portfolios = file_get_contents(self::DEBANK_URL_PORTFOLIOS.$slug);
		if($portfolios){
			$m_portfolios->save($slug, $portfolios);
		}

		return true;
	}

}