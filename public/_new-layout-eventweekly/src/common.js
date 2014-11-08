$(function() {

	var $userMenuButtonOpenTrigger = $(".js-user-menu-button-open-trigger"),
		$userMenuCollapsed = $(".js-user-menu-collapsed");



	$userMenuButtonOpenTrigger.on("click", function () {
		$userMenuCollapsed.toggleClass("open");
    });
});