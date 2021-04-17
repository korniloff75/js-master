<?php

/**
 ** Буферизируем исполнение всех активных плагинов и выводим результаты по мере необходимости через Plugins::getHook('hookname')
 */
class Plugins
{
	// *static class
	private function __construct() {}
	private function __clone() {}


	const
		BASE= \DR.'/plugins', //* Plugins dir
		HOOKS= [
			// *compat MyEngine
			'integration_system', // after core exec
			'integration_pages', // after page content on all pages
			'integration_page',
			'integration_blok', // in sidebar - include in template
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
		$listHooks=[],
		$availables=[], //список доступных плагинов
		$html, //defined in Render::finalPage()
		$DOM=[];

	public static function available(string $plName)
	:bool
	{
		return !file_exists(self::BASE . "/$plName/" . self::TOKENS['disable']);
	}

	public static function enable(string $plName)
	:bool
	{
		unlink(self::BASE . "/$plName/" . self::TOKENS['disable']);

		if($bool= self::available($plName)){
			self::_reinstallHooks();
		}
		return $bool;
	}

	public static function disable(string $plName)
	:bool
	{
		if($bool= file_put_contents(self::BASE . "/$plName/" . self::TOKENS['disable'],'') === 0){
			self::_reinstallHooks();
		}
		return $bool;
	}


	protected static function _reinstallHooks()
	{
		self::$listHooks = [];
		self::$availables = [];
		return self::_listHooks();
	}


	/**
	 * Составляем массив плагинов, разобранных по хукам
	 * ['hookname'=>{string} code]
	 */
	private static function _listHooks()
	:array
	{
		$listHooks= &self::$listHooks;
		if(count($listHooks)) return $listHooks;

		Site::createDir(self::BASE);

		foreach(new FilesystemIterator(self::BASE) as $dirname=>$fi){
			if(
				!$fi->isDir()
				|| !self::available($plName= $fi->getFilename())
			) continue;

			self::$availables[]= $plName;

			foreach(self::HOOKS as $hook){
				if(!file_exists($pathname= "$dirname/$hook.php")) continue;

				$listHooks[$hook]= $listHooks[$hook] ?? [];
				$listHooks[$hook][]= $pathname;
			}
		}

		return $listHooks;
	}


	/**
	 * Получаем хук из всех плагинов по имени
	 */
	static function getHook(string $hookname)
	:string
	{
		$listHooks= self::_listHooks();
		if(empty($listHooks[$hookname])) return '';

		ob_start();
		echo "<!--$hookname-plugin-->";
		foreach($listHooks[$hookname] as $pathname){
			include_once $pathname;
		}
		echo "<!--/$hookname-plugin-->";
		return ob_get_clean();
	}


	/**
	 * used in integration_end hook
	 * return array with cash of the DOMDocument & DOMXpath
	 */
	static function getDocXpath()
	:array
	{
		$DOM= &self::$DOM;
		$fixCyrillicUtf= "\xEF\xBB\xBF";

		if(
			!self::$html
			|| !empty($DOM['xpath'])
		){
			return $DOM;
		}

		$DOM['doc'] = new DOMDocument("1.0","UTF-8");

		@$DOM['doc']->loadHTML($fixCyrillicUtf . self::$html);
		$DOM['xpath'] = new DOMXpath($DOM['doc']);
		return $DOM;
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
