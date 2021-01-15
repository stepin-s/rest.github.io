<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Приложения");
?>

<section class="content">
	<div class="container">
		<h1>Наши приложения доступны для скачивания:</h1>
		<ul class="footer__apps-list">
		    <li>
		        <a href="https://play.google.com/store/apps/details?id=com.gootax.client" class="get-app__app google"></a>
		    </li>
		    <li>
		        <a href="https://apps.apple.com/us/app/id998929451?l=ru&ls=1" class="get-app__app appstore"></a>
		    </li>
		</ul>
	</div>
</section>
<script>
	$(document).ready(function() {
		if((navigator.userAgent.match(/Android/i))) { 
		window.location = 'https://play.google.com/store/apps/details?id=com.gootax.client';
		} else if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
			window.location = 'https://apps.apple.com/us/app/id998929451?l=ru&ls=1';
		}
	})
	
</script>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>