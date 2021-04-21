<?php
/*
* @param data - массив элементов из файла
* Функция возвращает массив, содержащий фрагмент
* массива $file, соответствующий запросу $name_request
*/

trait Paginator
{
	private
	// *DbJSON
		$file;
	protected
		$paginator;

	public function Paginator (int $max_entries=10, string $name_request='p', $reverse=1, $hash="")

	{
		$html = '';

		// *DbJSON
		$file= &$this->file;

		// var_dump($file);

		if(!$data_count = count($file)) return false;
		// var_dump($data_count);

		// if($reverse) $file= array_reverse($file);
		if($reverse) $file->reverse();

		$page_blocks_count=ceil($data_count/$max_entries);

		$p = $_REQUEST[$name_request] ?? "1";

		$first_page= ($p-1)*$max_entries;
		$last_page= $p*$max_entries; # -1


		if($page_blocks_count != 1) {
			$html .= "<ul class='uk-pagination uk-flex-center' data-id=\"$name_request\">";

			for($u=1; $u<=$page_blocks_count; $u++) {
				if($p!=$u){
					$html .= "<li><a href='?$name_request= $u'>$u</a></li> ";
				}	elseif($p==$u){
					$html .= "<li class=uk-active><b>$u</b></li>";
				}
			}

			$html .= "</ul>";
		} else {
			$html = '';
		}

		$this->paginator= [
			'fragm'=>array_slice($file->get(),$first_page,$max_entries),
			'html' => $html,
			'fp'=>$first_page,
			'lp'=>$last_page,
			'data_count'=>$data_count,
		];
	}
}