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

Nadlani.define('crm:views/account/fields/shipping-address', 'views/fields/address', function (Dep) {

    return Dep.extend({

        copyFrom: 'billingAddress',

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.mode == 'edit') {
                var label = this.translate('Copy Billing', 'labels', 'Account');
                $btn = $('<button class="btn btn-default btn-sm">' + label + '</button>').on('click', function () {
                    this.copy(this.copyFrom);
                }.bind(this));
                this.$el.append($btn);
            }
        },

        copy: function (fieldFrom) {
            var attrList = Object.keys(this.getMetadata().get('fields.address.fields')).forEach(function (attr) {
                destField = this.name + Nadlani.Utils.upperCaseFirst(attr);
                sourceField = fieldFrom + Nadlani.Utils.upperCaseFirst(attr);

                this.model.set(destField, this.model.get(sourceField));
            }, this);

        },

    });
});

