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

Nadlani.define('views/dashlets/stream', 'views/dashlets/abstract/base', function (Dep) {

    return Dep.extend({

        name: 'Stream',

        _template: '<div class="list-container">{{{list}}}</div>',

        actionRefresh: function () {
            this.getView('list').showNewRecords();
        },

        afterRender: function () {
            this.getCollectionFactory().create('Note', function (collection) {
                this.collection = collection;

                collection.url = 'Stream';
                collection.maxSize = this.getOption('displayRecords');

                this.listenToOnce(collection, 'sync', function () {
                    this.createView('list', 'views/stream/record/list', {
                        el: this.getSelector() + ' > .list-container',
                        collection: collection,
                        isUserStream: true,
                        noEdit: false,
                    }, function (view) {
                        view.render();
                    });
                }.bind(this));
                collection.fetch();

            }, this);
        },

        setupActionList: function () {
            this.actionList.unshift({
                name: 'viewList',
                html: this.translate('View List'),
                iconHtml: '<span class="fas fa-align-justify"></span>',
                url: '#Stream'
            });
            this.actionList.unshift({
                name: 'create',
                html: this.translate('Create Post', 'labels'),
                iconHtml: '<span class="fas fa-plus"></span>'
            });
        },

        actionCreate: function () {
            this.createView('dialog', 'views/stream/modals/create-post', {}, function (view) {
                view.render();
                this.listenToOnce(view, 'after:save', function () {
                    view.close();
                    this.actionRefresh();
                }, this);
            }, this)
        },

        actionViewList: function () {
            this.getRouter().navigate('#Stream', {trigger: true});
        }

    });
});


