/**
 * Extra Poll
 * https://github.com/martec
 *
 * Copyright (C) 2020-2020, Martec
 *
 * Extra Poll is licensed under the GPL Version 3, 29 June 2007 license:
 *	http://www.gnu.org/copyleft/gpl.html
 *
 * @fileoverview Extra Poll
 * @author Martec
 * @requires Mybb
 */

//https://stackoverflow.com/a/9899701
function docReady(fn) {
	// see if DOM is already available
	if (document.readyState === "complete" || document.readyState === "interactive") {
		// call on next available tick
		setTimeout(fn, 1);
	} else {
		document.addEventListener("DOMContentLoaded", fn);
	}
}

function ep_hide_extra() {
	var myClasses = document.querySelectorAll('.extra_poll'),
		i = 0,
		l = myClasses.length;

	for (i; i < l; i++) {
		myClasses[i].style.display = 'none';
	}
};

function ep_show_def() {
	ep_hide_extra();
	var def_poll = document.querySelector("span[id='default_poll']");
	def_poll.style.display = "block";
	return false;
};

function ajax_ep_get(pid) {
	load_poll = document.querySelector("span[pid='"+pid+"']");
	if (load_poll.childNodes.length == 0) {
		var xmlhttp = new XMLHttpRequest();

		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
				if (xmlhttp.status == 200) {
					load_poll.insertAdjacentHTML('beforeend', xmlhttp.responseText);
				}
			}
		};

		xmlhttp.open("GET", 'xmlhttp.php?action=ajax_ep_get&my_post_key='+my_post_key+'&pid='+pid+'', true);
		xmlhttp.send();
	}
	ep_hide_extra();
	load_poll.style.display = "block";
	var def_poll = document.querySelector("span[id='default_poll']");
	def_poll.style.display = "none";
	return false;
};

docReady(function() {
	if (typeof extrapoll_pid !== 'undefined') {
		var def_poll = document.querySelector("span[id='default_poll']");
		var thr_mode_pop = document.querySelector("div[id='thread_modes_popup']");
		var pid;
 		list_pop = document.createElement("div");
		list_pop.setAttribute("id", "extra_poll_popup");
		list_pop.setAttribute("class", "popup_menu");
		list_pop.style.display = "none";
		thr_mode_pop.after(list_pop);
		list_pop.insertAdjacentHTML('beforeend', '<div class="popup_item_container"><a href="" onclick="return ep_show_def();" class="popup_item">'+default_poll+'</a></div>');
		for (pid in extrapoll_pid) {
			new_elem = document.createElement("span");
			new_elem.setAttribute("pid", pid);
			new_elem.setAttribute("class", "extra_poll");
			new_elem.style.display = "none";
			def_poll.after(new_elem);
			def_poll = document.querySelector("span[pid='"+pid+"']");
			list_pop.insertAdjacentHTML('beforeend', '<div class="popup_item_container"><a href="" onclick="return ajax_ep_get('+pid+');" class="popup_item">'+extrapoll_pid[pid]+'</a></div>');
		}
		if(use_xmlhttprequest == "1") {
			$("#extra_poll").popupMenu();
		}
	}
});