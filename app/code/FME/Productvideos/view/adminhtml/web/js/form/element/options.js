/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
    'Magento_Ui/js/form/element/select',
    'uiRegistry'
    ],
    function (Select, registry) {
        'use strict';

        return Select.extend(
            {
                defaults: {
                    listens: {
                        value: 'changeTypeUpload'
                    },
                    typeUrl: 'video_url',
                    typeFile: 'video_file',
                    filterPlaceholder: 'ns = ${ $.ns }, parentScope = ${ $.parentScope }'
                },

            /**
             * Initialize component.
             * @returns {Element}
             */
                initialize: function () {
                    return this
                    ._super()
                    .changeTypeUpload(this.initialValue);
                },

            /**
             * Callback that fires when 'value' property is updated.
             *
             * @param {String} currentValue
             * @returns {*}
             */
                onUpdate: function (currentValue) {
                    this.changeTypeUpload(currentValue);

                    return this._super();
                },

            /**
             * Change visibility for typeUrl/typeFile based on current value.
             *
             * @param {String} currentValue
             */
                changeTypeUpload: function (currentValue) {
                    var componentFile = this.filterPlaceholder + ', index=video_file',
                    componentUrl = this.filterPlaceholder + ', index=video_url';
              
                    switch (currentValue) {
                        case 'file':
                            this.changeVisible(componentFile, true);
                            this.changeVisible(componentUrl, false);
                            break;

                        case 'url':
                            this.changeVisible(componentFile, false);
                            this.changeVisible(componentUrl, true);
                            break;
                    }
                },

            /**
             * Change visible
             *
             * @param {String} filter
             * @param {Boolean} visible
             */
                changeVisible: function (filter, visible) {
                    registry.async(filter)(
                        function (currentComponent) {
                        currentComponent.visible(visible);
                        }
                    );
                }
            }
        );
    }
);
