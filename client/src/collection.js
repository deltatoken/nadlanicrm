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

Nadlani.define('collection', [], function () {

    var Collection = Backbone.Collection.extend({

        name: null,

        total: 0,

        offset: 0,

        maxSize: 20,

        order: null,

        orderBy: null,

        where: null,

        whereAdditional: null,

        lengthCorrection: 0,

        _user: null,

        initialize: function (models, options) {
            options = options || {};
            this.name = options.name || this.name;
            this.urlRoot = this.urlRoot || this.name;
            this.url = this.url || this.urlRoot;

            this.orderBy = this.sortBy = options.orderBy || options.sortBy || this.orderBy || this.sortBy;
            this.order = options.order || this.order;

            this.defaultOrder = this.order;
            this.defaultOrderBy = this.orderBy;

            this.data = {};

            Backbone.Collection.prototype.initialize.call(this);
        },

        _onModelEvent: function(event, model, collection, options) {
            if (event === 'sync' && collection !== this) return;
            Backbone.Collection.prototype._onModelEvent.apply(this, arguments);
        },

        reset: function (models, options) {
            this.lengthCorrection = 0;
            Backbone.Collection.prototype.reset.call(this, models, options);
        },

        sort: function (orderBy, order) {
            this.orderBy = orderBy;

            if (order === true) {
                order = 'desc';
            } else if (order === false) {
                order = 'asc';
            }
            this.order = order || 'asc';

            if (typeof this.asc !== 'undefined') { // TODO remove in 5.7
                this.asc = this.order === 'asc';
                this.sortBy = orderBy;
            }

            this.fetch();
        },

        nextPage: function () {
            var offset = this.offset + this.maxSize;
            this.setOffset(offset);
        },

        previousPage: function () {
            var offset = this.offset - this.maxSize;
            this.setOffset(offset);
        },

        firstPage: function () {
            this.setOffset(0);
        },

        lastPage: function () {
            var offset = this.total - this.total % this.maxSize;
            this.setOffset(offset);
        },

        setOffset: function (offset) {
            if (offset < 0) {
                throw new RangeError('offset can not be less than 0');
            }
            if (offset > this.total) {
                throw new RangeError('offset can not be larger than total count');
            }
            this.offset = offset;
            this.fetch();
        },

        parse: function (response) {
            this.total = response.total;

            if ('additionalData' in response) {
                this.dataAdditional = response.additionalData;
            } else {
                this.dataAdditional = null;
            }

            return response.list;
        },

        fetch: function (options) {
            var options = options || {};
            options.data = _.extend(options.data || {}, this.data);

            this.offset = options.offset || this.offset;
            this.orderBy = options.orderBy || options.sortBy || this.orderBy;
            this.order = options.order || this.order;

            this.where = options.where || this.where;

            if (!('maxSize' in options)) {
                options.data.maxSize = options.more ? this.maxSize : ((this.length > this.maxSize) ? this.length : this.maxSize);
            } else {
                options.data.maxSize = options.maxSize;
            }

            options.data.offset = options.more ? this.length + this.lengthCorrection : this.offset;
            options.data.orderBy = this.orderBy;
            options.data.order = this.order;
            options.data.where = this.getWhere();

            if (typeof this.asc !== 'undefined') { // TODO remove in 5.7
                options.data.asc = this.asc;
                options.data.sortBy = this.sortBy;

                delete options.data.orderBy;
                delete options.data.order;
            }

            this.lastXhr = Backbone.Collection.prototype.fetch.call(this, options);

            return this.lastXhr;
        },

        abortLastFetch: function () {
            if (this.lastXhr && this.lastXhr.readyState < 4) {
                this.lastXhr.abort();
            }
        },

        getWhere: function () {
            return (this.where || []).concat(this.whereAdditional || []);
        },

        getUser: function () {
            return this._user;
        },

        getEntityType: function () {
            return this.name;
        }

    });

    return Collection;

});
