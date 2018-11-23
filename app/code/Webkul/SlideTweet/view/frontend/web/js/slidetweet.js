define([
    'jquery',
     'Magento_Ui/js/modal/confirm',
     ], function ($j,$ui) {
    'use strict';
    var globalThis;
$j.widget('slidetweet.slidetweet', {
        _create: function () {
              globalThis = this;
              var timeoutdelay=globalThis.options.timeoutdelay
               var value=timeoutdelay/3;
               var suppliment = globalThis.options.rand;
               $j(document).ready(function () {
                  function animate(data)
                  {
                      $j('#'+suppliment+' .wk_tweet_main:first').fadeTo(timeoutdelay,0, function () {
                            $j('#'+suppliment+' .wk_tweet_main:first').slideUp(value, function () {
                                $j('#'+suppliment+' .wk_tweet_main:first')
                                .insertAfter('#'+suppliment+' .wk_tweet_main:last')
                                .css("opacity",1)
                                .fadeIn(function () {
setTimeout(animate, parseInt(timeoutdelay)+500);});
                              });
                            });
                          }
                        setTimeout(animate, parseInt(timeoutdelay)+500);
                  });
                }
              });
    return $j.slidetweet.slidetweet;
});
