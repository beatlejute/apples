<?php

use backend\models\Apple;
use yii\helpers\Html;
use yii\widgets\Pjax;

$script = '

$("button.generate").on("click", function(e) {
    $.ajax({
		url: "generate",
		type: "GET",
		cache: false,
		contentType: "application/json; charset=utf-8",
		success: function( data, textStatus, jqXHR ){
			if( typeof data.error === "undefined" ){
						window.location.reload(false);				
					}
					else{
						console.log("ОШИБКИ ОТВЕТА сервера: " + data.error );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ){
					console.log("ОШИБКИ AJAX запроса2: " + textStatus );
				}
		});
});

$("button.fall").on("click", function(e) {
    let cotainer = $(this).parent().parent();
    let id = cotainer.data("id");
    $.ajax({
		url: "fall?id=" + id,
		type: "GET",
		cache: false,
		contentType: "application/json; charset=utf-8",
		success: function( data, textStatus, jqXHR ){
			if( typeof data.error === "undefined" ){
						data = JSON.parse(data);
						if (data.errorMessage) {
						    alert(data.errorMessage);
						    return false;
						}
						
						cotainer.addClass("fallen");
						cotainer.attr("data-fallen", data.fallen);
					}
					else{
						console.log("ОШИБКИ ОТВЕТА сервера: " + data.error );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ){
					console.log("ОШИБКИ AJAX запроса2: " + textStatus );
				}
		});
});

$("button.bite").on("click", function(e) {
    let cotainer = $(this).parent().parent();
    let id = cotainer.data("id");
    $.ajax({
		url: "bite?id=" + id,
		type: "GET",
		cache: false,
		contentType: "application/json; charset=utf-8",
		success: function( data, textStatus, jqXHR ) {
			if( typeof data.error === "undefined" ){
						data = JSON.parse(data);
						if (data.errorMessage) {
						    alert(data.errorMessage);
						    return false;
						}
						
						if (data.percent > 0) {
							cotainer.find(".imageView").css("width", (data.percent / 2) + "px");
						} else {
						    cotainer.remove();
						}
					}
					else{
						console.log("ОШИБКИ ОТВЕТА сервера: " + data.error );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ){
					console.log("ОШИБКИ AJAX запроса2: " + textStatus );
				}
		});
});

$("button.remove").on("click", function(e) {
    let cotainer = $(this).parent().parent();
    let id = cotainer.data("id");
    $.ajax({
		url: "remove?id=" + id,
		type: "GET",
		cache: false,
		contentType: "application/json; charset=utf-8",
		success: function( data, textStatus, jqXHR ) {
			if( typeof data.error === "undefined" ){
						data = JSON.parse(data);
						if (data.errorMessage) {
						    alert(data.errorMessage);
						    return false;
						}
						
						cotainer.remove();
					}
					else{
						console.log("ОШИБКИ ОТВЕТА сервера: " + data.error );
					}
				},
				error: function( jqXHR, textStatus, errorThrown ){
					console.log("ОШИБКИ AJAX запроса2: " + textStatus );
				}
		});
});

setInterval(function() {
  $(".apple").each(function() {
	if($(this).data("fallen") + ' . Apple::FRESH_TIME . ' <= Date.now() / 1000) {
		$(this).addClass("rotten");
		$(this).find("svg path").attr("fill", "#' . Apple::ROTTEN_COLOR . '");
	}
});
}, 2000);

';

$this->registerJs($script);

Pjax::begin();

?>
<?= Html::button("Добавить несколько яблок", ["class" => "generate"]) ?>
<? foreach ($apples as $apple) : ?>
	<div data-id="<?= $apple->{Apple::FIELD_ID} ?>" date-fallen="<?= $apple->{Apple::FIELD_DATE_FALL} ?>" class="apple <? if ($apple->isFallen()) : ?>fallen<? endif; ?> <? if ($apple->isRotten()) : ?>rotten<? endif; ?>">
		<div class="imageView" style="width: <?= $apple->{Apple::FIELD_REMAINDER_PERCENT} / 2 ?>px;">
			<svg width="50" height="59" version="1.1" viewBox="0 0 494.16 588.58" xmlns="http://www.w3.org/2000/svg">
				<path d="m211.07 582.29c4.8941-5.4624 6.0939-8.4188 8.0692-19.883 1.7802-10.331 4.1893-15.948 8.1906-19.095 3.2778-2.5783 3.8935-2.594 6.3261-0.16144 1.6566 1.6566 2.1032 1.7281 3.1224 0.5 1.7173-2.0692 4.5597-1.7624 6.895 0.74414 1.1156 1.1975 2.1456 1.985 2.289 1.75 0.14331-0.23498 0.75879-1.3272 1.3677-2.4272 4.04-7.2979 12.364 2.6406 15.019 17.931 1.9631 11.308 3.1217 14.436 7.2251 19.507l3.5914 4.4384-4.4142-2.4384c-6.0153-3.3228-15.003-12.33-18.622-18.663l-2.9856-5.2244-2.7106 10.821c-1.4908 5.9518-2.7347 11.903-2.7642 13.224-0.0472 2.1192-2.4128-4.7466-6.1491-17.847-0.66667-2.3375-1.4665-4.25-1.7773-4.25-0.31086 0-1.455 1.6873-2.5426 3.7495-2.669 5.061-13.973 16.184-19.497 19.184l-4.4802 2.4335z" <?= $apple->isRotten() ? "fill=\"#" . Apple::ROTTEN_COLOR . "\"" : "" ?>"/>
				<path d="m157.22 549.07c-25.332-3.2289-57.41-17.026-80.985-34.834-6.1723-4.6623-23.987-21.237-28.476-26.494-1.3965-1.6354-3.3143-3.7606-4.2617-4.7226-6.0516-6.1448-19.159-26.274-24.076-36.974-24.833-54.038-23.056-115.05 4.9166-168.83 17.828-34.279 47.518-61.357 81.882-74.681 11.873-4.6036 24.317-8.1757 34.736-9.9712 13.493-2.3253 33.914-1.4052 47.264 2.1296 10.55 2.7935 31.932 9.7527 37 12.042 1.65 0.74549 5.7 2.4707 9 3.8338s9.4791 4.1116 13.731 6.1077l7.7314 3.6294 5.2686-3.8169c11.858-8.5911 20.214-12.92 34.793-18.026 13.35-4.6757 20.738-5.7536 38.976-5.6865 13.81 0.0508 17.886 0.42831 25 2.3156 15.155 4.0204 22.77 7.0483 42.5 16.899 13.154 6.5675 30.36 18.773 39 27.666 34.018 35.013 51.027 77.77 50.942 128.06-0.0567 33.483-6.6546 59.984-22.699 91.171-4.5936 8.9291-16.275 24.43-25.92 34.395-31.439 32.483-70.665 52.188-111.32 55.922-33.771 3.1014-59.305-0.88261-83.868-13.086-5.4275-2.6964-10.276-4.9026-10.775-4.9026s-3.7526 1.7976-7.2315 3.9948c-19.438 12.276-46.022 17.315-73.126 13.86z" fill="#<?= $apple->isRotten() ? Apple::ROTTEN_COLOR : $apple->{Apple::FIELD_COLOR} ?>"/>
				<path d="m254.27 204.04c0.76479-6.5233 0.71116-26.806-0.0861-32.549-4.831-34.799-23.218-61.059-50.471-72.079-3.0272-1.2241-6.8284-3.2817-8.4471-4.5724l-2.943-2.3467 7.447-11.796c4.0959-6.4879 7.7685-12.287 8.1613-12.887 1.4767-2.2552 35.47 33.866 41.49 44.087 7.9373 13.476 11.889 27.259 12.986 45.3 0.91545 15.042-2.5638 38.319-7.2324 48.386l-1.4544 3.1362 0.5486-4.6792z" <?= $apple->isRotten() ? "fill=\"#" . Apple::ROTTEN_COLOR . "\"" : "" ?>"/>
				<path d="m402.92 2.002c-0.13157 0.015144-0.20108 0.16987-0.20117 0.48242-9.7e-4 3.0634-6.085 19.903-10.199 28.232-12.905 26.126-28.066 42.537-57.801 62.559-6.875 4.6293-12.725 8.6781-13 8.9961-0.275 0.31799-2.7409 2.1031-5.4785 3.9668-21.418 14.581-36.106 34.166-41.98 55.979-1.1107 4.1244-1.9396 11.891-2.2695 21.25-0.28596 8.1125-0.27586 14.75 0.02148 14.75 0.29736 0 3.3898-2.9752 6.873-6.6113 8.9776-9.3719 23.987-20.37 34.951-25.609 10.641-5.0853 22.133-8.9546 42.383-14.27 27.537-7.2277 39.017-12.32 50.768-22.523 4.5584-3.958 12.154-15.155 14.336-21.133 8.3843-22.968 6.3152-51.121-6.0879-82.854-4.0729-10.42-11.043-23.361-12.314-23.215zm-35.314 104.71 0.03515 3.5c0.04008 4.1374-2.865 10.947-6.5488 15.352-2.6459 3.1635-13.248 11.619-14.594 11.639-0.39621 0.00578-3.0962 1.0734-6 2.373-2.9038 1.2996-8.8793 3.5939-13.279 5.0976-11.248 3.8439-24.009 10.102-30.338 14.881-6.3827 4.819-13.316 13.064-16.898 20.096-1.4186 2.7842-3.6158 7.5375-4.8809 10.562-2.1018 5.026-2.3032 5.2291-2.3418 2.3555-0.11678-8.6881 7.2896-23.396 16.471-32.709 8.5204-8.6426 16.436-12.95 36.988-20.127 25.499-8.9047 33.72-14.916 39.693-29.02l1.6934-4z" fill="#<?= $apple->isRotten() ? Apple::ROTTEN_COLOR : "038002" ?>"/>
			</svg>
		</div>
		<div class="edit">
            <?= Html::button("Сбросить с дерева", ["class" => "fall"]) ?>
            <?= Html::button("Укусить", ["class" => "bite"]) ?>
            <?= Html::button("Выкинуть", ["class" => "remove"]) ?>
		</div>
	</div>
<? endforeach; ?>
<? Pjax::end(); ?>
<style>
	.imageView {
		overflow: hidden;
		display: inline-block;
	}

	.edit {
		display: inline-block;
		float: right;
	}

	.fallen .imageView {
		transform: rotate(45deg);
	}

	.rotten .imageView {
		transform: rotate(145deg);
	}

	button.bite,
	button.remove {
		display: none;
	}

	.fallen button.bite,
	.rotten button.remove {
		display: inline-block;
	}

	.fallen button.fall,
	.rotten button.bite {
		display: none;
	}
</style>
