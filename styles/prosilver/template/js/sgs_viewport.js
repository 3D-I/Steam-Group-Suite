/**
 *
 * Steam suite. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, 3Di (http://3di.space/32/) & javiexin
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

var steam = {};

(function($) {  // Avoid conflicts with other libraries

'use strict';

// Set the objects to manage
var $container = $("#steam-container").first(),
	$dropdowns = $("#steam-header > select"),
	$state = $("#steam-state").first(),
	$game = $("#steam-game").first(),
	$label = $("#steam-shown").first(),
	$btn = $("#steam-collapse").first(),
	$save = $("#steam-save").first(),
	$reset = $("#steam-reset").first(),
	$lines = $(".steam-btn.steam-lines");

// Return if no content could be found or no buttons
if (!$container.length || !$btn.length || !$save.length || !$reset.length || $lines.length != 2) {
	return;
}

// Get the unique key used for storage
steam.key = $container.data("storage");

// Define the functions to manage the viewport

// Show the selected steam users in the viewport
steam.showSelected = function () {
	// Set specific objects
	var $cells = $(".flex-cell");

	// Hide all and get the filter values
	$cells.removeClass("steam-shown").parent().hide();
	var steamState = $state.val(),
		steamGame = $game.val();

	// If no filter values, nothing to show
	if (steamState == "none" || steamGame == "none") { $label.text(0); return; };

	// Add cells according to filter values
	if (steamState == "not") { $cells = $cells.filter(".steam-state-0"); }
	else if (steamState == "yes") { $cells = $cells.not(".steam-state-0"); }
	else if (steamState > 0) { $cells = $cells.filter(".steam-state-"+steamState); };

	if (steamGame == "not") { $cells = $cells.filter(".steam-game-0"); }
	else if (steamGame == "yes") { $cells = $cells.not(".steam-game-0"); }
	else if (steamGame > 0) { $cells = $cells.filter(".steam-game-"+steamGame); };

	// Show the number of selected cells
	$label.text($cells.size());

	// Select cells that must be shown
	$cells.addClass("steam-shown");

	// Set the correct classes and properties for the hide/show situation
	var hidden = ($btn.data("collapsed") == "yes") ? true : false;
	$dropdowns.prop("disabled",hidden);
	$btn
		.addClass(hidden ? "steam-plus" : "steam-minus")
		.parents("div.forabg").css("opacity", (hidden) ? "0.5" : "1.0")
	;

	// If not hidden, show the cells and set the max size of the container
	if (!hidden) {
		$cells.parent().show();
		$container.css("max-height", $container.children().first().height()*Number($container.data("lines")));
	};

	// Finally refresh
	$container.trigger("scroll");
};

// Toggle visibility of the viewport
steam.showHide = function() {
	var hidden = ($btn.data("collapsed") == "yes") ? true : false;

	// Toggle visibility
	hidden = !hidden;

	// Change objects for the new situation
	$btn
		.data("collapsed", hidden ? "yes" : "no")
		.toggleClass("steam-plus steam-minus")
		.parents("div.forabg").css("opacity", (hidden) ? "0.5" : "1.0")
	;
	$dropdowns.prop("disabled",hidden);
	$lines.data("disabled",hidden);

	// Now show/hide the selected cells
	if (hidden) {
		$(".flex-cell").parent().hide();
	} else {
		$(".flex-cell.steam-shown").parent().show();
		$container.css("max-height", $container.children().first().height()*Number($container.data("lines")));
	};

	localStorage.setItem(steam.key+"sgs_hidden",$btn.data("collapsed"));

	// Finally refresh 
	$container.trigger("scroll");
};

// Manage the number of lines visible through the viewport
steam.growShrink = function(btnDirection) {
	var disabled = $lines.first().data("disabled"),
		lines = Number($container.data("lines"));

	if (disabled) return;

	if (btnDirection == "grow") lines = lines+1;
	else if (btnDirection == "shrink" && lines > 1) lines = lines-1;

	$container.data("lines", lines);
	steam.showSelected();
};

// Save current configuration to permanent storage in the browser, to preserve user choices
steam.saveConfig = function() {
	localStorage.setItem(steam.key+"sgs_state",$state.val());
	localStorage.setItem(steam.key+"sgs_game",$game.val());
	localStorage.setItem(steam.key+"sgs_lines",Number($container.data("lines")));
	localStorage.setItem(steam.key+"sgs_hidden",$btn.data("collapsed"));
};

// Confirm the user intention to save the current configuration
steam.confirmSaveConfig = function() {
	var $confirm = $('#steamsaveconfirm').detach().show();
	$confirm.show().find('p').text($confirm.data('save'));
	phpbb.confirm($confirm, function() {
		steam.saveConfig();
	});
};

// Reset stored configuration so that forum defaults apply
steam.resetConfig = function() {
	localStorage.removeItem(steam.key+"sgs_state");
	localStorage.removeItem(steam.key+"sgs_game");
	localStorage.removeItem(steam.key+"sgs_lines");
	localStorage.removeItem(steam.key+"sgs_hidden");
	steam.setConfig();
};

// Confirm the user intention to save the current configuration
steam.confirmResetConfig = function() {
	var $confirm = $('#steamsaveconfirm').detach().show();
	$confirm.show().find('p').text($confirm.data('reset'));
	phpbb.confirm($confirm, function() {
		steam.resetConfig();
	});
};

// Initialize the viewport with the saved data and, if no saved data, with the default
steam.setConfig = function() {
	$state.val(localStorage.getItem(steam.key+"sgs_state") || $state.data("default"));
	$game.val(localStorage.getItem(steam.key+"sgs_game") || $game.data("default"));
	$container.data("lines", Number(localStorage.getItem(steam.key+"sgs_lines") || $container.data("default")));
	$btn.data("collapsed", localStorage.getItem(steam.key+"sgs_hidden") || $btn.data("default"));
	steam.showSelected();
};

// Set the viewport according to the saved configuration
steam.setConfig();

// Unhide the buttons (makes them JS dependent) and assing the functions
$btn.show(); // .click(steam.showHide);
$lines.show(); // .click(steam.growShrink);
$save.show(); // .click(steam.saveConfig);
$reset.show(); // .click(steam.resetConfig);

// Activate the lazy-load function on images
$("img.lazy-steam-avatar").show().lazyload({
	container: $container
});

})(jQuery); // Avoid conflicts with other libraries
