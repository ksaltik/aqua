
/*
    /* ////////////////////////////////////////////////////////////////////////////////
  \\\\\\\\\\\\\\\\\\\\\\\\\  FME Mediaappearance Module  \\\\\\\\\\\\\\\\\\\\\\\\\
  /////////////////////////////////////////////////////////////////////////////////
  ///////                      * @category   FME                            ///////
  \\\\\\\                      * @package    FME_Mediaappearance              \\\\\\\
  ///////    * @developer       Ashar Riaz                                        ///////
  \\\\\\\        * @author            FME Extensions <support@fmeextensions.com>                                               \\\\\\\
  /////////////////////////////////////////////////////////////////////////////////
  \\* @copyright  Copyright 2017 © fmeextensions.com All right reserved\\\
  /////////////////////////////////////////////////////////////////////////////////
 
*/
define(
    [
    'jquery',
    'underscore',
    'uiRegistry',
    'FME_Productvideos/js/form/element/abstract',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert'
    ],
    function ($, _, uiRegistry,abstract, modal, uiAlert) {
        'use strict';




        return abstract.extend(
            {

            /**
             * On value change handler.
             *
             * @param {String} value
             */
                initialize:function (value) {
                    this._super();
          
         


                    ///return this._super();
                },
         
                onUpdate: function (value) {
          
                    
                    var notEqual = this.value() !== this.initialValue;
                    this.initialValue = value;
                    if (notEqual) {
                        var videoInfo = this._validateURL(value);

                        if (!videoInfo) {
                           
                            var field4 = uiRegistry.get('index = video_thumb');
                            field4.clear();
                        } else {
                            var   type = videoInfo.type;
                            var id = videoInfo.id;
                            var googleapisUrl;
                            var key = uiRegistry.get('index = youtube_key');
                            var y_key = key.value._latestValue;

                            if (type === 'youtube' && y_key=='') {
                                uiAlert(
                                    {
                                    content: "Add Youtube Api key in Configuration Settings( Catalog->Catalog->Productvideo) to auto insert data in fields"
                                    }
                                );
                                 // alert("Add Youtube Api key in Configuration Settings( Catalog->Catalog->Productvideo) to auto insert data in fields");
                            }

                            if (type === 'youtube' && y_key!='') {
                                googleapisUrl = 'https://www.googleapis.com/youtube/v3/videos?id=' +
                                id +
                                '&part=snippet,contentDetails,statistics,status&key='+y_key +
                                '' + '&alt=json&callback=?';
                                $.ajax(
                                    {
                                        url: googleapisUrl,
                                        dataType: 'jsonp',
                                        data: {
                                            format: 'json'
                                        },
                                        timeout: 5000,
                                        success:  function (data) {
                          
                                            if(data.pageInfo.totalResults !=0){


                                            var  tmp = data.items[0];
                            
                                            var filterPlaceholder = 'ns = ${ $.ns }, parentScope = ${ $.parentScope }'
                                            var componentFile = filterPlaceholder + ', index=title';
                                            var componentDescription = filterPlaceholder + ', index=content';
                                            //    var componentThumb = filterPlaceholder + ', index=youtube_thumb';
                                            var field1 = uiRegistry.get('index = title');
                                            var field2 = uiRegistry.get('index = content');
                                            var field3 = uiRegistry.get('index = youtube_thumb');

                                            var key = uiRegistry.get('index = youtube_key');
                        

                                            var componentThumb = filterPlaceholder + ', index=video_thumb';
                                            var field4 = uiRegistry.get('index = video_thumb');
                         
                                            field1.value(tmp.snippet.localized.title);
                                            field2.value(tmp.snippet.description);
                        
                                            var thumb = field4.value._latestValue;
                                            thumb.name = tmp.snippet.thumbnails.high.url;
                                            thumb.url = tmp.snippet.thumbnails.high.url;
                                            thumb.previewWidth = tmp.snippet.thumbnails.high.width;
                                            thumb.previewHeight = tmp.snippet.thumbnails.high.height;
                     
                         
                                            field3.value(tmp.snippet.thumbnails.high.url);
                       
                                            field4.addFile(thumb);
                                        }
                                        else{
                                            
                                            uiAlert(
                                                {
                                                content: "Video not found"
                                                }
                                            );
                                        }
                   

                                        },

                                    /**
                                     * @private
                                     */
                                        error: function () {
                                            uiAlert(
                                                {
                                                content: "Video not found"
                                                }
                                            );

                                           //  alert("Video not found");
                                            //self._onRequestError($.mage.__('Video not found'));
                                        }
                                    }
                                );
                            } else if (type === 'vimeo') {
                                $.ajax(
                                    {
                                        url: window.location.protocol + '//www.vimeo.com/api/v2/video/' + id + '.json',
                                        dataType: 'jsonp',
                                        data: {
                                            format: 'json'
                                        },
                                        timeout: 5000,
                                        success:  function (data) {
                         
                                            var  tmp = data[0];
                         
                           
                                            var filterPlaceholder = 'ns = ${ $.ns }, parentScope = ${ $.parentScope }'
                                            var componentFile = filterPlaceholder + ', index=title';
                                            var componentDescription = filterPlaceholder + ', index=content';
                                            //    var componentThumb = filterPlaceholder + ', index=youtube_thumb';
                                            var field1 = uiRegistry.get('index = title');
                                            var field2 = uiRegistry.get('index = content');
                                            var field3 = uiRegistry.get('index = youtube_thumb');

                                            var key = uiRegistry.get('index = youtube_key');
                        

                                            var componentThumb = filterPlaceholder + ', index=video_thumb';
                                            var field4 = uiRegistry.get('index = video_thumb');
                         
                                            field1.value(tmp.title);
                                            field2.value(tmp.description.replace(/(&nbsp;|<([^>]+)>)/ig, ''));
                          
                                            var thumb = field4.value._latestValue;
                                            thumb.name = tmp.thumbnail_large;
                                            thumb.url = tmp.thumbnail_large;
                                            thumb.previewWidth = tmp.width;
                                            thumb.previewHeight = tmp.height;
                     
                         
                                            field3.value(tmp.thumbnail_large);
                        
                                            field4.addFile(thumb);

                                        },

                                    /**
                                     * @private
                                     */
                                        error: function () {
                                            uiAlert(
                                                {
                                                content: "Video not found"
                                                }
                                            );
                                            //self._onRequestError($.mage.__('Video not found'));
                                        }
                                    }
                                );
                            }
                        }
                    }

            
                   // return !this.visible() ? false : notEqual;
                },
                _parseHref: function (href) {
                      var a = document.createElement('a');

                      a.href = href;

                      return a;
                },
                _validateURL: function (href, forceVideo) {
                    var id,
                    type,
                    ampersandPosition,
                    vimeoRegex;

                    if (typeof href !== 'string') {
                        return href;
                    }

                    href = this._parseHref(href);

                    if (href.host.match(/youtube\.com/) && href.search) {
                        id = href.search.split('v=')[1];

                        if (id) {
                            ampersandPosition = id.indexOf('&');
                            type = 'youtube';
                        }

                        if (id && ampersandPosition !== -1) {
                            id = id.substring(0, ampersandPosition);
                        }
                    } else if (href.host.match(/youtube\.com|youtu\.be/)) {
                        id = href.pathname.replace(/^\/(embed\/|v\/)?/, '').replace(/\/.*/, '');
                        type = 'youtube';
                    } else if (href.host.match(/vimeo\.com/)) {
                        type = 'vimeo';
                        vimeoRegex = new RegExp(
                            ['https?:\\/\\/(?:www\\.|player\\.)?vimeo.com\\/(?:channels\\/(?:\\w+\\/)',
                            '?|groups\\/([^\\/]*)\\/videos\\/|album\\/(\\d+)\\/video\\/|video\\/|)(\\d+)(?:$|\\/|\\?)'
                            ].join('')
                        );

                        if (href.href.match(vimeoRegex) != null) {
                            id = href.href.match(vimeoRegex)[3];
                        }
                    }

                    if ((!id || !type) && forceVideo) {
                        id = href.href;
                        type = 'custom';
                    }

                    return id ? {
                        id: id, type: type, s: href.search.replace(/^\?/, '')
                    } : false;
                }
        
            }
        );
   
    }
);
