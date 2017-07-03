 var gParams = {
	bounds: function() {
		var parameters = [
			["x",481,"min"], //   tablet/small screen
			["x",769,"min"], //   desktop 
		];
		var pageWidth = window.outerWidth;
		var pageHeight = window.outerHeight;
		var bounds = {
			'x': {
				'min': 0,
				'max': null
			},
			'y': {
				'min': 0,
				'max': null
			},
			contains: function(axis,dimension) {
				switch (axis) {
					case "x":
						if(dimension > bounds['x']['min'] && (bounds['x']['max'] == null || dimension < bounds['x']['max'])) { return true; } else { return false; }
					break;
					case "y":
						if(dimension > bounds['y']['min'] && (bounds['y']['max'] == null || dimension < bounds['y']['max'])) { return true; } else { return false; }
					break;
				}
			}
		}
		for(var i = 0; i < parameters.length; i++) {
			var p = parameters[i];
			switch (p[0]) {
				case "x":
					if(p[2] == "min" && pageWidth > p[1] && p[1] > bounds['x']['min']) { bounds['x']['min'] = p[1]; }
					if(p[2] == "max" && pageWidth < p[1] && (bounds['x']['max'] == null || p[1] < bounds['x']['min'])) { bounds['x']['max'] = p[1]; }
				break;
				case "y":
					if(p[2] == "min" && pageHeight > p[1] && p[1] > bounds['y']['min']) { bounds['y']['min'] = p[1]; }
					if(p[2] == "max" && pageHeight < p[1] && (bounds['y']['max'] == null || p[1] < bounds['y']['min'])) { bounds['y']['max'] = p[1]; }
				break;
			}
		}
		return bounds;
		//return: {'x':{'min':0,'max':1000},'y':{'min':0,'max':1000}}
	}
}

var ai = {
	init: function () {
		ai.page.title = document.title;
		ai.page.url = window.location.protocol + "//" + window.location.host + window.location.pathname.split('?')[0];
		
		History.Adapter.bind(window,'statechange',function(){ var State = History.getState(); ai.setContent(State.data, true); });
		
		ai.setLinks();
		
		$(".singleFormInput-text").find("input").removeAttr("size");
		$(".singleFormInput-text").find("input[type='email']").attr("placeholder", "email address");
		$("#footermailsignup, #footermailsignup-label").on('click', function(){
				ai.cookies.set("s_ml", "1", 0, "/");
				ai._modal.setContent($("#maillistform").html());
		});
		
		var initState = History.getState();
		History.replaceState({ title: ai.page.title, url: ai.page.url, main: $("main").html() }, ai.page.title, ai.page.url);
		
		eval(ai.page.ready);	
	},
	setContent: function(data, isPopState) {
		
		if(data.hasOwnProperty("main") && data["main"]){
			var pMain = data["main"];
			ai.page.title = data["title"];
			ai.page.url = data["url"];
			ai.page.before = data["pageBefore"];
			ai.page.ready = data["pageReady"];
			eval(ai.page.leave);
			ai.page.leave = null;
			ai.page.leave = data["pageLeave"];
			document.title = ai.page.title;
			eval(ai.page.before);
			ai.page.before = null;
			$("main").html(pMain);
			eval(ai.page.ready);
			ai.page.ready = null;
			window.scrollTo(0,0);
			ai.setLinks();
			try { ain.controls["globalMenu"].exit(); } catch(e) { }
		}
		if(data.hasOwnProperty("mediaResult")) {
			ai.deploy_Media.setContent(data, (isPopState ? true : false)); }
	},
	setLinks: function(){
		$( "a[href^='http://'], a[href^='https://']" ).each(function() {
			//var comp = new RegExp(location.host);
			if(new URL($(this).attr("href")).host != location.host){
				$(this).attr("target","_blank");
			}
		});
		$( "a[href!='#'][target!='_blank']" ).off("click");
		$( "a[href!='#'][target!='_blank']" ).on("click", function(event){
			event.preventDefault();
			ai.goto($(this).attr("href"));
			return false;
		});
	},
	goto: function (url) {
		var key = "ajaxpipe";
		var value = "1";
		var uri = url;
		var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";
		if (uri.match(re)) {
			uri = uri.replace(re, '$1' + key + "=" + value + '$2');
		}
		else {
			uri = uri + separator + key + "=" + value;
		}
		$.ajax({
			url: uri,
			data: {
				"ajaxpipe": true
			},
			type: "GET",
			dataType: "njson json"//,
			//success: function(data, textStatus, response){ ai.setContent(data, false, response); }
		}).always(function(data){
			if(data.hasOwnProperty("mediaResult") && !data.hasOwnProperty("main"))
				History.pushState(data, data["media_title"], data["media_url"]);
			else
				History.pushState(data, data["title"], data["url"]);
		});
	},
	page: {
		/* 
		
		***Page Event Functions***
		
		before:	executes before page content is loaded into the DOM after ai.page.leave
		ready:	executes after page content is loaded into the DOM
		leave:	executes before current page content is replaced by new page content
		
		All functions are stored as strings. They are evaluated and executed in ai.setContent 
		and are reset to null after execution.
		
		*/
		before: null,
		ready: null,
		leave: null,
		
		/*
		
		***Page Attributes***
		
		Page attributes are used to support History.js framework
		
		*/
		
		title: null,
		url: null,
		mediapermalink: null
	},
	
	deploy_Media: {
		
		init: function(data, isPopState, isPush){
			var insertMedia = '<div class="mediacontainer"><\/div>';
			$(".mediaBox").prepend(insertMedia); 
			},
		
		setContent: function(data, isPopState, isPush) {
					
			if($(".mediacontainer").length > 0){
				$(".mediacontainer").html(data['mediaResult']);
				History.pushState(History.getState(), data['media_title'], data['media_url']);
				$(".mediacontainer").scrollTop(0);
				ai.deploy_Media.isOpen = true;
				ai.setLinks();
			}else {
				ai.deploy_Media.init(data, isPopState, (isPush !== false ? true : false));
				$(".mediacontainer").html(data['mediaResult']);
				History.pushState(History.getState(), data['media_title'], data['media_url']);
				$(".mediacontainer").scrollTop(0);
				ai.setLinks(); }
		},
	},
	_modal: {
		init: function (content) {
			$("body").on('touchmove', function(e){e.preventDefault()});
			$("body").addClass("stop-scrolling");
			var insertContent = '<div id="mscover"><div id="mscontainer"><\/div><\/div>';
			$("body").append(insertContent);
			if(content) {
				ai._modal.setContent(content);
			}
			ai._modal.isOpen = true;
		},
		setContent: function (content) {
			if($("div#mscover").length > 0) {
				$("#mscontainer").html(content);
				ai._modal.setControls();
				ai._modal.size();
			}
			else {
				ai._modal.init(content);
			}
		},
		setControls: function () {
			$(".mscontrol").off("click");
			$(".mscontrol").on("click", function(event){
				event.preventDefault();
				var action = $(this).attr("data-ms-control-action");
				switch(action) {
					case "exit":
						ai._modal.exit();
					break;
				}
			});
		},
		size: function () {
			$("#mscontainer").css({ top: (($(window).height() - $("#mscontainer").height()) / 2) + "px" });
		},
		exit: function () {
			$("body").off('touchmove');
			$("body").removeClass("stop-scrolling");
			$("#mscover").remove();
			ai._modal.isOpen = false;
		},
		isOpen: false
	},
	
	categoryMedia: {
		init: function () {
			var container = document.querySelector("#animates-grid-container");
			
			imagesLoaded(container, function () {
				ai.categoryMedia.msnry = new Masonry( container, {
					columnWidth: ".masonry-grid-sizer",
					gutter: ".masonry-gutter-sizer",
					itemSelector: ".masonry-item",
					percentPosition: true
				});
				ai.categoryMedia.msnry.on('layoutComplete', function() {
					ai.categoryMedia.size();
				});
			});
		},
		size: function () {
			$(".masonry-item").each(function(){
				$(this).css({ "marginBottom": $(".masonry-gutter-sizer").outerWidth(false) + "px" });
			});
		},
		destroy: function () {
			ai.categoryMedia.msnry.destroy();
		},
		msnry: null
	},
	searchPage: {
		setSearch: function () {
			$( "#s" ).on("keyup", function() {
				ai.searchPage.getResults();
			});
		},
		currentRequest: null,
		getResults: function() {
			if(ai.searchPage.currentRequest){
				ai.searchPage.currentRequest.abort();
			}
			ai.searchPage.currentRequest = $.ajax({
				url: "/",
				data: {
					"category_id": parseInt($("#s").parent().attr("data-category-id")),
					"s": $("#s").val(),
					"format": $("#s").parent().attr("data-format")
				},
				type: "GET",
				dataType: "text/html",
				complete: function(data) {
					ai.searchPage.currentRequest = null;
					var cm;
					if(ai.categoryMedia.msnry) { cm = true; ai.categoryMedia.msnry.destroy(); } else { cm = false; }
					$($("#s").parent().attr("data-resultscontainer")).html(data.responseText);
					if(cm) { ai.setLinks(); ai.categoryMedia.init(); }
				}
			});
		}
	}, 
	social: {
		getCount: function () {
			var currentUrl = History.getState().url;
			$.ajax({
				url: "/social.php",
				data: {
					"url": currentUrl
				},
				type: "GET",
				dataType: "application/json",
				complete: function(data){
					var responseText = JSON.parse(data.responseText);
					if(responseText.twitter_count) { $(".socialcount[data-source='twitter'][data-url='" + currentUrl + "']").html(responseText.twitter_count); }
					if(responseText.facebook_count) { $(".socialcount[data-source='facebook'][data-url='" + currentUrl + "']").html(responseText.facebook_count); }
				}
			});
		}
	},
	cookies: {
		get: function (name) {
			var dc = document.cookie;
			var prefix = name + "=";
			var begin = dc.indexOf("; " + prefix);
			if (begin == -1) {
				begin = dc.indexOf(prefix);
				if (begin != 0) return null;
			}
			else
			{
				begin += 2;
				var end = document.cookie.indexOf(";", begin);
				if (end == -1) {
				end = dc.length;
				}
			}
			return unescape(dc.substring(begin + prefix.length, end));
		},
		set: function (name, value, expires, path) {
			if(!expires) { expires = 0; }
			if(!path) { path = window.location.pathname; }
			var cookie = name + "=" + value + "; expires=" + expires + "; path=" + path;
			document.cookie = cookie;
		}
	}
}

$(document).ready(function(){ ai.init(); ain.init(); $(window).scroll(); }); //initiate the doc as ready to scroll

$(document).scroll(function() {
	
	//console.log($("data-minHeight").position());
		
	var parallax = document.querySelectorAll(".parallax");
	$(".parallax").each(function(){
		$this = $(this);
		
		if($(this).height() >= $($(this).attr("data-minHeight")).height() + window.pageYOffset) {
			$(this).siblings(".parallaxFiller").remove();
			$(this).css({ 
				"height": ($(this).attr("data-origin-height") - window.pageYOffset) + "px",
				"position": "relative",
				"top": "0px" });
				
			$(this).find(".footer").css("opacity", 1 - 2*(window.pageYOffset / ($(this).attr("data-origin-height") - $($(this).attr("data-minHeight")).height())));
			if(!$(this).find("figure.blur").hasClass("hide")) { $(this).find("figure.blur").addClass("hide"); }
			$(".side-nav").css({"position" : "static"});
		}
		else{
			if($(this).siblings(".parallaxFiller").length < 1) {
				$(this).before("<div class='parallaxFiller' style='position:relative;height:" + $(this).height() + "px'></div>");
			}
			$(this).css({
				"position": "fixed",
				"top": ($(this).height() - $($(this).attr("data-minHeight")).height())*-1 + "px" });
				
			if($(this).find("figure.blur").hasClass("hide")) { $(this).find("figure.blur").removeClass("hide"); 
			}
	
		var sideNavCheck = document.querySelectorAll(".side-nav");
		
		if ( sideNavCheck.length != 0 ) {
			
			$(".side-nav").css({"position" : "fixed", "top" : $($this.attr("data-minHeight")).height() + ($("#main").innerHeight() - $("#main").height())/2 + "px"});
			
			if( $(".side-nav").offset().top + $(".side-nav").height() > $("footer").offset().top ) {
        	$(".side-nav").css({bottom : $("footer").outerHeight() + "px"}); 
			}
    		if($(document).scrollTop() + window.innerHeight < $("footer").offset().top) {
        $(".side-nav").css({"position" : "fixed", "top" : $($this.attr("data-minHeight")).height() + ($("#main").innerHeight() - $("#main").height())/2 + "px", "bottom" : 0 + "px" }); 
				}//restore when you scroll up
	
			}//end the side-nav adjustment query*/ 
		} //end of ".parallax".each
	}); //end of $(document).scroll func 
	  
	if($("#m-single-main__hero").length > 0){
		if(ai.cookies.get("s_ml") == "0" && ai.cookies.get("p_ml") == "0" && ($(document).scrollTop() + $(window).height()) > $("section#social").offset().top + $("section#social").outerHeight(true)) {
			ai.cookies.set("s_ml", 1, 0, "/");
			ai._modal.setContent($("#maillistform").html());
		}
	}
	
	if($("#m-page-main__hero").length > 0) {
		$hero = $("#m-page-main__hero");
		if($hero.height() >= $($hero.attr("data-minHeight")).height() + window.pageYOffset) {
			//$hero.siblings(".parallaxFiller").remove();
			$hero.css({ 
				"height": ($hero.attr("data-origin-height") - window.pageYOffset) + "px",
				"position": "relative",
				"top": "0px"
			});
			$hero.find(".footer").css("opacity", 1 - 2*(window.pageYOffset / ($hero.attr("data-origin-height") - $($hero.attr("data-minHeight")).height())));
			if(!$("#m-global-header").hasClass("backgroundTransparent")) { $("#m-global-header").addClass("backgroundTransparent"); };
		}
		else{
			/*if($hero.siblings(".parallaxFiller").length < 1) {
				$hero.before("<div class='parallaxFiller' style='position:relative;height:" + $hero.height() + "px'></div>");
			}*/
			if($("#m-global-header").hasClass("backgroundTransparent")) { $("#m-global-header").removeClass("backgroundTransparent"); };
		}
	}
	
	if($("#categoryHero.preview").length > 0) {
		$(".category-result").each(function(){
			if(($(this).offset().top + $(this).height()) < ($("#categoryHero").offset().top + $("#categoryHero").height()) && $(this).hasClass('visible')) {
				$(this).addClass('invisible');
				$(this).removeClass('visible');
			}
			else if(($(this).offset().top + $(this).height()) >= ($("#categoryHero").offset().top + $("#categoryHero").height()) && $(this).hasClass('invisible')) {
				$(this).removeClass('invisible');
				$(this).addClass('visible');
			}
		});
	}
});

$(window).resize(function(){
	if($("#contentContainer.parallax-cover").length > 0) {
		$("#contentContainer").css({ minHeight: ($(window).height() - $("#navContainer").height() - $("header").height() - $("footer").height()) + "px" });
		var pObject = $("#contentContainer.parallax-cover").find(".parallax-object");
		pObject.css({ width: $(window).width() + "px", height: "auto" });
	}
	
	if($("#animates-grid-container").length > 0){
		ai.categoryMedia.size();
	}
});

var ain = {
	init: function () {
		//set Controls
		$(".gControl").each(function () { var controlID = ain.controls.length; var c = $(this).attr('class').split('gControl-')[1].split(' ')[0]; ain.controls[c] = new ain.control(c); $(this).on('click', function() { var control = ain.controls[c]; control.toggle(); }); });
	},
	control: function (Name) {
		var cStatus = 0; //0 = off, 1 = in progress, 2 = on
		var cErrors = {
			notDefined: "Control not defined.",
			notFound: "Control \"" + this.Name + "\" not found."
		}
		if(Name === null) { throw cErrors.notDefined; }
		this.Name = Name;
		this.getStatus = function () {
			if(cStatus == 0) { return "off"; }
			else if(cStatus == 2) { return "on"; }
		}
		this.toggle = function () {
			switch (this.Name) {
				case "globalSearch":
					if(cStatus == 0) {
						$('#m-global-header__search').find('input').eq(0).addClass('animate');$('.gControl.gControl-globalSearch').eq(0).addClass('search-open').removeClass('search-close'); cStatus = 2; $('.gControl.gControl-globalSearch').eq(0).attr("style", $('.gControl.gControl-globalSearch').eq(0).attr("data-gControl-style-1"));
					}
					else if(cStatus == 2) {
						$('#m-global-header__search').find('input').eq(0).removeClass('animate');$('.gControl.gControl-globalSearch').eq(0).addClass('search-close').removeClass('search-open'); cStatus = 0; $('.gControl.gControl-globalSearch').eq(0).attr("style", $('.gControl.gControl-globalSearch').eq(0).attr("data-gControl-style-0"));
					}
				break;
				case "globalMenu":
					if(cStatus == 0) {
						$('#m-global-header__nav').removeClass('hidden'); $('.gControl.gControl-globalMenu').eq(0).addClass('menu-open').removeClass('menu-close'); cStatus = 2; $('.gControl.gControl-globalMenu').eq(0).attr("style", $('.gControl.gControl-globalMenu').eq(0).attr("data-gControl-style-1"));
					}
					else if(cStatus == 2) {
						$('#m-global-header__nav').addClass('hidden'); $('.gControl.gControl-globalMenu').eq(0).addClass('menu-close').removeClass('menu-open'); cStatus = 0; $('.gControl.gControl-globalMenu').eq(0).attr("style", $('.gControl.gControl-globalMenu').eq(0).attr("data-gControl-style-0"));
					}
				break;
				throw cErrors.notFound;
			}
		}
		this.exit = function () {
			if(this.getStatus() == "on") { this.toggle(); }
		}
	},
	controls: new Object(),
	gallery: function(Name, Type) {
		this.Type = Type; //"photo" or "video" or "photo,video"
		this.Name = Name;
		var TypeList = this.Type.toLowerCase().split(",");
		this.Show = function(Permalink) {
		}
		this.Item = function(Permalink) {
			this.Permalink = "";
			this.Title = "";
			//this.
		}
	}
}