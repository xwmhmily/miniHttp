<?php
/**
 * Author: 大眼猫
 */

class Crawler {

	const DEFI_URL_PROTOCOLS     = 'https://api.llama.fi/protocols';

	const DEFI_URL_PROTOCOL_SLUG = 'https://api.llama.fi/protocol/';

	const DEFI_URL_CHARTS        = 'https://api.llama.fi/charts';

	const DEBANK_URL_PORTFOLIOS  = 'https://api.debank.com/project/portfolios/user_list?id=';

	public static function protocols(){
		$m_protocols = Helper::load('Protocols');
		$has_today_done = $m_protocols->has_today_done();
		if($has_today_done){
			Logger::log("Protocols are already fetched today");
			return;
		}

		$protocols = file_get_contents(self::DEFI_URL_PROTOCOLS);
		if($protocols){
			$protocols = json_decode($protocols, true);
			$m_protocols->save_protocols($protocols);
			/*
			foreach($protocols as $slug){
				$slug = file_get_contents(self::DEFI_URL_PROTOCOL_SLUG.$slug);
				if($slug){
					$slug = json_decode($slug, true);
					$m_protocols->save_slug($slug);
				}
			}
			*/

			foreach($protocols as $slug){
				self::portfolios($slug['name']);
			}
		}

		return true;
	}

	public static function charts(){
		$m_chart = Helper::load('Chart');
		$has_today_done = $m_chart->has_today_done();
		if($has_today_done){
			Logger::log("Charts are already fetched today");
			return;
		}

		$charts = file_get_contents(self::DEFI_URL_CHARTS);
		if($charts){
			$m_chart->save($charts);
		}

		return true;
	}

	private static function portfolios($slug){
		$portfolios = file_get_contents(self::DEBANK_URL_PORTFOLIOS.$slug);
		if($portfolios){
			$m_portfolios = Helper::load('Portfolios');
			$m_portfolios->save($slug, $portfolios);
		}

		return true;
	}

}