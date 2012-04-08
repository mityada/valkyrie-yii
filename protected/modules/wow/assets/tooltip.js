var Tooltip = {

    csrfToken : '',
    csrfTokenName : null,
    wrapper: null,
    contentCell: null,
    cache: {},
    initialized: false,
	currentNode: null,
	visible: false,
	options: {
		ajax: false,
		className: false,
		location: 'topRight'
	},

    initialize: function() {
		var tooltipDiv = $('<div/>').addClass('ui-tooltip').appendTo("body");

		Tooltip.contentCell = $('<div/>').addClass('tooltip-content').appendTo(tooltipDiv);

        // Assign to reference later
        Tooltip.wrapper = tooltipDiv;
        Tooltip.initialized = true;
    },

	bind: function(query, options) {
		query = $(query);
		query.unbind('mouseover.tooltip');
		query.bind('mouseover.tooltip', function(e) {
			var title = $(this).data('tooltip') || this.title;

			if (title)
				Tooltip.show(this, title, options);
		});
	},

    show: function(node, content, options) {
		if (!Tooltip.wrapper)
			Tooltip.initialize();

		if (options === true)
			options = { ajax: true };
		else
			options = options || {};

		options = $.extend({}, Tooltip.options, options);

		Tooltip.currentNode = node = $(node);

		// Update trigger node
        node.mouseout(function() {
        	Tooltip.hide();

			if (options.className)
				Tooltip.wrapper.removeClass(options.className);
        });

		// Update values
		if (!Tooltip['_'+ options.location])
			options.location = Tooltip.options.location;

		// Left align tooltips in the right half of the screen
		if (options.location == Tooltip.options.location && node.offset().left > $(window).width() / 2)
			options.location = 'topLeft';

		if (options.className)
			Tooltip.wrapper.addClass(options.className);

		if (typeof content === 'object') {
			// Content: DOM node created w/ jQuery
			Tooltip.position(node, content, options.location);

		} else if (typeof content === 'string') {
			// Content: AJAX
			if (options.ajax) {
				if (Tooltip.cache[content]) {
					Tooltip.position(node, Tooltip.cache[content], options.location);
				} else {
					$.ajax({
						type: "POST",
						url: content,
						dataType: "json",
                        data: this.getCsrfToken(),
						global: false,
						beforeSend: function() {
							// Show "Loading..." tooltip when request is being slow
							setTimeout(function() {
								if(!Tooltip.visible) {
									Tooltip.position(node, 'Загрузка', options.location);
								}
							}, 200);
						},
						success: function(data) {
							if (Tooltip.currentNode == node) {
                                data = data.content; 
								Tooltip.cache[content] = data;
								Tooltip.position(node, data, options.location);
							}
						},
						error: function(xhr) {
							if (xhr.status != 200) {
								Tooltip.hide();
							}
						}
					});
				}

			// Content: Copy content from the specified DOM node (referenced by ID)
			} else if (content.substr(0, 1) === '#') {
				Tooltip.position(node, $(content).html(), options.location);

			// Content: Text
			} else {
				Tooltip.position(node, content, options.location);
			}
		}
    },
    
    getCsrfToken : function(){
        if((this.csrfTokenName != null))
        {
            return ('&' + this.csrfTokenName + '=' + this.csrfToken);
        }
    },
    
    /**
     * Hide the tooltip.
     */
	hide: function() {
		if (!Tooltip.wrapper)
			return;

		Tooltip.wrapper.hide();
		Tooltip.wrapper.unbind('mousemove.tooltip');

		Tooltip.currentNode = null;
		Tooltip.visible = true;
	},

    /**
     * Position the tooltip at specific coodinates.
     *
     * @param node
     * @param content
	 * @param location
     */
    position: function(node, content, location) {
		if (!Tooltip.currentNode)
			return;

		if (typeof content == 'string')
	        Tooltip.contentCell.html(content);
		else
			Tooltip.contentCell.empty().append(content);

        var width = Tooltip.wrapper.outerWidth(),
			height = Tooltip.wrapper.outerHeight(),
			coords = Tooltip['_'+ location](width, height, node);

		if (coords)
			Tooltip.move(coords.x, coords.y, width, height);
    },

	/**
	 * Move the tooltip around.
	 *
	 * @param x
	 * @param y
	 * @param w
	 * @param h
	 */
	move: function(x, y, w, h) {
		Tooltip.wrapper
			.css("left", x +"px")
			.css("top",  y +"px")
			.show();

		Tooltip.visible = true;

    },

	/**
	 * Position at the mouse cursor.
	 *
	 * @param width
	 * @param height
	 * @param node
	 */
	_mouse: function(width, height, node) {
		node.unbind('mousemove.tooltip').bind('mousemove.tooltip', function(e) {
			Tooltip.move((e.pageX + 10), (e.pageY + 10), width, height);
		});
	},

	/**
	 * Position at the top left.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_topLeft: function(width, height, node) {
		var offset = node.offset(),
			x = offset.left - width,
			y = offset.top - height;

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Position at the top center.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_topCenter: function(width, height, node) {
		var offset = node.offset(),
			nodeWidth = node.outerWidth(),
			x = offset.left + ((nodeWidth / 2) - (width / 2)),
			y = offset.top - height;

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Position at the top right.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_topRight: function(width, height, node) {
		var offset = node.offset(),
			nodeWidth = node.outerWidth(),
			x = offset.left + nodeWidth,
			y = offset.top - height;

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Position at the middle left.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_middleLeft: function(width, height, node) {
		var offset = node.offset(),
			nodeHeight = node.outerHeight(),
			x = offset.left - width,
			y = (offset.top + (nodeHeight / 2)) - (height / 2);

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Position at the middle right.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_middleRight: function(width, height, node) {
		var offset = node.offset(), 
			nodeWidth = node.outerWidth(),
			nodeHeight = node.outerHeight(),
			x = offset.left + nodeWidth,
			y = (offset.top + (nodeHeight / 2)) - (height / 2);

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Position at the bottom left.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_bottomLeft: function(width, height, node) {
		var offset = node.offset(),
			nodeHeight = node.outerHeight(),
			x = offset.left - width,
			y = offset.top + nodeHeight;

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Position at the bottom center.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_bottomCenter: function(width, height, node) {
		var offset = node.offset(), 
			nodeWidth = node.outerWidth(),
			nodeHeight = node.outerHeight(),
			x = offset.left + ((nodeWidth / 2) - (width / 2)),
			y = offset.top + nodeHeight;

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Position at the bottom right.
	 *
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_bottomRight: function(width, height, node) {
		var offset = node.offset(), 
			nodeWidth = node.outerWidth(),
			nodeHeight = node.outerHeight(),
			x = offset.left + nodeWidth,
			y = offset.top + nodeHeight;

		return Tooltip._checkViewport(x, y, width, height, node);
	},

	/**
	 * Makes sure the tooltip appears within the viewport.
	 *
	 * @param x
	 * @param y
	 * @param width
	 * @param height
	 * @param node
	 * @return object
	 */
	_checkViewport: function(x, y, width, height, node) {
		var offset = node.offset();

		// Greater than x viewport
		if ((x + width) > Page.dimensions.width)
			x = (offset.left - width);

		// Less than x viewport
		else if (x < 0)
			x = 15;

		// Greater than y viewport
		if ((y + height) > (Page.scroll.top + Page.dimensions.height))
			y = y - ((y + height) - (Page.scroll.top + Page.dimensions.height));

		// Node on top of viewport scroll
		else if ((offset.top - 100) < Page.scroll.top)
			y = offset.top + node.outerHeight();

		// Less than y viewport scrolled
		else if (y < Page.scroll.top)
			y = Page.scroll.top + 15;

		// Less than y viewport
		else if (y < 0)
			y = 15;

		return {
			x: x,
			y: y
		};
	}

};
 