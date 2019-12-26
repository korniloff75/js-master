<div id="calcBox" style=" z-index: 10; position: absolute; right: 0;" class="DA_del" >
	<button id="calcBut" class="btn min700" >Калькулятор</button>

	<div id="CalcFr" hidden style="width:220px; z-index: 100; text-align:center; background:#eef;"></div>
	
	<script type="text/javascript">
		_K.G('#calcBut').onclick = function(e) {
			var CalcFr = _K.G('#CalcFr');
			CalcFr.cr('script');

			if (!this.inited) console.log(
				$(CalcFr).load('<?=$m_path?>calc.php', {calc:1}, function() {
					_K.G('#Input').focus();
					$.getScript("<?=$m_path?>calc.js");
				})
			);

			CalcFr.hidden = !CalcFr.hidden;

			if(CalcFr.hidden) {
				this.textContent= 'Калькулятор';
			} else {
				this.textContent= 'Спрятать'; 
				_K.G('#calcBox', {position : 'fixed', right : 0, top : 0});
			}
			this.inited = true;
		};
		
		// 
	</script>
</div>