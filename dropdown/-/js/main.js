// head {
var __nodeId__ = "std_ui_nav_dropdown__main";
var __nodeNs__ = "std_ui_nav_dropdown";
// }

(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, {
        options: {},

        _create: function () {
            this.bind();
        },

        _setOption: function (key, value) {
            $.Widget.prototype._setOption.apply(this, arguments);
        },

        bind: function () {
            var widget = this;

            $window = $(window);

            var toggleButton = $(widget.options.toggleButtonSelector);
            var nav = $("ul.first_level", widget.element);

            var navExpandClass = widget.options.navExpandClass || "expand";

            toggleButton.on("click", function (e) {
                e.preventDefault();

                nav.slideToggle(100, function () {
                    if (nav.is(':hidden')) {
                        //if (widget.options.sticky) {
                        //    $window.unbind("scroll." + __nodeId__ + '.' + widget.options.instance);
                        //}

                        toggleButton.removeClass(navExpandClass);
                    } else {
                        //var wh = $window.height();
                        //var navTop = nav.offset().top;
                        //var navHeight = nav.height();
                        //var scrollTopLimit = navHeight - wh + navTop;

                        //if (widget.options.sticky) {
                        //$window.bind("scroll." + __nodeId__ + '.' + widget.options.instance, function () {
                        //    if ($window.scrollTop() > scrollTopLimit) {
                        //        $window.scrollTop(scrollTopLimit);
                        //    }
                        //});
                        //}

                        toggleButton.addClass(navExpandClass);
                    }

                    onResize();

                    //$window.scroll();
                });
            });

            var ww = window.innerWidth;
            var cw = widget.options.collapseWidth;

            if (cw) {
                $window.resize(function () {
                    onResize();
                });
            }

            var collapsed = ww > cw;

            onResize();

            function onResize() {
                ww = window.innerWidth;

                if (ww > cw) {
                    onUncollapse();
                } else {
                    onCollapse();
                }
            }

            function onCollapse() {
                if (!collapsed) {
                    nav.hide();

                    if (widget.options.sticky) {
                        nav.unstick();
                        toggleButton.sticky(widget.options.sticky).sticky('update');

                        //$window.scroll();
                    }

                    collapsed = true;
                }

                $("li", widget.element).rebind("click.autohide", function () {
                    toggleButton.click();
                });
            }

            function onUncollapse() {
                if (collapsed) {
                    if (nav.is(':hidden')) {
                        nav.show();
                    }

                    if (widget.options.sticky) {
                        nav.sticky(widget.options.sticky).sticky('update');
                        toggleButton.unstick();

                        //$window.scroll();
                    }

                    //$window.unbind("scroll." + __nodeId__ + '.' + widget.options.instance);

                    collapsed = false;
                }

                $("li", widget.element).unbind("click.autohide");
            }
        }
    });
})(__nodeNs__, __nodeId__);
