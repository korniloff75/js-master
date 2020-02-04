<?php
require_once "UniConstruct.trait.php";

class GameTest extends CommonBot {
	use UniConstruct;

	/**
	 * @param cmd - 'cmdName_opt1_opt2_...etc'
	 */
	public function __construct(UniKffBot &$UKB, ?string $cmd=null)
	{
		$this->setConstruct($UKB, $cmd)->routerCmd();

	} //* __construct


	private function init()
	{
		// $this->routerCmd();
	} //* init

	private function routerCmd()
	{
		$o=null;
		switch ($this->cmd[0]) {
			case 'ℹ️Информация':
				$o = [
					'text' => 'Тут будет ну оооочень нужная информация...',
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => '💡 Дополнительно'],
								['text' => '❓ Помощь'],
								['text' => '⚙️ Настройки'],
							],
							[
								['text' => '⬅️ Главная'],
							],
				],],];
				break;

			case '❓ Помощь':
				$o = [
					'text' => "Поможем всем!\nТут будут ссылки на поддержку. Скорее всего, инлайн-кнопками.",
					'reply_markup' => [
						"inline_keyboard" => [
							[
								['text' => 'Support', 'url' => 'https://t.me/korniloff75'],
								['text' => 'Поддержка разработчика', 'url' => 'https://t.me/korniloff75'],
							],
							[
								['text' => '💬 Community', 'url' => 'https://t.me/korniloff75'],
							],
						],
						/* "keyboard" => [
							[
								['text' => '💡 Дополнительно'],
								['text' => '⚙️ Настройки'],
							],
							[
								['text' => '⬅️ Назад'],
							],
						], */
				],];
				break;

			default:
				$o = [
					'text' => 'Сделайте свой выбор',
					'reply_markup' => [
						"keyboard" => [
							[
								['text' => '🗃Открыть кейс'],
							],
							[
								['text' => '💰Баланс'],
								['text' => 'ℹ️Информация'],
							],
				],],];
				break;
		}

		if($o)
		{
			$o['reply_markup'] += ["one_time_keyboard" => false, "resize_keyboard" => true, "selective" => true];
			$this->apiRequest($o);
		}
	}


	private function showMainMenu()
	{
		// $this->apiResponseJSON([
		$this->apiRequest([
			'text' => 'Сделайте свой выбор',
			'reply_markup' => [
				"keyboard" => [
					[
						['text' => '🗃Открыть кейс'],
					],
					[
						['text' => '💰Баланс'],
						['text' => 'ℹ️Информация'],
					]
				],
				"one_time_keyboard" => false,
				"resize_keyboard" => true
			],
		]);
	} //* showMainMenu

} //* GameTest
