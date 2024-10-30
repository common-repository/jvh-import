jQuery(function($) {
	$('.export_vc_snippets').click(function() {
		exportVcTemplate();
	});

	function exportVcTemplate() {
		jQuery.ajax({
			url: 'https://import.jvh.software/',
			type: 'post',
			data: {
				templateData: getTemplateData(),
			},
			beforeSend: function() {
				$('.export_vc_snippets').addClass('loading');
			},
			success: function(response) {
				$('.export_vc_snippets').removeClass('loading');
				alert(response);
			},
		});
	}

	function getTemplateData() {
		return {
			license_key: exportData.jvhImportKey,
			post_id: getPostId(),
			title: getTitle(),
			content: getContent(),
			categories: getCategoryNames(),
			image_url: getImageUrl(),
			extra_classes: getExtraClasses(),
		};
	}

	function getImageUrl() {
		var srcThumb = $('#postimagediv img').attr('src');
		var srcFull = srcThumb.replace(/-\d+x\d+/,'');

		return srcFull;
	}

	function getPostId() {
		return $('#post_ID').val();
	}

	function getTitle() {
		return $('#title').val();
	}

	function getContent() {
		return $('#content').val();
	}

	function getCategoryNames() {
		var names = [];

		$('#category-jvh-templatechecklist input:checked').each(function() {
			var name = $(this).parent().text();
			names.push(name);
		});

		return names;
	}

	function getExtraClasses() {
		var classes = [];
		var classNames = getExtraClassNames();

		for (var i = 0; i < classNames.length; i++) {
			var className = classNames[i];
			var cssClass = exportData.cssClasses[className];

			if (cssClass != undefined) {
				classes.push(cssClass);
			}
		}

		return classes;
	}

	function getExtraClassNames() {
		var matches = getContent().match(/extra_css_class=".*?"/g);

		if (matches == null) {
			return [];
		}

		for (var i=0; i < matches.length; i++) {
			matches[i] = matches[i].replace('extra_css_class="', '').replace('"', '');
		}

		return matches;
	}
});
