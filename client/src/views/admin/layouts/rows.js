/************************************************************************
 * This file is part of NadlaniCrm.
 *
 * NadlaniCrm - Open Source CRM application.
 * Copyright (C) 2014-2018 Pablo Rotem
 * Website: https://www.facebook.com/sites4u2
 *
 * NadlaniCrm is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NadlaniCrm is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NadlaniCrm. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "NadlaniCrm" word.
 ************************************************************************/

Nadlani.define('views/admin/layouts/rows', 'views/admin/layouts/base', function (Dep) {

    return Dep.extend({

        template: 'admin/layouts/rows',

        events: _.extend({
            'click a[data-action="editItem"]': function (e) {
                this.editRow($(e.target).closest('li').data('name'));
            },
        }, Dep.prototype.events),

        dataAttributeList: null,

        dataAttributesDefs: {},

        editable: false,

        data: function () {
            return {
                scope: this.scope,
                type: this.type,
                buttonList: this.buttonList,
                enabledFields: this.enabledFields,
                disabledFields: this.disabledFields,
                layout: this.rowLayout,
                dataAttributeList: this.dataAttributeList,
                dataAttributesDefs: this.dataAttributesDefs,
                editable: this.editable,
            };
        },

        setup: function () {
            this.itemsData = {};
            Dep.prototype.setup.call(this);

            this.on('update-item', function (name, attributes) {
                console.log(name, attributes);
                this.itemsData[name] = Nadlani.Utils.cloneDeep(attributes);
            }, this);
        },

        editRow: function (name) {
            var attributes = Nadlani.Utils.cloneDeep(this.itemsData[name] || {});
            attributes.name = name;
            this.openEditDialog(attributes)
        },

        afterRender: function () {
            $('#layout ul.enabled, #layout ul.disabled').sortable({
                connectWith: '#layout ul.connected'
            });
        },

        fetch: function () {
            var layout = [];
            $("#layout ul.enabled > li").each(function (i, el) {
                var o = {};

                var name = $(el).data('name');

                var attributes = this.itemsData[name] || {};
                attributes.name = name;

                this.dataAttributeList.forEach(function (attribute) {
                    var value = attributes[attribute] || null;
                    if (value) {
                        o[attribute] = value;
                    }
                }, this);

                layout.push(o);
            }.bind(this));


            return layout;
        },

        validate: function (layout) {
            if (layout.length == 0) {
                this.notify('Layout cannot be empty', 'error');
                return false;
            }
            return true;
        }
    });
});

