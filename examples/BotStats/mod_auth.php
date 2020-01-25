<div id="auth" class="uk-section uk-section-muted uk-flex uk-flex-wrap-reverse uk-flex-between uk-padding-small uk-padding-remove-vertical uk-padding-remove-right">
	<!-- uk-width-1-2 -->
	<form action="" id="period" class="uk-invisible uk-form-horizontal uk-width-expand">
		<legend class="uk-legend uk-visible@s">Выберите период статистики</legend>
		<label class="uk-form-label">
			<span uk-icon="icon: calendar"></span>
			Начало статистики
			<input type="date" name="from" class="uk-input" value="<?=(new DateTime('-'.(DEV?400:7).' days'))->format('Y-m-d')?>">
		</label>
		<label class="uk-form-label">
			<span uk-icon="icon: calendar"></span>
			Окончание статистики
			<input type="date" name="to" class="uk-input" value="<?=date('Y-m-d')?>">
		</label>
	</form>

	<div class="uk-width uk-animation-toggle uk-padding-small uk-padding-remove-vertical uk-flex uk-flex-column uk-flex-top" style="position:relative;">
		<a href="tg://resolve?domain=AuthKffBot" id="authlink" class="uk-button uk-button-primary" uk-icon="link" uk-tooltip="Авторизация через бота">Авторизироваться</a>
		<script async src="https://telegram.org/js/telegram-widget.js?7" data-telegram-login="AuthKffBot" data-size="medium" data-radius="100%" data-auth-url="" data-request-access="write"></script>
	</div>

</div>