$(function() {
	cms.models.plugin = Backbone.Model.extend({
		defaults: {
			title: '',
			description: '',
			version: '0.0.0',
			settings: false,
			installed: false
		},

		toggleStatus: function(remove_data) {
			if(!remove_data) remove_data = false;
			this.save({installed: ! this.get("installed"), remove_data: remove_data});
		},

		clear: function() {
			this.destroy();
		}
	});
	
	cms.collections.plugins = Backbone.Collection.extend({
		url: SITE_URL + 'api-plugins',

		model: cms.models.plugin,

		parse: function(response) {
			return response.response;
		},
		
		// Filter down the list of all todo items that are finished.
		activated: function() {
			return this.filter(function(plugin){ return plugin.get('status'); });
		},

		comparator: function(a) {
			return !a.get('installed');
		}
	});

	cms.views.plugin = Backbone.View.extend({
		tagName: 'tr',

		template: _.template($('#plugin-item').html()),

		events: {
			"click .change-status": "toggleStatus"
		},

		initialize: function() {
			this.model.on('change', this.render, this);
			this.model.on('destroy', this.remove, this);
		},

		toggleStatus: function() {
			remove_data = false;
			if( this.model.get('installed') && confirm(__('Remove database data')))
				remove_data = true;
			
			this.model.toggleStatus(remove_data);
		},

		// Re-render the titles of the todo item.
		render: function() {
			this.$el.toggleClass('success', this.model.get('installed'));

			this.$el.html(this.template(this.model.toJSON()));
			
			var button = this.$el.find('button');

			if(this.model.get('installed')) {
				button.addClass('btn-danger');
				button.html('<span class="icon icon-off icon-white" />');
			} else {
				button.html('<span class="icon icon-play-circle" />');
			}

			return this;
		},

		// Remove the item, destroy the model.
		clear: function() {
			this.model.clear();
		}
	});

	cms.views.plugins = Backbone.View.extend({

		el: $("#pluginsMap tbody"),

		initialize: function() {
			var $self = this;
			this.collection = new cms.collections.plugins();
			this.collection.fetch({
				success: function () {
					$self.render();
				}
			});
		},

		render: function() {
			this.clear();

			this.collection.each(function(plugin) {
				this.addPlugin(plugin);
			}, this);
		},
		
		clear: function() {
			this.$el.empty();
		},

		addPlugin: function(plugin) {
			var view = new cms.views.plugin({model: plugin});
			this.$el.append(view.render().el);
		}
	});
	
	var AppPlugins = new cms.views.plugins();
});