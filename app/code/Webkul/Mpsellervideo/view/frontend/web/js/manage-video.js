/**
 * Webkul Software
 *
 * @category  Webkul
 * @package   Webkul_Mpsellervideo
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
/*jshint jquery:true*/
define(
    [
    "jquery",
    'sellerProfileJsVideo',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    "jquery/ui"
    ],
    function ($, $t, alert) {
        'use strict';
        $.widget(
            'mage.manageVideo',
            {
                options: {
                    bannerId: '#banner-slide',
                    videoWrapper:'.bjqs-wrapper',
                    previousVideo:'.bjqs-prev',
                    nextVideo:'.bjqs-next',
                },
                _create: function () {
                    var self = this;
                    var tempWidth = self.options.videoWidth;
                    var tempHeight = self.options.videoHeight;

                    /*-----------to slide a video through bjqs jquery-----------*/

                    if (tempWidth > 700) {
                        tempWidth = 700;
                    }
                    if (tempWidth > $('body').find('.column.main').outerWidth()) {
                        tempWidth = $('body').find('.column.main').outerWidth();
                    }

                    $(self.options.bannerId).bjqs(
                        {
                            animtype      : 'slide',
                            height        : tempHeight,
                            width         : tempWidth,
                            animduration  : self.options.animduration,
                            animspeed     : self.options.animspeed,
                            responsive    : true,
                            randomstart   : false
                        }
                    );
                    $('body '+self.options.videoWrapper).mouseover(
                        function (event) {
                            $('body '+self.options.previousVideo).show();
                            $('body '+self.options.nextVideo).show();
                        }
                    );
                    $('body '+self.options.previousVideo).mouseover(
                        function (event) {
                            $('body '+self.options.previousVideo).show();
                            $('body '+self.options.nextVideo).show();
                        }
                    );
                    $('body '+self.options.nextVideo).mouseover(
                        function (event) {
                            $('body '+self.options.previousVideo).show();
                            $('body '+self.options.nextVideo).show();
                        }
                    );
                    $('body '+self.options.videoWrapper).mouseout(
                        function (event) {
                            $('body '+self.options.previousVideo).hide();
                            $('body '+self.options.nextVideo).hide();
                        }
                    );
                    $('body '+self.options.previousVideo).mouseout(
                        function (event) {
                            $('body '+self.options.previousVideo).hide();
                            $('body '+self.options.nextVideo).hide();
                        }
                    );
                    $('body '+self.options.nextVideo).mouseout(
                        function (event) {
                            $('body '+self.options.previousVideo).hide();
                            $('body '+self.options.nextVideo).hide();
                        }
                    );
                },
            }
        );
        return $.mage.manageVideo;
    }
);
