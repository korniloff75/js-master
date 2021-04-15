<?php

/**
 * static class
 * Буферизируем исполнение всех активных плагинов и выводим результаты по мере необходимости через Plugins::getHook('hookname')
 */
class Plugins
{
	// use Get_set;

	const
		BASE= \DR.'/plugins', //* Plugins dir
		HOOKS= [
			// *compat MyEngine
			'integration_system', // after core exec
			'integration_pages', // after page content on all pages
			'integration_page',
			'integration_blok', // in sidebar
			'integration_end', // after render html
			'admin',
			'integration_admin',
			// *other
			'head','header','content','footer',
		],
		// *Файлы маркеры
		TOKENS=[
			'disable'=>'.disabled', // *Plugin disabled
		];

	static
		// $pathname=
		$list=[],
		$html,
		$doc,
		$xpath;


	/**
	 * Составляем массив плагинов, разобранных по хукам
	 * ['hookname'=>{string} code]
	 */
	private static function _list()
	{
		$list= &self::$list;
		if(count($list)) return $list;

		Site::createDir(self::BASE);

		foreach(new FilesystemIterator(self::BASE) as $dirname=>$fi){
			if(
				!$fi->isDir()
				|| file_exists("$dirname/".self::TOKENS['disable'])
			) continue;

			foreach(self::HOOKS as $hook){
				if(!file_exists($pathname= "$dirname/$hook.php")) continue;

				$list[$hook]= $list[$hook] ?? [];
				$list[$hook][]= $pathname;
			}
		}

		return $list;
	}


	/**
	 * Получаем хук из всех плагинов по имени
	 */
	static function getHook(string $hookname)
	:string
	{
		$list= self::_list();
		if(empty($list[$hookname])) return '';

		ob_start();
		echo "<!--$hookname-plugin-->";
		foreach($list[$hookname] as $pathname){
			include_once $pathname;
		}
		echo "<!--/$hookname-plugin-->";
		return ob_get_clean();
	}


	static function getDocXpath()
	:array
	{
		$out= [
			'doc'=> &self::$doc,
			'xpath'=> &self::$xpath
		];

		if(
			!self::$html
			|| $out['xpath']
		){
			return $out;
		}

		$out['doc'] = new DOMDocument("1.0","UTF-8");
		@$out['doc']->loadHTML("\xEF\xBB\xBF" . self::$html);
		$out['xpath'] = new DOMXpath($out['doc']);
		return $out;
	}


	// todo
	static function appendTo(string $blockName, string $html)
	{
		if(!self::$html){
			tolog(__CLASS__.'::$html is EMPTY',E_USER_WARNING);
			return;
		}


	}

}
